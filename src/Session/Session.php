<?php
namespace Libyaf\Session;

use Libyaf\Session\Exception;
use Libyaf\Logkit\Logger;
use Libyaf\Helper\Encrypt;

abstract class Session
{
    private static $default = 'default';

    private static $instances = [];

    protected $name = 'PHPSESSION';

    protected $lifetime = 0;

    protected $encrypt = null;

    protected $data = [];

    protected $destroyed = false;

    public static function ins($group = null, $id = null)
    {
        if ($group === null) {
            $group = Session::$default;
        }

        if (isset(Session::$instances[$group])) {
            return Session::$instances[$group];
        }

        $config = \Yaf\Application::app()->getConfig()->session->$group;

        if (! isset($config)) {
            throw new Exception('Failed to load Session group: '.$group);
        }

        $class  = 'Libyaf\\Session\\Driver\\'.ucfirst($config->driver);

        if (! class_exists($class)) {
            throw new Exception('Driver '.$class.' not found.');
        }

        Session::$instances[$group] = $session = new $class($config->toArray(), $id);

        register_shutdown_function([$session, 'write']);

        return Session::$instances[$group];
    }

    protected function __construct(array $config, $id)
    {
        if (isset($config['name'])) {
            $this->name = $config['name'];
        }

        if (isset($config['lifetime'])) {
            $this->lifetime = (int) $config['lifetime'];
        }

        if (isset($config['encrypt'])) {
            $this->encrypt = $config['encrypt'];
        }

        $this->read($id);
    }

    public function __toString()
    {
        $data = serialize($this->data);

        if ($this->encrypt) {
            $data = Encrypt::ins($this->encrypt)->encode($data);
        } else {
            $data = base64_encode($data);
        }

        return $data;
    }

    public function id()
    {
        return null;
    }

    public function name()
    {
        return $this->name;
    }

    public function get($key, $default = NULL)
    {
        return isset($this->data[$key]) ? $this->data[$key] : $default;
    }

    public function getOnce($key, $default = NULL)
    {
        $value = $this->get($key, $default);

        unset($this->data[$key]);

        return $value;
    }

    public function set($key, $value)
    {
        $this->data[$key] = $value;

        return $this;
    }

    public function delete(...$key)
    {
        foreach ($key as $item)
        {
            unset($this->data[$item]);
        }

        return $this;
    }

    public function read($id = null)
    {
        $data = null;

        try {
            $data = $this->_read($id);

            if (is_string($data)) {
                if ($this->encrypt) {
                    $data = Encrypt::ins($this->encrypt)->decode($data);
                } else {
                    $data = base64_decode($data);
                }

                $data = unserialize($data);
            }
        } catch (Exception $e) {
            Logger::ins('_session')->error('Error reading session data.');

            return false;
        }

        if (is_array($data)) {
            $this->data = $data;
        }
    }

    public function regenerate()
    {
        return $this->_regenerate();
    }

    public function write()
    {
        if (headers_sent() || $this->destroyed) {
            return false;
        }

        try {
            return $this->_write();
        } catch (Exception $e) {
            Logger::ins('_session')->error($e->getMessage());

            return false;
        }
    }

    public function destroy()
    {
        if ($this->destroyed === false) {
            if ($this->destroyed = $this->_destroy()) {
                $this->data = [];
            }
        }

        return $this->destroyed;
    }

    public function restart()
    {
        if ($this->destroyed === false) {
            $this->destroy();
        }

        $this->destroyed = false;

        return $this->_restart();
    }

    abstract protected function _read($id = NULL);

    abstract protected function _regenerate();

    abstract protected function _write();

    abstract protected function _destroy();

    abstract protected function _restart();

}

