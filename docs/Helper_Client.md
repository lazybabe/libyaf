## 简介
> Client 客户端辅助工具，如获取IP、获取国家城市、网络匹配等功能

## getIp
获取客户端IP

可以通过传参指定头信息，来获取经过CDN的请求客户端IP
```php
/**
    * @brief 获取客户端IP
    *
    * @param $from  string  指定header名的值作为客户端IP
    *
    * @return string
 */
public static function getIp($from = null)
```

## getCity
获取IP所属国家和城市

PS: 依赖于GeoIP数据库，需要单独安装。安装路径：/usr/local/GeoIP/GeoLite2-City.mmdb
```php
/**
    * @brief 使用GeoIp查询客户端国家、城市
    *
    * @param $ip    string  IP地址
    *
    * @return array
 */
public static function getCity($ip)
```

## netMatch
网络匹配

如：netMatch('192.168.0.100', '192.168.0.0/24') = true
```php
/**
    * @brief 网络匹配,支持CIDR掩码
    *
    * @param $ip    string  IP
    * @param $cidr  string  IP或CIDR
    *
    * @return boolean
 */
public static function netMatch($ip, $cidr)
```
