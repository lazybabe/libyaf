<?php
namespace Libyaf\Helper;

class Encrypt
{
    public static $default = 'default';

    public static $instances = [];

    private static $rand = MCRYPT_DEV_URANDOM;

    private $key;

    private $mode;

    private $cipher;

    private $iv_size;

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

        $key    = $config->key ? : null;
        $mode   = $config->mode ? : MCRYPT_MODE_NOFB;
        $cipher = $config->cipher ? : MCRYPT_RIJNDAEL_128;

        if (! $key) {
            throw new \Exception('Need encrypt key');
        }

        Encrypt::$instances[$group] = new Encrypt($key, $mode, $cipher);

        return Encrypt::$instances[$group];
    }

    private function __construct($key, $mode, $cipher)
    {
        $size = mcrypt_get_key_size($cipher, $mode);

        if (isset($key[$size])) {
            $key = substr($key, 0, $size);
        } else if (version_compare(PHP_VERSION, '5.6.0', '>=')) {
            $key = $this->normalizekey($key, $cipher, $mode);
        }

        $this->key    = $key;
        $this->mode   = $mode;
        $this->cipher = $cipher;

        $this->iv_size = mcrypt_get_iv_size($this->cipher, $this->mode);
    }

    public function encode($data)
    {
        $iv = $this->create_iv();

        $data = mcrypt_encrypt($this->cipher, $this->key, $data, $this->mode, $iv);

        return base64_encode($iv.$data);
    }

    public function decode($data)
    {
        $data = base64_decode($data, true);
        if (! $data) {
            return false;
        }

        $iv = substr($data, 0, $this->iv_size);

        if ($this->iv_size !== strlen($iv)) {
            return false;
        }

        $data = substr($data, $this->iv_size);

        return rtrim(mcrypt_decrypt($this->cipher, $this->key, $data, $this->mode, $iv), "\0");
    }

    private function create_iv()
    {
        if (Encrypt::$rand !== MCRYPT_DEV_URANDOM && Encrypt::$rand !== MCRYPT_DEV_RANDOM) {
            Encrypt::$rand = MCRYPT_DEV_URANDOM;
        }

        return mcrypt_create_iv($this->iv_size, Encrypt::$rand);
    }

    private function normalizekey($key, $cipher, $mode)
    {
        $td = mcrypt_module_open($cipher, '', $mode, '');

        foreach (mcrypt_enc_get_supported_key_sizes($td) as $supported) {
            if (strlen($key) <= $supported) {
                return str_pad($key, $supported, "\0");
            }
        }

        return substr($key, 0, mcrypt_get_key_size($cipher, $mode));
    }

}

