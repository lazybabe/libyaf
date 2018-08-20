## 简介
> 提供对称加密工具，根据配置可以指定不同加密模式和算法

## 配置
**application.ini**
```ini
; 密钥
encrypt.default.key     = 'xxxxxxxxxxxxxxxxxxxxxxxxxxx'

; 加密算法，默认AES-256-ECB
encrypt.default.method  = 'AES-256-ECB'

; 加密options，默认0
encrypt.default.options = OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING

; 加密初始化向量，默认空
encrypt.default.iv      = ''
```

## 如何使用
```php
<?php
use Libyaf\Helper\Encrypt;

$encrypt = Encrypt::ins();

$raw = 'qinyuguang';

$encode = $encrypt->encode($raw);

$decode = $encrypt->decode($encode);

```
