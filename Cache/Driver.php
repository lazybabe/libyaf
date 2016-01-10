<?php
namespace Cache;

interface Driver
{
	public function __construct($config);

	public function getCacheDriver();
}

