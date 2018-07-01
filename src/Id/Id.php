<?php
namespace Libyaf\Id;

use Libyaf\Id\Exception;
use Libyaf\Logkit\Logger;

class Id
{
    private static $instances = [];

    private static $default = 'default';

    public static function ins($group = null, $customConfig = [])
    {
        if (! isset($group)) {
            $group = Id::$default;
        }

        if (isset(Id::$instances[$group])) {
            return Id::$instances[$group];
        };

        $config  = \Yaf\Application::app()->getConfig()->id;

        $config = isset($config) ? $config->$group : null;

        if (! isset($config)) {
            throw new Exception('Failed to load Id group: '.$group);
        }

        $driver = 'Libyaf\\Id\\Driver\\'.ucfirst($config->driver);

        if (! class_exists($driver)) {
            throw new Exception('Driver '.$driver.' not found.');
        }

        $config = $config->toArray() + $customConfig;

        $logger = Logger::ins('_id');

        $instance = new $driver($config, $logger);

        Id::$instances[$group] = $instance;

        return Id::$instances[$group];
    }

    private function __construct()
    {

    }

}

