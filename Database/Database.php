<?php
namespace Database;

use Database\Logger;

class Database 
{
    private static $instances = [];

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
            throw new \Exception('Failed to load Database group: '.$group);
        }

        $driver = 'Database\\Driver\\'.$config->driver;

        if (! class_exists($driver)) {
            throw new \Exception('Driver '.$driver.' not found.');
        }

        Database::$instances[$group] = (new $driver($config->toArray()))->getDatabaseDriver();

        Database::$instances[$group]->getConfiguration()->setSQLLogger(new Logger($group));

        return Database::$instances[$group];
    }

    private function __construct()
    {

    }

}

