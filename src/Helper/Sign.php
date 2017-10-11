<?php
namespace Libyaf\Helper;

class Sign
{
    public static $key = '';

    public static function checkMd5(array $data, $name = 'sign')
    {
        if (! isset($data[$name])) {
            return false;
        }

        $sign = $data[$name];

        unset($data[$name]);

        ksort($data);

        $query  = http_build_query($data, '', '&');
        $query  = urldecode($query);
        $query  = $query.self::$key;

        $md5    = md5($query);

        return ($sign == $md5);
    }

}

