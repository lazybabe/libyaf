<?php
namespace Libyaf\Id\Driver;

use Libyaf\Id\Exception;

class Snowflake implements DriverInterface
{
    const APCU_PREFIX = 'LIBYAF:ID:SNOWFLAKE:';

    const TWEPOCH = 1288834974657;

    const WORKER_ID_BITS = 8;
    const DATACENTER_ID_BITS = 3;
    const SEQUENCE_BITS = 11;

    const MAX_WORKER_ID = -1 ^ (-1 << self::WORKER_ID_BITS);
    const MAX_DATACENTER_ID  = -1 ^ (-1 << self::DATACENTER_ID_BITS);

    const WORKER_ID_SHIFT = self::SEQUENCE_BITS;
    const DATACENTER_ID_SHIFT = self::SEQUENCE_BITS + self::WORKER_ID_BITS;
    const TIMESTAMP_LEFT_SHIFT = self::SEQUENCE_BITS + self::WORKER_ID_BITS + self::DATACENTER_ID_BITS;

    const SEQUENCE_MASK = -1 ^ (-1 << self::SEQUENCE_BITS);

    const LAST_TIMESTAMP = -1;

    private $logger;
    private $cache;

    private $workerId;
    private $datacenterId;
    private $sequence = 0;

    public function __construct($config, \Psr\Log\LoggerInterface $logger)
    {
        if (! extension_loaded('apcu')) {
            throw new Exception('snowflake depends on php `APCu` extension');
        }

        $this->logger = $logger;

        if ($config['workerid']) {
            $workerId = intval($config['workerid']);
            if ($workerId > self::MAX_WORKER_ID || $workerId < 0) {
                throw new Exception("worker Id can't be greater than $workerId or less than 0");
            }
        } else if (isset($_SERVER['SERVER_ADDR'])) {
            // worker Id generate by server address, it's not safe
            $ip = sprintf('%u', ip2long($_SERVER['SERVER_ADDR']));
            $workerId = $ip % self::MAX_WORKER_ID;
        } else {
            throw new Exception("you must specify `workerid` configure or set env variable `SERVER_ADDR`");
        }

        $datacenterId = intval($config['datacenterid']);
        if ($datacenterId > self::MAX_DATACENTER_ID || $datacenterId < 0) {
            throw new Exception("datacenter Id can't be greater than $datacenterId or less than 0");
        }

        $this->workerId = $workerId;
        $this->datacenterId = $datacenterId;

        $this->logger->info('worker start.', ['workerId'=>$this->workerId, 'datacenterId'=>$datacenterId]);
    }

    public function getNextId()
    {
        $timestamp = $this->timeGen();

        $lastTimestamp = $this->getLastTimestamp();

        if ($timestamp < $lastTimestamp) {
            $this->logger->error("clock is moving backwards. Rejecting requests until $lastTimestamp.");
            throw new Exception(sprintf('Clock moved backwards. Refusing to generate id for %d milliseconds', $lastTimestamp - $timestamp));
        }

        if ($timestamp == $lastTimestamp) {
            $this->logger->debug('increase sequence at '.$timestamp);

            $sequence = $this->incSequence($timestamp) & self::SEQUENCE_MASK;
            if ($sequence == 0) {
                $timestamp = $this->tilNextMillis($lastTimestamp);

                $this->initSequence($timestamp, -1);
                $this->setLastTimestamp($timestamp);

                return $this->getNextId();
            }
        } else {
            $this->logger->debug('init sequence at '.$timestamp);

            $this->initSequence($timestamp, 0);
            $this->setLastTimestamp($timestamp);

            $sequence = 0;
        }

        return (($timestamp - self::TWEPOCH) << self::TIMESTAMP_LEFT_SHIFT) | ($this->datacenterId << self::DATACENTER_ID_SHIFT) | ($this->workerId << self::WORKER_ID_SHIFT) | $sequence;
    }

    private function timeGen()
    {
        return floor(microtime(true) * 1000);
    }

    private function getLastTimestamp()
    {
        $key = self::APCU_PREFIX.'last_timestamp';

        $timestamp = intval(apcu_fetch($key));

        return $timestamp ? : self::LAST_TIMESTAMP;
    }

    private function setLastTimestamp($timestamp)
    {
        $key = self::APCU_PREFIX.'last_timestamp';

        return apcu_store($key, $timestamp);
    }

    private function initSequence($timestamp, $sequence)
    {
        $key = self::APCU_PREFIX.$timestamp;

        return apcu_add($key, $sequence, 2);
    }

    private function incSequence($timestamp)
    {
        $key = self::APCU_PREFIX.$timestamp;

        return apcu_exists($key) ? apcu_inc($key) : 0;
    }

    private function tilNextMillis($lastTimestamp)
    {
        $timestamp = $this->timeGen();

        while ($timestamp <= $lastTimestamp) {
            $timestamp = $this->timeGen();
        }

        return $timestamp;
    }

}

