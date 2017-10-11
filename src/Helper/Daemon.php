<?php
namespace Libyaf\Helper;

use Libyaf\Logkit\Logger;

class Daemon
{
    const STATUS_STARTING = 1;

    const STATUS_RUNNING = 2;

    const STATUS_SHUTDOWN = 4;

    const OPERATIONS = ['start', 'stop', 'restart', 'kill'];

    private $config = [];

    private $logger = null;

    private $job = null;

    private $workerId = 0;

    private $count = 1;

    private $user = '';

    private $group = '';

    private $daemonize = false;

    private $stdoutFile = '/dev/null';

    private $pidFile = '';

    private $masterPid = 0;

    private $workers = [];

    private $pidMap = [];

    private $status = self::STATUS_STARTING;

    private $startFile = '';

    public function __construct(array $config = [])
    {
        $this->config   = $config;

        $this->logger   = Logger::ins('_daemon');

        $this->workerId = spl_object_hash($this);

        $this->workers[$this->workerId] = $this;

        $this->pidMap[$this->workerId]  = [];
    }

    public function setJob(callable $job)
    {
        $this->job = $job;
    }

    public function run()
    {
        $this->checkSapiEnv();
        $this->init();
        $this->parseCommand();
        $this->daemonize();
        $this->initWorkers();
        $this->installSignal();
        $this->saveMasterPid();
        $this->forkWorkers();
        $this->resetStd();
        $this->monitorWorkers();
    }

    private function checkSapiEnv()
    {
        if (php_sapi_name() != 'cli') {
            exit("only run in command line mode \n");
        }
    }

    private function init()
    {
        $this->startFile  = $this->getStartFile();

        if (isset($this->config['pid'])) {
            $this->pidFile = $this->config['pid'];
        } else {
            $this->pidFile  = '/var/run/'.str_replace('/', '_', self::$_startFile).'.pid';
        }

        if (isset($this->config['worker'])) {
            if ($count = intval($this->config['worker'])) {
                $this->count = $count;
            }
        }

        if (isset($this->config['user'])) {
            $this->user = $this->config['user'];
        }

        if (isset($this->config['group'])) {
            $this->group = $this->config['group'];
        }

        $this->status = self::STATUS_STARTING;

        $this->setProcessTitle('YAF Daemon: master process  start_file=' . $this->startFile);
    }

    private function getStartFile()
    {
        global $argv;

        $fileName = $argv[0];

        foreach ($argv as $arg) {
            list($name, $value) = explode('=', $arg, 2);
            if ($name == 'request_uri') {
                $fileName .= $value;
            }
        }

        return $fileName;
    }

    private function parseCommand()
    {
        global $argv;

        if (! $command = array_intersect($argv, self::OPERATIONS)) {
            exit("Usage: php yourfile.php {start|stop|restart|kill}\n");
        }

        $command    = trim(array_shift($command));
        $command2   = in_array('-d', $argv) ? '-d' : '';

        $mode = '';
        if ($command === 'start') {
            if ($command2 === '-d') {
                $mode = 'in DAEMON mode';
            } else {
                $mode = 'in DEBUG mode';
            }
        }
        $this->log("YAF Daemon [{$this->startFile}] $command $mode");

        $masterPid      = @file_get_contents($this->pidFile);
        $masterIsAlive  = $masterPid && @posix_kill($masterPid, 0);
        if ($masterIsAlive) {
            if ($command === 'start') {
                self::log("YAF Daemon [{$this->startFile}] already running");
                exit;
            }
        } elseif ($command !== 'start' && $command !== 'restart') {
            $this->log("YAF Daemon [{$this->startFile}] not run");
        }

        switch ($command) {
            case 'kill':
                exec("ps aux | grep {$this->startFile} | grep -v grep | awk '{print $2}' |xargs kill -SIGINT");
                exec("ps aux | grep {$this->startFile} | grep -v grep | awk '{print $2}' |xargs kill -SIGKILL");
                break;
            case 'start':
                if ($command2 === '-d') {
                    $this->daemonize = true;
                }
                break;
            case 'restart':
            case 'stop':
                $this->log("YAF Daemon [{$this->startFile}] is stoping ...");
                $masterPid && posix_kill($masterPid, SIGINT);
                $timeout    = 5;
                $startTime  = time();
                while (1) {
                    $masterIsAlive = $masterPid && posix_kill($masterPid, 0);
                    if ($masterIsAlive) {
                        if (time() - $startTime >= $timeout) {
                            $this->log("YAF Daemon [{$this->startFile}] stop fail");
                            exit;
                        }
                        usleep(10000);
                        continue;
                    }
                    $this->log("YAF Daemon [{$this->startFile}] stop success");
                    if ($command === 'stop') {
                        exit;
                    }
                    if ($command2 === '-d') {
                        $this->daemonize = true;
                    }
                    break;
                }
                break;
            default :
                exit("Usage: php yourfile.php {start|stop|restart|kill}\n");
        }
    }

    private function daemonize()
    {
        if (! $this->daemonize) {
            return;
        }

        umask(0);

        $pid = pcntl_fork();
        if (-1 === $pid) {
            throw new \Exception('fork fail');
        } elseif ($pid > 0) {
            exit;
        }

        if (-1 === posix_setsid()) {
            throw new \Exception("setsid fail");
        }
    }

    private function initWorkers()
    {
        foreach ($this->workers as $worker) {
            if (empty($worker->user)) {
                $worker->user = $this->getCurrentUser();
            } else {
                if (posix_getuid() !== 0 && $worker->user != $this->getCurrentUser()) {
                    $this->log('You must have the root privileges to change uid and gid.', 'warn');
                }
            }
        }
    }

    private function installSignal()
    {
        // stop
        pcntl_signal(SIGINT, array($this, 'signalHandler'), false);
    }

    private function saveMasterPid()
    {
        $this->masterPid = posix_getpid();

        $dir = dirname($this->pidFile);

        if (! is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        if (false === @file_put_contents($this->pidFile, $this->masterPid)) {
            throw new \Exception('can not save pid to ' . $this->pidFile);
        }
    }

    private function forkWorkers()
    {
        foreach ($this->workers as $worker) {
            while (count($this->pidMap[$worker->workerId]) < $worker->count) {
                $this->forkOneWorker($worker);
            }
        }
    }

    private function monitorWorkers()
    {
        $this->status = self::STATUS_RUNNING;

        while (1) {
            pcntl_signal_dispatch();

            $status = 0;
            $pid    = pcntl_wait($status, WUNTRACED);

            pcntl_signal_dispatch();

            if ($pid > 0) {
                foreach ($this->pidMap as $workerId => $workerPidArray) {
                    if (isset($workerPidArray[$pid])) {
                        $worker = $this->workers[$workerId];
                        if ($status !== 0) {
                            $this->log("worker[$pid] exit with status $status");
                        }

                        unset($this->pidMap[$workerId][$pid]);

                        break;
                    }
                }
                if ($this->status !== self::STATUS_SHUTDOWN) {
                    $this->forkWorkers();
                } else {
                    if (! $this->getAllWorkerPids()) {
                        $this->exitAndClearAll();
                    }
                }
            } else {
                if ($this->status === self::STATUS_SHUTDOWN && ! $this->getAllWorkerPids()) {
                    $this->exitAndClearAll();
                }
            }
        }
    }

    public function signalHandler($signal)
    {
        switch ($signal) {
            // Stop.
            case SIGINT:
                $this->stopAll();
                break;
        }
    }

    private function stopAll()
    {
        $this->status = self::STATUS_SHUTDOWN;

        if ($this->masterPid === posix_getpid()) {
            $this->log("YAF Daemon [{$this->startFile}] Stopping ...");
            $workerPidArray = $this->getAllWorkerPids();
            foreach ($workerPidArray as $workerPid) {
                $status = posix_kill($workerPid, SIGINT);
            }
        } else {
            exit;
        }
    }

    private function getAllWorkerPids()
    {
        $pidArray = array();
        foreach ($this->pidMap as $workerPidArray) {
            foreach ($workerPidArray as $workerPid) {
                $pidArray[$workerPid] = $workerPid;
            }
        }
        return $pidArray;
    }

    private function exitAndClearAll()
    {
        @unlink($this->pidFile);
        $this->log("YAF Daemon[{$this->startFile}] has been stopped");
        exit(0);
    }

    private function getCurrentUser()
    {
        $userInfo = posix_getpwuid(posix_getuid());
        return $userInfo['name'];
    }

    private function forkOneWorker($worker)
    {
        $pid = pcntl_fork();

        if ($pid > 0) {
            $this->pidMap[$worker->workerId][$pid] = $pid;
        } elseif (0 === $pid) {
            if ($this->status === self::STATUS_STARTING) {
                $this->resetStd();
            }

            $this->pidMap  = [];
            $this->workers = [$worker->workerId => $worker];

            $this->setProcessTitle('YAF Daemon: worker process');

            $worker->setUserAndGroup();

            $worker->runWorker();
        } else {
            throw new \Exception("forkOneWorker fail");
        }
    }

    private function runWorker()
    {
        $this->status = self::STATUS_RUNNING;

        register_shutdown_function(array($this, 'checkErrors'));

        if ($this->job) {
            try {
                call_user_func($this->job, $this);
            } catch (\Exception $e) {
                echo $e;
                exit(250);
            }
        } else {
            $this->log("Job not exsits", 'warn');
        }
    }

    public function loop($count = 0)
    {
        if ($this->job) {
            $job    = $this->job;
            $count  = intval($count);

            $this->job = function($worker) use ($job, $count) {
                $i = 1;
                while (1) {
                    if ($count) {
                        if ($i > $count) {
                            $this->stopAll();
                        }
                        $i++;
                    }

                    pcntl_signal_dispatch();
                    call_user_func($job, $worker);
                }
            };
        }
    }

    public function checkErrors()
    {
        if (self::STATUS_SHUTDOWN != $this->status) {
            $errorMsg = "WORKER EXIT UNEXPECTED ";
            $errors    = error_get_last();
            if ($errors && ($errors['type'] === E_ERROR ||
                $errors['type'] === E_PARSE ||
                $errors['type'] === E_CORE_ERROR ||
                $errors['type'] === E_COMPILE_ERROR ||
                $errors['type'] === E_RECOVERABLE_ERROR)
            ) {
                $errorMsg .= self::getErrorType($errors['type']) . " {$errors['message']} in {$errors['file']} on line {$errors['line']}";
            }
            $this->log($error_msg, 'error');
        }
    }

    private function setUserAndGroup()
    {
        $userInfo = posix_getpwnam($this->user);
        if (! $userInfo) {
            $this->log("User {$this->user} not exsits", 'warn');
            return false;
        }
        $uid = $userInfo['uid'];

        if ($this->group) {
            $groupInfo = posix_getgrnam($this->group);
            if (!$groupInfo) {
                $this->log("Group {$this->group} not exsits", 'warn');
                return false;
            }
            $gid = $groupInfo['gid'];
        } else {
            $gid = $userInfo['gid'];
        }

        if ($uid != posix_getuid() || $gid != posix_getgid()) {
            if (! posix_setgid($gid) || ! posix_initgroups($userInfo['name'], $gid) || ! posix_setuid($uid)) {
                $this->log('change gid or uid fail.', 'warn');
            }
        }
    }

    private function resetStd()
    {
        if (! $this->daemonize) {
            return;
        }

        global $STDOUT, $STDERR;

        $handle = fopen($this->stdoutFile, "a");

        if ($handle) {
            unset($handle);
            @fclose(STDOUT);
            @fclose(STDERR);
            $STDOUT = fopen($this->stdoutFile, "a");
            $STDERR = fopen($this->stdoutFile, "a");
        } else {
            throw new \Exception('can not open stdoutFile '.$this->stdoutFile);
        }
    }

    private function setProcessTitle($title)
    {
        if (function_exists('cli_set_process_title')) {
            @cli_set_process_title($title);
        } elseif (extension_loaded('proctitle') && function_exists('setproctitle')) {
            @setproctitle($title);
        }
    }

    private function log($msg, $level = 'info')
    {
        $msg = $msg . "\n";

        if (! $this->daemonize) {
            switch ($level) {
            case 'warn':
                echo "\033[33m$msg\033[0m";
                break;
            case 'error':
                echo "\031[33m$msg\033[0m";
                break;
            default:
                echo $msg;
                break;
            }
        }

        switch ($level) {
        case 'warn':
            $this->logger->warn($msg);
            break;
        case 'error':
            $this->logger->error($msg);
            break;
        default:
            $this->logger->info($msg);
            break;
        }
    }
}

