<?php
namespace Logkit;

use Monolog\Logger as MonoLogger;
use Monolog\Handler\SyslogHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Processor\ProcessIdProcessor;
use Monolog\Processor\PsrLogMessageProcessor;
use Logkit\Exception;

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

        $syslog     = new SyslogHandler($channel, LOG_LOCAL6, self::$logLevels[$config->log->level]);
        $syslogAll  = new SyslogHandler($channelAll, LOG_LOCAL6, self::$logLevels[$config->log->level]);

        //设置日志格式
        $formatter  = new LineFormatter("%channel% %level_name%: %message% %context% %extra%");
        $syslog->setFormatter($formatter);
        $syslogAll->setFormatter($formatter);

        $logger->pushHandler($syslog);
        $logger->pushHandler($syslogAll);

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

