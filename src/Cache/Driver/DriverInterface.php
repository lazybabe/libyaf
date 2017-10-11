<?php
namespace Libyaf\Cache\Driver;

interface DriverInterface
{
	public function __construct($config);

	public function getCacheDriver();
}

