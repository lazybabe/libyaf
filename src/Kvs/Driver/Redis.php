<?php
namespace Libyaf\Kvs\Driver;

use Libyaf\Kvs\Driver\Type\KString;
use Libyaf\Kvs\Driver\Type\KKey;
use Libyaf\Kvs\Driver\Type\KHash;
use Libyaf\Kvs\Driver\Type\KList;
use Libyaf\Kvs\Driver\Type\KZset;
use Libyaf\Kvs\Exception;

class Redis extends AbstractDriver
{
    use KString, KKey, KHash, KList, KZset;

    protected $driverType = 'redis';

    private $instance;

    private $config;

    public function __construct($config, \Psr\Log\LoggerInterface $logger)
    {
        $this->logger = $logger;

        $this->config = $config;

        $this->connect();
    }

    private function connect()
    {
        $this->instance = new \Redis();

        $this->logger->info('redis connect.', $this->config->toArray());

        if (isset($this->config->persistent) && $this->config->persistent) {
            $this->instance->pconnect($this->config->host, $this->config->port, $this->config->timeout);
        } else {
            $this->instance->connect($this->config->host, $this->config->port, $this->config->timeout);
        }

        if (isset($this->config->auth) && $this->config->auth) {
            $this->instance->auth($this->config->auth);
        }

        if (isset($this->config->database) && is_numeric($this->config->database)) {
            $this->instance->select($this->config->database);
        }

        if (isset($this->config->prefix) && $this->config->prefix !== '') {
            $this->instance->setOption(\Redis::OPT_PREFIX, $this->config->prefix);
        }

        if (isset($this->config->serializer)) {
            switch (strtolower($this->config->serializer)) {
                case 'php':
                    $serializer = \Redis::SERIALIZER_PHP;
                    break;
                case 'igbinary':
                    $serializer = \Redis::SERIALIZER_IGBINARY;
                    break;
                default:
                    $serializer = \Redis::SERIALIZER_NONE;
                    break;
            }

            $this->instance->setOption(\Redis::OPT_SERIALIZER, $serializer);
        }
    }

    public function __call($name, ...$arguments)
    {
        if (! method_exists($this->instance, $name)) {
            throw new Exception("phpredis does not have a method '$name'");
        }

        return call_user_func_array([$this->instance, $name], ...$arguments);
    }

    public function connected()
    {
        return ($this->instance->ping() === '+PONG') ? true : false;
    }

    public function reconnect()
    {
        $this->connect();
    }

    public function get($key)
    {
        return $this->instance->get($key);
    }

    public function getBit($key, $offset)
    {
        return $this->instance->getBit($key, $offset);
    }

    public function getRange($key, $start, $end)
    {
        return $this->instance->getRange($key, $start, $end);
    }

    public function getSet($key, $value)
    {
        return $this->instance->getSet($key, $value);
    }

    public function set($key, $value, $ttl = null)
    {
        if (is_numeric($ttl)) {
            return $this->instance->set($key, $value, $ttl);
        } else {
            return $this->instance->set($key, $value);
        }
    }

    public function setBit($key, $offset, $value)
    {
        return $this->instance->setBit($key, $offset, $value);
    }

    public function setEx($key, $ttl, $value)
    {
        return $this->instance->setEx($key, $ttl, $value);
    }

    public function setNx($key, $value)
    {
        return $this->instance->setNx($key, $value);
    }

    public function strLen($key)
    {
        return $this->instance->strLen($key);
    }

    public function mGet(array $keys)
    {
        return $this->instance->mGet($keys);
    }

    public function mSet(array $data)
    {
        return $this->instance->mSet($data);
    }

    public function bitCount($key, $start, $end)
    {
        return $this->instance->bitCount($key, $start, $end);
    }

    public function bitOp($op, $desKey, ...$keys)
    {
        return $this->instance->bitOp($op, $desKey, ...$keys);
    }

    public function incr($key, $value = 1)
    {
        return $this->instance->incr($key, $value);
    }

    public function decr($key, $value = 1)
    {
        return $this->instance->decr($key, $value);
    }

    public function del($key)
    {
        return $this->instance->del($key);
    }

    public function exists($key)
    {
        return $this->instance->exists($key);
    }

    public function expire($key, $ttl)
    {
        return $this->instance->expire($key, $ttl);
    }

    public function expireAt($key, $timestamp)
    {
        return $this->instance->expireAt($key, $timestamp);
    }

    public function ttl($key)
    {
        return $this->instance->ttl($key);
    }

    public function hDel($key)
    {
        return $this->instance->hDel($key);
    }

    public function hExists($key, $hashKey)
    {
        return $this->instance->hExists($key, $hashKey);
    }

    public function hGet($key, $hashKey)
    {
        return $this->instance->hGet($key, $hashKey);
    }

    public function hGetAll($key)
    {
        return $this->instance->hGetAll($key);
    }

    public function hIncr($key, $hashKey, $value = 1)
    {
        return $this->instance->hIncrBy($key, $hashKey, $value);
    }

    public function hKeys($key)
    {
        return $this->instance->hKeys($key);
    }

    public function hLen($key)
    {
        return $this->instance->hLen($key);
    }

    public function hMGet($key, $hashKey)
    {
        return $this->instance->hMGet($key, $hashKey);
    }

    public function hMSet($key, $data)
    {
        return $this->instance->hMSet($key, $data);
    }

    public function hSet($key, $hashKey, $value)
    {
        return $this->instance->hSet($key, $hashKey, $value);
    }

    public function hVals($key)
    {
        return $this->instance->hVals($key);
    }

    public function lIndex($key, $index)
    {
        return $this->instance->lIndex($key, $index);
    }

    public function lLen($key)
    {
        return $this->instance->lLen($key);
    }

    public function lPop($key)
    {
        return $this->instance->lPop($key);
    }

    public function lPush($key, $value)
    {
        return $this->instance->lPush($key, $value);
    }

    public function lRange($key, $start, $end)
    {
        return $this->instance->lRange($key, $start, $end);
    }

    public function lSet($key, $index, $value)
    {
        return $this->instance->lSet($key, $index, $value);
    }

    public function rPop($key)
    {
        return $this->instance->rPop($key);
    }

    public function rPush($key, $value)
    {
        return $this->instance->rPush($key, $value);
    }

    public function zAdd($key, $score, $value)
    {
        return $this->instance->zAdd($key, $score, $value);
    }

    public function zCard($key)
    {
        return $this->instance->zCard($key);
    }

    public function zCount($key, $start, $end)
    {
        return $this->instance->zCount($key, $start, $end);
    }

    public function zIncrBy($key, $value, $member)
    {
        return $this->instance->zIncrBy($key, $value, $member);
    }

    public function zRange($key, $start, $end, $withScore = false)
    {
        return $this->instance->zRange($key, $start, $end, $withScore);
    }

    public function zRevRange($key, $start, $end, $withScore = false)
    {
        return $this->instance->zRevRange($key, $start, $end, $withScore);
    }

    public function zRank($key, $member)
    {
        return $this->instance->zRank($key, $member);
    }

    public function zRevRank($key, $member)
    {
        return $this->instance->zRevRank($key, $member);
    }

    public function zRem($key, $member)
    {
        return $this->instance->zRem($key, $member);
    }

    public function zRemRangeByRank($key, $start, $end)
    {
        return $this->instance->zRemRangeByRank($key, $start, $end);
    }

    public function zRemRangeByScore($key, $start, $end)
    {
        return $this->instance->zRemRangeByScore($key, $start, $end);
    }

    public function zScore($key, $member)
    {
        return $this->instance->zScore($key, $member);
    }

}

