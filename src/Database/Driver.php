<?php
namespace Libyaf\Database;

interface Driver
{
	public function __construct(array $config);

	public function getDatabaseDriver();

    public function ping();
}

