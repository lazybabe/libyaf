<?php
namespace Libyaf\Helper;

class Url
{
    /**
     * @brief 检查URL的域名合法性
     *
     * @param $url       string          待检查URL
     * @param $domain    string or array 可信任域名
     *
     * @return boolean
     */
    public static function checkDomain($url, $domain)
    {
        $host = parse_url($url, PHP_URL_HOST);

        if (is_string($domain)) {
            return self::handle($host, $domain);
        } elseif (is_array($domain)) {
            foreach ($domain as $item) {
                $result = self::handle($host, $item);
                if ($result === true) {
                    return true;
                }
            }
            return false;
        }
    }

    private static function handle($host, $domain)
    {
        if ($domain[0] === '.') {
            $domain = substr($domain, 1);
        }

        $domainWithDot = '.'.$domain;

        $lenth = strlen($domainWithDot);

        if ($host == $domain || substr($host, -$lenth) == $domainWithDot) {
            return true;
        }

        return false;
    }

}

