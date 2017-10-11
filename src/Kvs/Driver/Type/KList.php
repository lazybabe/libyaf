<?php
namespace Libyaf\Kvs\Driver\Type;

trait KList {
    abstract public function lIndex($key, $index);

    abstract public function lLen($key);

    abstract public function lPop($key);

    abstract public function lPush($key, $value);

    abstract public function lRange($key, $start, $end);

    abstract public function lSet($key);

    abstract public function rPop($key);

    abstract public function rPush($key);

}

