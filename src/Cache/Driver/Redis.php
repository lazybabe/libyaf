<?php
namespace Libyaf\Cache\Driver;

use Doctrine\Common\Cache as DC;

class Redis implements DriverInterface
{
    private $instance;

    public function __construct($config)
    {
        $this->instance = new \Redis();
        $this->instance->connect($config->host, $config->port);

        if (isset($config->dbnum)) {
            $this->instance->select(intval($config->dbnum));
        }
    }

    public function getCacheDriver()
    {
        $cacheDriver = new DC\RedisCache();
        $cacheDriver->setRedis($this->instance);

        return $cacheDriver;
    }
}

