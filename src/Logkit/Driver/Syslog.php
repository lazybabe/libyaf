<?php
namespace Libyaf\Logkit\Driver;

use Monolog\Handler\SyslogHandler;
use Monolog\Formatter\LineFormatter;

class Syslog extends AbstractDriver
{
    public function getLoggerDriver($channel, $level)
    {
        $handler = new SyslogHandler($channel, LOG_LOCAL6, $level);

        $formatter  = new LineFormatter("%channel% %level_name%: %message% %context% %extra%");
        $handler->setFormatter($formatter);

        return $handler;
    }

}

