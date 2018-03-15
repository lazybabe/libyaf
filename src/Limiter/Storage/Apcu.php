<?php
namespace Libyaf\Limiter\Storage;

class Apcu implements Storage
{
    public function exists($key)
    {
        return apcu_exists($key);
    }

    public function save($key, $value, $ttl)
    {
        return apcu_store($key, (int) ceil($value), $ttl);
    }

    public function fetch($key)
    {
        return apcu_fetch($key);
    }

    public function incr($key, $value)
    {
        return apcu_add($key, (int) ceil($value));
    }

    public function decr($key, $value)
    {
        return apcu_dec($key, (int) ceil($value));
    }

    public function remove($key)
    {
        return apcu_delete($key);
    }
}

