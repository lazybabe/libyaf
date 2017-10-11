<?php
namespace Libyaf\Session\Driver;

use Libyaf\Session\Session;
use Libyaf\Helper\Cookie;
use Libyaf\Cache\Cache as CC;

class Cache extends Session
{
    protected $cache;

    protected $sessionid;

    protected $updateid;

    public function __construct(array $config = null, $id = null)
    {
        if (! isset($config['cache'])) {
            $config['cache'] = 'default';
        }

        $this->cache = CC::ins($config['cache']);

        parent::__construct($config, $id);
    }

    public function id()
    {
        return $this->sessionid;
    }

    protected function _read($id = null)
    {
        if (! is_null($id) || $id = Cookie::get($this->name)) {
            if ($id == 'deleted') {
                $this->_regenerate();
                return null;
            }

            $ret = $this->cache->fetch($id);

            if ($ret !== false) {
                $this->sessionid = $this->updateid = $id;
                return $ret;
            }
        }

        $this->_regenerate();

        return null;
    }

    protected function _regenerate()
    {
        do {
            $id     = str_replace('.', '', uniqid(null, true));

            $ret    = $this->cache->fetch($id);
        } while (! is_null($ret) && $ret !== false);

        return $this->sessionid = $id;
    }

    protected function _write()
    {
        $this->cache->save($this->sessionid, $this->__toString(), $this->lifetime);

        $this->updateid = $this->sessionid;

        Cookie::$httponly = TRUE;
        Cookie::set($this->name, $this->sessionid, $this->lifetime);
        Cookie::$httponly = FALSE;

        return TRUE;
    }

    protected function _restart()
    {
        $this->_regenerate();

        return TRUE;
    }

    protected function _destroy()
    {
        return $this->cache->delete($this->updateid);
    }

}

