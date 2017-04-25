## 简介
> Text 提供字符串的相关方法
>
> 详细使用方法见注释

## random
生成随机字符串
```php
/**
    * @brief 根据指定的类型和长度，生成随机字符串
    *
    * 支持如下类型的字符池
    *  alnum:   大小写字母和数字
    *  alpha:   大小写字母
    *  hexdec   十六进制字符
    *  numeric  纯数字
    *  nozero   非0数字
    *  distinct 不易混淆的大写字母和数字
    *
    * @param $type      mix     字符池类型，或传入一个字符串作为字符池
    * @param $length    int     返回字符串长度
    *
    * @return string
 */
public static function random($type = null, $length = 8)
```
