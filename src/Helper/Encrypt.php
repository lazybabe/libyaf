<?php
namespace Libyaf\Helper;

class Encrypt
{
    public static $default = 'default';

    public static $instances = [];

    private $key;

    private $method;

    private $options;

    private $iv;

    public static function ins($group = null)
    {
        if ($group === null) {
            $group = Encrypt::$default;
        }

        if (isset(Encrypt::$instances[$group])) {
            return Encrypt::$instances[$group];
        }

        $config  = \Yaf\Application::app()->getConfig()->encrypt;

        $config = isset($config) ? $config->$group : null;

        if (! isset($config)) {
            throw new \Exception('Failed to load Encrypt group: '.$group);
        }

        $key        = $config->key ? : null;
        $method     = $config->method ? : 'AES-256-ECB';
        $options    = $config->options ? : 0;
        $iv         = $config->iv ? : '';

        if (! $key) {
            throw new \Exception('Need encrypt key');
        }

        Encrypt::$instances[$group] = new Encrypt($key, $method, $options, $iv);

        return Encrypt::$instances[$group];
    }

    private function __construct($key, $method, $options = null, $iv = null)
    {
        $this->key      = $key;
        $this->method   = $method;
        $this->options  = $options;
        $this->iv       = $iv;
    }

    public function encode($data)
    {
        return openssl_encrypt($data, $this->method, $this->key, $this->options, $this->iv);
    }

    public function decode($data)
    {
        return openssl_decrypt($data, $this->method, $this->key, $this->options, $this->iv);
    }

}

