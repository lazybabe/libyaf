<?php
namespace Libyaf\Logkit\Driver;

use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

class File extends AbstractDriver
{
    public function getLoggerDriver($channel, $level)
    {
        $logname    = $channel.date('/Y/m/d/H').'.log';
        $logfile    = $this->config->filepath.'/'.$logname;

        $handler    = new StreamHandler($logfile, $level);

        $formatter  = new LineFormatter("%datetime% $channel %channel% %level_name%: %message% %context% %extra%\n");
        $handler->setFormatter($formatter);

        return $handler;
    }

}

