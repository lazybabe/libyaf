<?php
namespace Libyaf\Database;

use Libyaf\Database\Exception;
use Libyaf\Database\Logger;

class Database
{
    private static $instances = [];

    private static $drivers = [];

    private static $default = 'default';

    public static function ins($group = null)
    {
        if (! isset($group)) {
            $group = Database::$default;
        }

        if (isset(Database::$instances[$group])) {
            return Database::$instances[$group];
        };

        $config  = \Yaf\Application::app()->getConfig()->database;

        $config = isset($config) ? $config->$group : null;

        if (! isset($config)) {
            throw new Exception('Failed to load Database group: '.$group);
        }

        $driver = 'Libyaf\\Database\\Driver\\'.$config->driver;

        if (! class_exists($driver)) {
            throw new Exception('Driver '.$driver.' not found.');
        }

        Database::$drivers[$group]      = new $driver($config->toArray());
        Database::$instances[$group]    = Database::$drivers[$group]->getDatabaseDriver();

        Database::$instances[$group]->getConfiguration()->setSQLLogger(new Logger($group));

        return Database::$instances[$group];
    }

    private function __construct()
    {

    }

    public static function ping($group = null)
    {
        if (! isset($group)) {
            $group = Database::$default;
        }

        if (isset(Database::$drivers[$group])) {
            Database::$drivers[$group]->ping();
        }
    }

}

