<?php
namespace Libyaf\Queue\Driver;

abstract class AbstractDriver
{
    protected $logger;

    public function setLogger(\Psr\Log\LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

}

