<?php
namespace Libyaf\Kvs\Driver\Type;

trait KHash {
    abstract public function hDel($key);

    abstract public function hExists($key, $hashKey);

    abstract public function hGet($key, $hashKey);

    abstract public function hGetAll($key);

    abstract public function hIncr($key, $hashKey, $value = 1);

    abstract public function hKeys($key);

    abstract public function hLen($key);

    abstract public function hMGet($key);

    abstract public function hMSet($key);

    abstract public function hSet($key);

    abstract public function hVals($key);

}

