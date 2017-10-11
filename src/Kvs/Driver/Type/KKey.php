<?php
namespace Libyaf\Kvs\Driver\Type;

trait KKey {
    abstract public function del($key);

    abstract public function exists($key);

    abstract public function expire($key, $ttl);

    abstract public function expireAt($key, $timestamp);

    abstract public function ttl($key);

}

