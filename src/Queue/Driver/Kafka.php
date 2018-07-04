<?php
namespace Libyaf\Queue\Driver;

use Kafka\ProducerConfig;
use Kafka\Producer;
use Kafka\ConsumerConfig;
use Kafka\Consumer;

class Kafka extends AbstractDriver implements DriverInterface
{
    private $producerInstance;

    private $consumerInstance;

    private $config;

    public function __construct($config)
    {
        $config = $config->toArray();

        $this->config = $config;

        $this->config['topics'] = explode(',', $this->config['topics']);

        if (! $this->config['version']) {
            $this->config['version'] = '1.0.0';
        }
    }

    public function push($name, $message)
    {
        $data = [[
            'topic' => $name,
            'value' => $message,
            'key' => '',
        ]];

        $result = $this->producerInstance()->send($data);

        $code = $result[0]['data']['0']['partitions']['0']['errorCode'] ?? false;

        if ($code !== 0) {
            return false;
        }

        $this->logger->info('[produce] '.json_encode(['topic'=>$name, 'data'=>$message]));

        return true;
    }

    public function pull($name, callable $callback)
    {
        return $this->consumerInstance()->start(function ($topic, $partition, $message) use ($callback) {
            try {
                $data = $message['message']['value'];

                $result = call_user_func($callback, $data);

                if (! $result) {
                    $this->logger->error('[consume] fail', ['topic'=>$topic, 'partation'=>$partition, 'data'=>$data]);

                    return false;
                }

                $this->logger->info('[consume] success', ['topic'=>$topic, 'partation'=>$partition, 'data'=>$data]);

                return true;
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());

                return false;
            }
        });
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

        $config = ProducerConfig::getInstance();

        $config->setMetadataBrokerList($this->config['brokers']);
        $config->setBrokerVersion($this->config['version']);

        if ($this->config['refresh_interval_ms']) {
            $config->setMetadataRefreshIntervalMs($this->config['refresh_interval_ms']);
        }

        $this->producerInstance = new Producer();
        $this->producerInstance->setLogger($this->logger);

        return $this->producerInstance;
    }

    private function consumerInstance()
    {
        if (isset($this->producerInstance)) {
            return $this->producerInstance;
        }

        $config = ConsumerConfig::getInstance();
        $config->setMetadataBrokerList($this->config['brokers']);
        $config->setTopics($this->config['topics']);
        $config->setGroupId($this->config['group']);
        $config->setOffsetReset('earliest');
        $config->setConsumeMode(ConsumerConfig::CONSUME_BEFORE_COMMIT_OFFSET);;
        $config->setBrokerVersion($this->config['version']);

        if ($this->config['refresh_interval_ms']) {
            $config->setMetadataRefreshIntervalMs($this->config['refresh_interval_ms']);
        }

        $this->consumerInstance = new Consumer();
        $this->consumerInstance->setLogger($this->logger);

        return $this->consumerInstance;
    }

}

