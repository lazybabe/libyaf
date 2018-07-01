## 简介
> 分布式ID生成器。基于 <a href="https://developer.twitter.com/en/docs/basics/twitter-ids.html" target="_blank">Snowflake</a> 算法。

## 配置
**application.ini**
```ini
id.default.driver           = 'snowflake'
id.default.datacenterid     = 1
; id.default.workerid       = 0 // 非必须配置中指定，可以在构造时传入自定义config
```

## 如何使用
```php
<?php
use Libyaf\Id\Id;

$config = [
    'workerid' => 123,
];

// 自定义workerid生成ID
Id::ins(null, $config)->getNextId();

// 默认配置生成ID
Id::ins()->getNextId();
```

## Method
- **getNextId**

