<?php
namespace Libyaf\Queue\Driver;

use Pheanstalk\Pheanstalk;

class Beanstalk extends AbstractDriver implements DriverInterface
{
    const EXCEPTION_DELAY = 3;

    private $instance;

    public function __construct($config)
    {
        $this->instance = new Pheanstalk($config->host, $config->port, $config->timeout, $config->persistent);
    }

    public function push($name, $message)
    {
        $id = $this->instance->useTube($name)->put($message);

        $this->logger->info('[put] '.json_encode(['id'=>$id, 'data'=>$message]));

        return $id;
    }

    public function pull($name, callable $callback, $timeout = null)
    {
        $job    = $this->instance->watch($name)->reserve($timeout);

        if ($timeout !== null && $job === false) {
            return true;
        }

        $data   = $job->getData();

        try {
            call_user_func($callback, $data);
        } catch (\Exception $e) {
            $this->instance
                ->release($job, Pheanstalk::DEFAULT_PRIORITY, self::EXCEPTION_DELAY);

            $this->logger->info('[release] '.serialize($job).' [delay] '.self::EXCEPTION_DELAY.'(s)');

            return false;
        }

        $this->instance->delete($job);

        $this->logger->info('[delete] '.json_encode(['id'=>$job->getId(), 'data'=>$data]));

        return true;
    }

    public function delay($name, $message, $seconds)
    {
        $id = $this->instance->useTube($name)->put($message, Pheanstalk::DEFAULT_PRIORITY, $seconds);

        $this->logger->info('[put] '.json_encode(['id'=>$id, 'data'=>$message])." [delay] $seconds(s)");

        return $id;
    }

    public function size($name)
    {
        return $this->instance->statsTube($name)->current_jobs_ready;
    }

}

