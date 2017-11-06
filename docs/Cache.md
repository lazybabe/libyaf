## 简介

> Cache是基于<a href="http://docs.doctrine-project.org/projects/doctrine-common/en/latest/reference/caching.html" target="_blank">doctrine\cache</a>，获取连接实例的封装。
> 目前driver有redis，如有需要可以方便的扩展mc等其他driver

## 配置
**application.ini**
```ini
; 不同项目间可能使用同一cache实例，此时通过配置项目名projName，作为cache的命名空间
projName                    = 'cs'

; 默认使用default组的配置，可以配置其他组
cache.default.driver        = 'redis'
cache.default.host          = '192.168.0.49'
cache.default.port          = '6379'
cache.default.username      = ''
cache.default.password      = ''

cache.master.driver        = 'redis'
cache.master.host          = '192.168.0.49'
cache.master.port          = '6380'
cache.master.username      = ''
cache.master.password      = ''
```

## 如何使用
```php
<?php
use Libyaf\Cache\Cache;

//使用默认cache组，Cache::ins()等于Cache::ins('default')
$value = Cache::ins()->fetch('value');
if ($value === false)
{
    $value = 'some value';
    Cache::ins()->save('value', $value, 7200);
}


//使用另一个cache组
$value = Cache::ins('master')->fetch('value');
if ($value === false)
{
    $value = 'some value';
    Cache::ins('master')->save('value', $value, 7200);
}
```

## Method

- **fetch**

- **save**

- **delete**

- **contains**

- **getStats**


## 更多参考
- <a href="http://docs.doctrine-project.org/projects/doctrine-common/en/latest/reference/caching.html" target="_blank">Doctrine\Cache 文档</a>
