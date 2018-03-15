<?php
namespace Libyaf\Limiter\Storage;

interface Storage
{
    public function exists($key);

    public function save($key, $value, $ttl);

    public function fetch($key);

    public function incr($key, $value);

    public function decr($key, $value);

    public function remove($key);

}

