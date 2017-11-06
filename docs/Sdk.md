## 简介
> 基于guzzle http封装的通用http client，可以独立使用

## SDK 配置
对基础URI、请求超时、代理、认证、日志进行配置
```php
<?php
use Libyaf\Sdk\Base\Conf;
use Libyaf\Logkit\Logger;

$options = [
    'baseUri'   => 'http://api.sign.com',           //基础URI
    'timeout'   => 3.0,                             //超时时间，允许小数
    'proxy'     => 'http://127.0.0.1:8086',         //支持代理
    'auth'      => ['user'=>'meuc', 'pass'=>'***'], //基础认证
    'logger'    => Logger::ins('sign'),             //基于PSR3的logger
];

$conf = new Conf($options);
```
## HTTP Client
HTTP Client，可以自定义头信息和cookie，HTTP方法支持GET、POST、PUT等
```php
<?php
use Libyaf\Sdk\Base\Client;

$caller = '****';
$client = new Client($conf, $caller);

//发起GET请求，resource=sign，operate=info，GET参数user=abc&service=wechat
$client->get('sign', 'info', ['user'=>'abc', 'service'=>'wechat']);

//发起POST请求，resource=sign，operate=info，POST参数user=abc&service=wechat
$client->post('sign', 'info', ['user'=>'abc', 'service'=>'wechat']);

//发起PUT请求，request_uri=sign/info，请求body为user=abc&service=wechat
$client->request('PUT', 'sign/info', ['user'=>'abc', 'service'=>'wechat']);
```
