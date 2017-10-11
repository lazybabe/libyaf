<?php
namespace Libyaf\Kvs\Driver\Type;

trait KString {
    abstract public function get($key);

    abstract public function getBit($key, $offset);

    abstract public function getRange($key, $start, $end);

    abstract public function getSet($key, $value);

    abstract public function set($key, $value, $ttl = null);

    abstract public function setBit($key, $offset, $value);

    abstract public function setEx($key, $ttl, $value);

    abstract public function setNx($key, $value);

    abstract public function strLen($key);

    abstract public function mGet(array $key);

    abstract public function mSet(array $key);

    abstract public function bitCount($key, $start, $end);

    abstract public function bitOp($op, $desKey, ...$keys);

    abstract public function incr($key, $value = 1);

    abstract public function decr($key, $value = 1);
}

