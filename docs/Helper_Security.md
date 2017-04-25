## 简介

> Security提供生成及校验token的功能，用来防止CSRF攻击，支持10个有效token
>
> 依赖于<a href="http://gitlab.alibaba-inc.com/gaode.search/libyaf/wikis/Session" target="_blank">Session</a>，请注意配置

## token
```php
/**
    * @brief 生成一个有效token，并存入session
    *
    * @return string
 */
public static function token()
```

## check
```php
/**
    * @brief 校验是否为有效token
    *
    * @param $token string 待检查token
    *
    * @return boolean
 */
public static function check($token)
```
