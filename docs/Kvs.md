## 简介

> Kvs（Key Value Storage）是用来方便获取KV存储连接实例的工具。

> 目前driver包括<a href="http://www.redis.cn/" target="_blank">redis</a>、<a href="http://ssdb.io/zh_cn/" target="_blank">ssdb</a>，对一些通用方法进行抽象，使ssdb可以兼容<a href="https://github.com/phpredis/phpredis" target="_blank">phpredis</a>写法。

## 配置
**application.ini**
```ini
; 默认使用default组的配置，可以配置其他组
kvs.default.driver          = 'redis'
kvs.default.host            = '127.0.0.1'
kvs.default.port            = '6379'
kvs.default.timeout         = 2
kvs.default.persistent      = true
kvs.default.database        = 9
kvs.default.auth            = ''
kvs.default.prefix          = ''
;kvs.default.serializer      = 'php'

kvs.ssdb.driver             = 'ssdb'
kvs.ssdb.host               = '127.0.0.1'
kvs.ssdb.port               = '8888'
kvs.ssdb.timeout            = 2
kvs.ssdb.auth               = ''
```

## 如何使用
```php
<?php
use Libyaf\Kvs\Kvs;

$result = Kvs::ins()->set('name', 'qinyuguang');
$result = Kvs::ins()->get('name', 'qinyuguang');

//通过判断driver类型，可以使用对应driver的独有方法
$driver = Kvs::ins()->getDriverType();

//连接检查及重连
if (! Kvs::ins()->connected()) {
    Kvs::ins()->reconnect();
}

// ...
```

## Method

> 支持大部分phpredis的写法 redis和ssdb的通用方法，详见 Kvs\Driver\Type

> 另外，如果driver的可用方法，不包含在抽象方法中，也是可以直接使用的。

> **建议先判断当前driver，再使用独有方法，以增加代码健壮性**

## 更多参考
- <a href="https://github.com/phpredis/phpredis" target="_blank">phpredis 文档</a>

- <a href="http://ssdb.io/docs/zh_cn/php/index.html" target="_blank">ssdb 文档</a>
