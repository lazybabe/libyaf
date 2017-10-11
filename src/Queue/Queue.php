<?php
namespace Libyaf\Queue;

use Libyaf\Queue\Exception;
use Libyaf\Logkit\Logger;

class Queue
{
    private static $instances = [];

    private static $default = 'default';

    public static function ins($group = null)
    {
        if (! isset($group)) {
            $group = Queue::$default;
        }

        if (isset(Queue::$instances[$group])) {
            return Queue::$instances[$group];
        };

        $config  = \Yaf\Application::app()->getConfig()->queue;

        $config = isset($config) ? $config->$group : null;

        if (! isset($config)) {
            throw new Exception('Failed to load Queue group: '.$group);
        }

        $driver = 'Libyaf\\Queue\\Driver\\'.ucfirst($config->driver);

        if (! class_exists($driver)) {
            throw new Exception('Driver '.$driver.' not found.');
        }

        $instance = new $driver($config);
        $instance->setLogger(Logger::ins('_queue'));

        Queue::$instances[$group] = $instance;

        return Queue::$instances[$group];
    }

    private function __construct()
    {

    }

}

