<?php
namespace Libyaf\Logkit;

use Monolog\Logger as MonoLogger;
use Monolog\Processor\ProcessIdProcessor;
use Monolog\Processor\PsrLogMessageProcessor;
use Libyaf\Logkit\Exception;

class Logger
{
    private static $ins = [];

    private static $logLevels = [
        'emergency' => MonoLogger::EMERGENCY,
        'alert'     => MonoLogger::ALERT,
        'critical'  => MonoLogger::CRITICAL,
        'error'     => MonoLogger::ERROR,
        'warning'   => MonoLogger::WARNING,
        'notice'    => MonoLogger::NOTICE,
        'info'      => MonoLogger::INFO,
        'debug'     => MonoLogger::DEBUG
    ];

    public static function ins($space = 'default')
    {
        //获取配置
        $config     = \Yaf\Application::app()->getConfig();

        //项目名
        $projName   = isset($config->projName) ? strtolower($config->projName) : 'default';

        $channel    = $projName.'/'.$space;
        $channelAll = $projName.'/_all';

        if (isset(self::$ins[$channel])) {
        	return self::$ins[$channel];
        }

        //日志配置
        if (! isset($config->log)) {
            throw new Exception('must config the logger first.');
        }

        $logger     = new MonoLogger($space);

        $class = 'Libyaf\\Logkit\\Driver\\'.ucfirst($config->log->driver);

        if (! class_exists($class)) {
            throw new Exception('Driver '.$class.' not found.');
        }

        $driver     = new $class($config->log);

        $level      = self::$logLevels[$config->log->level];

        $handler    = $driver->getLoggerDriver($channel, $level);
        $handlerAll = $driver->getLoggerDriver($channelAll, $level);

        $logger->pushHandler($handler);
        $logger->pushHandler($handlerAll);

        //增加pid
        $processor  = new ProcessIdProcessor();
        $logger->pushProcessor($processor);

        //与psr3 context保持一致
        $processor  = new PsrLogMessageProcessor();
        $logger->pushProcessor($processor);

        self::$ins[$channel] = $logger;

        return self::$ins[$channel];
    }
}

