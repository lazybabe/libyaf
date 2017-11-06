## 简介
> 支持多种方式驱动的Session工具，目前支持cache驱动。
>
> `TODO` 驱动扩展 && session加密

## 配置
**application.ini**
```ini
; 使用cache驱动
session.default.driver      = 'cache'

; cache组
session.default.cache       = 'default'

; session name
session.default.name        = 'MYSESSION'

; session有效时间
session.default.lifetime    = 7200

; session加密组
session.default.encrypt     = 'default'
```

## 如何使用
```php
<?php
use Libyaf\Session\Session;

$session = Session::ins();

$session->set('name', 'qinyuguang');

$session->get('name');

$session->getOnce('name');

$session->delete('name', 'name2', 'name3');
```
