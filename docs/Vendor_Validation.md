## 简介
> respect/validation是一款极其强大且优雅易用的验证库
>
> 可满足各种验证需求，包括表单验证等

## 使用方法
```php
<?php
use Respect\Validation\Validator as v;

//数字校验
$number = 123;
v::numeric()->validate($number); //true

//字符串类型、长度校验
$usernameValidator = v::alnum()->noWhitespace()->length(1,15);
$usernameValidator->validate('alganet'); //true

//对象属性校验
$user = new stdClass;
$user->name = 'Alexandre';
$user->birthdate = '1987-07-01';

$userValidator = v::attribute('name', v::string()->length(1,32))
                  ->attribute('birthdate', v::date()->age(18));
$userValidator->validate($user); //true
```

## 更多参考
- <a href="http://respect.github.io/Validation/" target="_blank">Validation 文档</a>
