## 简介
> 令牌桶，适用限流场景，可以独立使用

## 配置令牌
对令牌周期、令牌数量、超时时间进行配置
```php
<?php
use Libyaf\Limiter\Token;

// 设置n秒产生x个token，默认3600秒过期
$token = new Token($n, $x, 3600);

```
## 配置令牌桶
令牌桶支持多驱动存储，扩展storage适配器即可
```php
<?php
use Libyaf\Limiter\Bucket;
use Libyaf\Limiter\Storage\Apcu;

// 使用apcu存储token数据
$storage = new Apcu();

// 创建token bucket, 按scope分桶
$bucket = new Bucket($scope, $token, $storage);

```
## 使用令牌桶做限流判断
```php
<?php
// 消费n个令牌, 可以按id再次分桶
if ($bucket->consume($n, $id)) {
    // 消费成功，正常执行
} else {
    // 消费失败，被限制
}
```
