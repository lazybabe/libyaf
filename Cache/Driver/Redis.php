<?php
namespace Cache\Driver;

use Doctrine\Common\Cache as DC;
use Cache\Driver;

class Redis implements Driver
{
    private $instance;

    public function __construct($config)
    {
        $this->instance = new \Redis();
        $this->instance->connect($config->host, $config->port);
    }

    public function getCacheDriver()
    {
        $cacheDriver = new DC\RedisCache();
        $cacheDriver->setRedis($this->instance);

        return $cacheDriver;
    }
}

