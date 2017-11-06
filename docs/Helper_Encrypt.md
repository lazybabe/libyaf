## 简介
> 提供对称加密工具，根据配置可以指定不同加密模式和算法

## 配置
**application.ini**
```ini
; 密钥
encrypt.default.key      = 'xxxxxxxxxxxxxxxxxxxxxxxxxxx'

; 加密模式，默认nofb
encrypt.default.mode     = MCRYPT_MODE_NOFB

; 加密算法，默认rijndael-128
encrypt.default.cipher   = MCRYPT_RIJNDAEL_128
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
