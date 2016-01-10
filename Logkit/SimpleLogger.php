<?php
namespace Logkit;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;
use Logkit\Conf;
use Logkit\Exception;

class SimpleLogger extends AbstractLogger
{
    private static $ins = [];

    private $conf;

    private $fileHandle;

    private $processid;

    private $datetime;

    private $logLevels = [
        LogLevel::EMERGENCY => 0,
        LogLevel::ALERT     => 1,
        LogLevel::CRITICAL  => 2,
        LogLevel::ERROR     => 3,
        LogLevel::WARNING   => 4,
        LogLevel::NOTICE    => 5,
        LogLevel::INFO      => 6,
        LogLevel::DEBUG     => 7
    ];

    public static function ins($space = 'default')
    {
        //获取项目名
        $projName = \Yaf\Application::app()->getConfig()->projName;
        $projName = isset($projName) ? strtolower($projName) : 'default';

        if (isset(self::$ins[$projName])) {
            self::$ins[$projName]->conf->space = $space;
        	return self::$ins[$projName];
        }

        //获取日志配置
        $config = \Yaf\Application::app()->getConfig()->log;

        if (! isset($config)) {
            throw new Exception('must config the logger first.');
        }

        $conf = new Conf();
        $conf->filepath     = $config->filepath;
        $conf->level        = $config->level;
        $conf->name         = $projName;
        $conf->space        = $space;

        self::$ins[$projName] = new self($conf);

        return self::$ins[$projName];
    }

    public function __construct(Conf $conf = null)
    {
        if (is_file($conf->filepath) && ! is_writable($conf->filepath)) {
            error_log('[YafLogKit] [$conf->name] The file is not writeable.');
            return false;
        }

        $this->fileHandle = fopen($conf->filepath, 'a');
        if (! $this->fileHandle) {
            error_log('[YafLogKit] [$conf->name] The file could not be opened.');
        	return false;
        }

        $this->conf         = $conf;
        $this->processid    = getmypid();
        $this->datetime     = date('Y-m-d H:i:s');
    }

    public function __destruct()
    {
        if ($this->fileHandle) {
            fclose($this->fileHandle);
        }
    }

    public function log($level, $message, array $context = [])
    {
        if ($this->logLevels[$this->conf->level] < $this->logLevels[$level]) {
        	return false;
        }

        $message = $this->formatMessage($level, $message, $context);

        return $this->write($message);
    }

    private function formatMessage($level, $message, $context)
    {
        $defaultContext = [
            'level'     => strtolower($level),
            'space'     => "{$this->conf->name}/{$this->conf->space}",
            'processid' => $this->processid,
            'datetime'  => $this->datetime,
        ];

        $message = "{datetime} {space}[{processid}] [{level}] ".$message;
        $context = $defaultContext + $context;

        $message = $this->replace($message, $context);
        $message = str_replace("\n", ' ', $message);
        $message = $message.PHP_EOL;

        return $message;
    }

    private function write($message)
    {
        if (fwrite($this->fileHandle, $message) === false) {
            error_log('[YafLogKit] [$conf->name] The file could not be written to.');
            return false;
        }
        return true;
    }

    private function replace($message, $context)
    {
        $replace = [];

        foreach ($context as $key => $item) {
        	$replace['{'.$key.'}'] = $item;
        }

        return strtr($message, $replace);
    }
}

