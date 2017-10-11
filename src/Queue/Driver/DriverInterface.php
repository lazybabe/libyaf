<?php
namespace Libyaf\Queue\Driver;

interface DriverInterface
{
    public function push($name, $message);

    public function pull($name, callable $callback);

    public function delay($name, $message, $seconds);

    public function size($name);

}

