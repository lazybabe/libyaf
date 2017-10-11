<?php
namespace Libyaf\Kvs\Driver\Type;

trait KZset {
    abstract public function zAdd($key, $score, $value);

    abstract public function zCard($key);

    abstract public function zCount($key, $start, $end);

    abstract public function zIncrBy($key, $value, $member);

    abstract public function zRange($key, $start, $end, $withScore = false);

    abstract public function zRevRange($key, $start, $end, $withScore = false);

    abstract public function zRank($key, $member);

    abstract public function zRevRank($key, $member);

    abstract public function zRem($key, $member);

    abstract public function zRemRangeByRank($key, $start, $end);

    abstract public function zRemRangeByScore($key, $start, $end);

    abstract public function zScore($key, $member);

}

