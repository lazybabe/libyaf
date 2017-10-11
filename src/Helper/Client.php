<?php
namespace Libyaf\Helper;

class Client
{
    //GeoIp数据库,需单独安装
    const GEOIP_CITY_DB = '/usr/local/GeoIP/GeoLite2-City.mmdb';

    //可信代理
    public static $trustedProxies = [
        '127.0.0.1',
        'localhost',
    ];

    /**
        * @brief 获取客户端IP
        *
        * @param $from  string  指定header名的值作为客户端IP
        *
        * @return string
     */
    public static function getIp($from = null)
    {
        $clientIp = '0.0.0.0';

        do {
            //指定头信息作为客户端IP,比如经过CDN的请求
            if ($from !== null && isset($_SERVER[$from])) {
                $clientIp = $_SERVER[$from];
                break;
            }

            //获取经过信任代理的客户端IP
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])
                && isset($_SERVER['REMOTE_ADDR'])
                && in_array($_SERVER['REMOTE_ADDR'], self::$trustedProxies)
            ) {
                $clientIps  = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $clientIp   = array_shift($clientIps);
                break;
            }

            if (isset($_SERVER['HTTP_CLIENT_IP'])
                && isset($_SERVER['REMOTE_ADDR'])
                && in_array($_SERVER['REMOTE_ADDR'], self::$trustedProxies)
            ) {
                $clientIps  = explode(',', $_SERVER['HTTP_CLIENT_IP']);
                $clientIp   = array_shift($clientIps);
                break;
            }

            if (isset($_SERVER['REMOTE_ADDR'])) {
                $clientIp = $_SERVER['REMOTE_ADDR'];
                break;
            }
        } while(false);

        return $clientIp;
    }

    /**
        * @brief 使用GeoIp查询客户端国家、城市
        *
        * @param $ip    string  IP地址
        *
        * @return array
     */
    public static function getCity($ip)
    {
        if (! is_file(self::GEOIP_CITY_DB) || ! is_readable(self::GEOIP_CITY_DB)) {
            throw new \Exception('GEO数据库未找到');
        }

        try {
            $reader = new \GeoIp2\Database\Reader(self::GEOIP_CITY_DB);

            $record = $reader->city($ip);

            return [
                'country'   => $record->country->names['zh-CN'],
                'city'      => $record->city->names['zh-CN'],
            ];
        } catch (\Exception $e) {
            return [
                'country'   => '火星',
                'city'      => '',
            ];
        }
    }

    /**
        * @brief 网络匹配,支持CIDR掩码
        *
        * @param $ip    string  IP
        * @param $cidr  string  IP或CIDR
        *
        * @return boolean
     */
    public static function netMatch($ip, $cidr)
    {
        list ($net, $mask) = explode ('/', $cidr);

        if ($mask === null) {
            return ($ip == $cidr);
        }

        return (ip2long ($ip) & ~((1 << (32 - $mask)) - 1)) == ip2long($net);
    }

}

