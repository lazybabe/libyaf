<?php
namespace Libyaf\Logkit\Driver;

abstract class AbstractDriver
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

	abstract public function getLoggerDriver($channel, $level);
}

