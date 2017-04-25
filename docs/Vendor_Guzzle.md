## 简介
> guzzlehttp/guzzle是一个基于PSR7规范的HTTP client库，极大的简化了对RESTfull web服务的调用过程。
>
> 支持get、post、put、delete等各种方法，支持同步和异步请求

## 如何使用
```php
<?php
use GuzzleHttp\Client;

$client     = new Client();
$response   = $client->get('http://www.me.cn/');

$code       = $response->getStatusCode();
$body       = $response->getBody();
```

## 更多参考
- <a href="http://docs.guzzlephp.org/en/latest/index.html" target="_blank">Guzzle 文档</a>
