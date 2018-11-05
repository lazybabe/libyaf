<?php
namespace Libyaf\Queue\Driver;

use RdKafka\Conf;
use RdKafka\TopicConf;
use RdKafka\Producer;
use RdKafka\KafkaConsumer;

class Rdkafka extends AbstractDriver implements DriverInterface
{
    private $producerInstance;

    private $consumerInstance;

    private $config;

    public function __construct($config)
    {
        $config = $config->toArray();

        $this->config = $config;

        $this->config['topics'] = explode(',', $this->config['topics']);
    }

    public function push($name, $message)
    {
        $topic = $this->producerInstance()->newTopic($name);

        $topic->produce(\RD_KAFKA_PARTITION_UA, 0, $message);
        $this->producerInstance()->poll(0);

        $this->logger->info('[produce] '.json_encode(['topic'=>$name, 'data'=>$message]));

        return true;
    }

    public function pull($name, callable $callback, $timeout = null)
    {
        $timeout = intval($timeout);

        if ($timeout) {
            $timeout = $timeout * 1000;
        } else {
            $timeout = 60e3;
        }

        try {
            $message = $this->consumerInstance()->consume($timeout);

            switch ($message->err) {
            case \RD_KAFKA_RESP_ERR_NO_ERROR:
                $data = $message->payload;

                call_user_func($callback, $data);

                break;
            case \RD_KAFKA_RESP_ERR__PARTITION_EOF:
            case \RD_KAFKA_RESP_ERR__TIMED_OUT:
                return false;
            default:
                $this->logger->error('rdkafka consume error.', json_decode(json_encode($message), true));
                return false;
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());

            return false;
        }

        $this->consumerInstance()->commit();
        $this->logger->info('[consume] success', ['data'=>$data]);

        return true;
    }

    public function delay($name, $message, $seconds)
    {
        return false;
    }

    public function size($name)
    {
        return false;
    }

    private function producerInstance()
    {
        if (isset($this->producerInstance)) {
            return $this->producerInstance;
        }

        pcntl_sigprocmask(SIG_BLOCK, [SIGIO]);

        $conf = new Conf();
        $conf->set('internal.termination.signal', SIGIO);
        $conf->set('socket.blocking.max.ms', 1);

        $this->producerInstance = new Producer($conf);
        $this->producerInstance->addBrokers($this->config['brokers']);

        return $this->producerInstance;
    }

    private function consumerInstance()
    {
        if (isset($this->consumerInstance)) {
            return $this->consumerInstance;
        }

        $topicConf = new TopicConf();
        if ($this->config['offset_reset'] == 'earliest') {
            $topicConf->set('auto.offset.reset', 'earliest');
        }

        $conf = new Conf();
        $conf->set('metadata.broker.list', $this->config['brokers']);
        $conf->set('group.id', $this->config['group']);
        $conf->set('enable.auto.commit', 'false');
        $conf->setDefaultTopicConf($topicConf);

        $this->consumerInstance = new KafkaConsumer($conf);
        $this->consumerInstance->subscribe($this->config['topics']);

        return $this->consumerInstance;
    }

}

