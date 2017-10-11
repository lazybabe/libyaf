<?php
namespace Libyaf\Cache;

use Libyaf\Cache\Exception;

class Cache
{
    private static $instances = [];

    private static $default = 'default';

    public static function ins($group = null)
    {
        if (! isset($group)) {
            $group = Cache::$default;
        }

        if (isset(Cache::$instances[$group])) {
            return Cache::$instances[$group];
        }

        $allConfig  = \Yaf\Application::app()->getConfig();

        $config = isset($allConfig->cache) ? $allConfig->cache->$group : null;

        if (! isset($config)) {
            throw new Exception('Failed to load Cache group: '.$group);
        }

        $class = 'Libyaf\\Cache\\Driver\\'.ucfirst($config->driver);

        if (! class_exists($class)) {
            throw new Exception('Driver '.$class.' not found.');
        }

        $cache = (new $class($config))->getCacheDriver();

        //获取项目名作为cache namespace
        $projName = $allConfig->projName;
        $projName = isset($projName) ? strtolower($projName) : 'default';
        $cache->setNamespace($projName);

        Cache::$instances[$group] = $cache;

        return Cache::$instances[$group];
    }

    private function __construct()
    {

    }
}

