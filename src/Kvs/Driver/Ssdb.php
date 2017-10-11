<?php
namespace Libyaf\Kvs\Driver;

use Libyaf\Kvs\Driver\Type\KString;
use Libyaf\Kvs\Driver\Type\KKey;
use Libyaf\Kvs\Driver\Type\KHash;
use Libyaf\Kvs\Driver\Type\KList;
use Libyaf\Kvs\Driver\Type\KZset;
use Libyaf\Kvs\Exception;
use Libyaf\Kvs\Vendor\SimpleSSDB;

class Ssdb extends AbstractDriver
{
    use KString, KKey, KHash, KList, KZset;

    protected $driverType = 'ssdb';

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
        $this->instance = new SimpleSSDB($this->config->host, $this->config->port, $this->config->timeout * 1000);

        $this->logger->info('ssdb connect.', $this->config->toArray());

        if (isset($this->config->auth) && $this->config->auth) {
            $this->instance->auth($this->config->auth);
        }
    }

    public function __call($name, ...$arguments)
    {
        return call_user_func_array([$this->instance, $name], ...$arguments);
    }

    public function connected()
    {
        try {
            $this->instance->ping();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function reconnect()
    {
        return $this->connect();
    }

    public function get($key)
    {
        $result = $this->instance->get($key);

        if ($result === null || $result === false) {
            return false;
        } else {
            return $result;
        }
    }

    public function getBit($key, $offset)
    {
        return $this->instance->getBit($key, $offset);
    }

    public function getRange($key, $start, $end)
    {
        return $this->instance->substr($key, $start, $end - $start + 1);
    }

    public function getSet($key, $value)
    {
        return $this->instance->getSet($key, $value);
    }

    public function set($key, $value, $ttl = null)
    {
        if (is_numeric($ttl)) {
            $result = $this->instance->setx($key, $value, $ttl);
        } else {
            $result = $this->instance->set($key, $value);
        }

        return ($result === false) ? false : true;
    }

    public function setBit($key, $offset, $value)
    {
        return $this->instance->setBit($key, $offset, $value);
    }

    public function setEx($key, $ttl, $value)
    {
        return ($this->instance->setx($key, $ttl, $value) !== false);
    }

    public function setNx($key, $value)
    {
        return (bool) $this->instance->setnx($key, $value);
    }

    public function strLen($key)
    {
        return $this->instance->strlen($key);
    }

    public function mGet(array $keys)
    {
        $result = $this->instance->multi_get($keys);

        $return = [];

        foreach ($keys as $key=>$item) {
            $return[$key] = isset($result[$item]) ? $result[$item] : false;
        }

        return $return;
    }

    public function mSet(array $data)
    {
        return (bool) $this->instance->multi_set($data);
    }

    public function bitCount($key, $start, $end)
    {
        $result = $this->instance->bitCount($key, $start, $end);

        return is_array($result) ? (int) array_pop($result) : false;
    }

    public function bitOp($op, $desKey, ...$keys)
    {
        if (! in_array($op, ['AND', 'OR', 'NOT', 'XOR'])) {
            return false;
        }

        if (! $keys) {
            return false;
        }

        if ($op == 'NOT' && count($keys) != 1) {
            return false;
        }

        $values = $this->mGet($keys);

        $result = array_shift($values);

        foreach ($values as $value) {
            switch ($op) {
                case 'AND':
                    $result = $result & $value;
                    break;
                case 'OR':
                    $result = $result | $value;
                    break;
                case 'XOR':
                    $result = $result ^ $value;
                    break;
                default:
                    break;
            }
        }

        if ($op == 'NOT') {
            $result = ~ $result;
        }

        return $this->set($desKey, $result) ? strlen($result) : false;
    }

    public function incr($key, $value = 1)
    {
        return $this->instance->incr($key, $value);
    }

    public function decr($key, $value = 1)
    {
        return $this->instance->incr($key, - $value);
    }

    public function del($key)
    {
        if (is_array($key)) {
            return $this->instance->multi_del($key);
        } else {
            return $this->instance->del($key);
        }
    }

    public function exists($key)
    {
        return $this->instance->exists($key);
    }

    public function expire($key, $ttl)
    {
        return (bool) $this->instance->expire($key, $ttl);
    }

    public function expireAt($key, $timestamp)
    {
        return (bool) $this->instance->expire($key, $timestamp - time());
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
        return $this->instance->hexists($key, $hashKey);
    }

    public function hGet($key, $hashKey)
    {
        $result = $this->instance->hget($key, $hashKey);

        if ($result === null || $result === false) {
            return false;
        } else {
            return $result;
        }
    }

    public function hGetAll($key)
    {
        return $this->instance->hgetall($key);
    }

    public function hIncr($key, $hashKey, $value = 1)
    {
        return $this->instance->hincr($key, $hashKey, $value);
    }

    public function hKeys($key)
    {
        return $this->instance->hkeys($key, '', '', $this->hLen($key));
    }

    public function hLen($key)
    {
        return $this->instance->hsize($key);
    }

    public function hMGet($key, $hashKey)
    {
        $result = $this->instance->multi_hget($key, $hashKey);

        $return = [];

        foreach ($hashKey as $item) {
            $return[$item] = isset($result[$item]) ? $result[$item] : false;
        }

        return $return;
    }

    public function hMSet($key, $data)
    {
        return ($this->instance->multi_hset($key, $data) !== false);
    }

    public function hSet($key, $hashKey, $value)
    {
        return $this->instance->hSet($key, $hashKey, $value);
    }

    public function hVals($key)
    {
        return array_values($this->hGetAll($key));
    }

    public function lIndex($key, $index)
    {
        $result = $this->instance->qget($key, $index);

        if ($result === null || $result === false) {
            return false;
        } else {
            return $result;
        }
    }

    public function lLen($key)
    {
        return $this->instance->qsize($key);
    }

    public function lPop($key)
    {
        $result = $this->instance->qpop_front($key);

        if ($result === null || $result === false) {
            return false;
        } else {
            return $result;
        }
    }

    public function lPush($key, $value)
    {
        return $this->instance->qpush_front($key, $value);
    }

    public function lRange($key, $start, $end)
    {
        return $this->instance->qrange($key, $start, $end - $start + 1);
    }

    public function lSet($key, $index, $value)
    {
        return ($this->instance->qset($key, $index, $value) !== false);
    }

    public function rPop($key)
    {
        $result = $this->instance->qpop_back($key);

        if ($result === null || $result === false) {
            return false;
        } else {
            return $result;
        }
    }

    public function rPush($key, $value)
    {
        return $this->instance->qpush_back($key, $value);
    }

    public function zAdd($key, $score, $value)
    {
        return $this->instance->zset($key, $value, $score);
    }

    public function zCard($key)
    {
        return $this->instance->zcount($key, '', '');
    }

    public function zCount($key, $start, $end)
    {
        $start  = ($start === '') ? 0 : $start;
        $end    = ($end === '') ? 0 : $end;

        return $this->instance->zcount($key, $start, $end);
    }

    public function zIncrBy($key, $value, $member)
    {
        return $this->instance->zincr($key, $member, $value);
    }

    public function zRange($key, $start, $end, $withScore = false)
    {
        $result = $this->instance->zrange($key, $start, $end - $start + 1);

        return $withScore ? $result : array_keys($result);
    }

    public function zRevRange($key, $start, $end, $withScore = false)
    {
        $result = $this->instance->zrrange($key, $start, $end - $start + 1);

        return $withScore ? $result : array_keys($result);
    }

    public function zRank($key, $member)
    {
        return $this->instance->zrank($key, $member);
    }

    public function zRevRank($key, $member)
    {
        return $this->instance->zrrank($key, $member);
    }

    public function zRem($key, $member)
    {
        return $this->instance->zdel($key, $member);
    }

    public function zRemRangeByRank($key, $start, $end)
    {
        return $this->instance->zremrangebyrank($key, $start, $end);
    }

    public function zRemRangeByScore($key, $start, $end)
    {
        return $this->instance->zremrangebyscore($key, $start, $end);
    }

    public function zScore($key, $member)
    {
        $result = $this->instance->zget($key, $member);

        if ($result === null || $result === false) {
            return false;
        } else {
            return $result;
        }
    }

}

