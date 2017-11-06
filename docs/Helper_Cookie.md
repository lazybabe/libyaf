## 简介
> Cookie类是对$_COOKIE的一些通用操作
>
> 可以避免诸如deleted导致的问题，也可以对cookie值加salt使之更为安全

## 配置
**application.ini**
```ini
cookie.salt                 = 'cs@xxx.com'
cookie.expire               = 2592000
cookie.path                 = '/'
cookie.domain               = '.cs.xxx.com'
cookie.secure               = false
cookie.httponly             = false
```

## 初始化
**Bootstrap.php**
```php
<?php
use Libyaf\Helper\Cookie;
class Bootstrap extends Yaf\Bootstrap_Abstract
{
    public function _initCookie($dispatcher)
    {
        $config = Yaf\Registry::get('config')->cookie;

        if ($config) {
            Cookie::$salt       = (string)  $config->salt;
            Cookie::$expiration = (int)     $config->expire;
            Cookie::$path       = (string)  $config->path;
            Cookie::$domain     = (string)  $config->domain;
            Cookie::$secure     = (bool)    $config->secure;
            Cookie::$httponly   = (bool)    $config->httponly;
        }
    }
}
```

## 如何使用
```php
<?php
use Helper\Cookie;

Cookie::set('username', 'qinyuguang');

Cookie::get('username');

Cookie::delete('username');
```

