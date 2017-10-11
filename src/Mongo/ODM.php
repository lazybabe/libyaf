<?php
namespace Libyaf\Mongo;

use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Libyaf\Mongo\Exception;
use Libyaf\Logkit\Logger;

class ODM
{
	private static $ins = [];

	private $logger;

	private $ducumentManager;

	public static function ins($name = 'default')
	{
	    if (isset(self::$ins[$name])) {
	    	return self::$ins[$name];
	    }

	    //获取mongodb连接配置
	    $mongoConfig = \Yaf\Application::app()->getConfig()->mongo->$name;

	    if (! $mongoConfig) {
	    	throw new Exception('The mongo connection config "'.$name.'" not found.');
	    }

	    self::$ins[$name] = new self($mongoConfig);

	    return self::$ins[$name];
	}

	private function __construct($mongoConfig)
	{
	    $this->logger = Logger::ins('_mongo');

	    AnnotationDriver::registerAnnotationClasses();

	    //获取ODM配置
	    $ODMConfig = \Yaf\Application::app()->getConfig()->ODM;

	    if (! $ODMConfig) {
	    	throw new Exception('The ODM config not found.');
	    }

	    //连接mongodb
	    $dsn		= 'mongodb://'.$mongoConfig->host.':'.$mongoConfig->port;
	    $options	= $mongoConfig->options->toArray();
	    $connection = new Connection($dsn, $options);

	    //配置ODM
	    $config = new Configuration();
	    $config->setProxyDir($ODMConfig->proxiesDir);
	    $config->setProxyNamespace('Proxies');
	    $config->setHydratorDir($ODMConfig->hydratorsDir);
	    $config->setHydratorNamespace('Hydrators');
	    $config->setMetadataDriverImpl(AnnotationDriver::create($ODMConfig->documentsDir));

	    $this->ducumentManager = DocumentManager::create($connection, $config);
	}

	public function getDocumentManager()
	{
		return $this->ducumentManager;
	}
}

