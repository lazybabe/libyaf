<?php
namespace Libyaf\Database\Driver;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Libyaf\Database\Driver;

class PDOMysql implements Driver
{
    private $instance;

    public function __construct(array $config)
    {
        $conf = new Configuration();

        $connectionParams = [
            'driver'    => 'pdo_mysql',
            'host'      => $config['host'],
            'port'      => $config['port'],
            'user'      => $config['username'],
            'password'  => $config['password'],
            'dbname'    => $config['dbname'],
            'charset'   => $config['charset'],
        ];

        $this->instance = DriverManager::getConnection($connectionParams, $conf);
    }

    public function getDatabaseDriver()
    {
        return $this->instance;
    }

    public function ping()
    {
        try {
            @$this->instance->getWrappedConnection()->getAttribute(\PDO::ATTR_SERVER_INFO);
        } catch (\PDOException $e) {
            if ($e->getCode() == 'HY000') {
                $this->instance->close();
                $this->instance->connect();
            } else {
                throw $e;
            }
        }
    }

}

