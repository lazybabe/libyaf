<?php
namespace Libyaf\Kvs\Driver;

abstract class AbstractDriver
{
    protected $logger;

    abstract public function connected();

    abstract public function reconnect();

    public function getDriverType()
    {
        return $this->driverType;
    }
}

