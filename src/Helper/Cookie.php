<?php
namespace Libyaf\Helper;

class Cookie {

    public static $salt = null;

    public static $expiration = 0;

    public static $path = '/';

    public static $domain = null;

    public static $secure = false;

    public static $httponly = false;

    public static function get($key, $default = null)
    {
        if (! isset($_COOKIE[$key])) {
            return $default;
        }

        $cookie = $_COOKIE[$key];

        $split  = strlen(Cookie::salt($key, null));

        if (isset($cookie[$split]) && $cookie[$split] === '~') {
            list ($hash, $value) = explode('~', $cookie, 2);

            if (Cookie::salt($key, $value) === $hash) {
                return $value;
            }

            Cookie::delete($key);
        }

        return $default;
    }

    public static function set($name, $value, $expiration = null)
    {
        if ($expiration === null) {
            $expiration = Cookie::$expiration;
        }

        if ($expiration !== 0) {
            $expiration += time();
        }

        $value = Cookie::salt($name, $value).'~'.$value;

        return setcookie($name, $value, $expiration, Cookie::$path, Cookie::$domain, Cookie::$secure, Cookie::$httponly);
    }

    public static function delete($name)
    {
        unset($_COOKIE[$name]);

        return setcookie($name, null, -86400, Cookie::$path, Cookie::$domain, Cookie::$secure, Cookie::$httponly);
    }

    public static function salt($name, $value)
    {
        if (! Cookie::$salt) {
            throw new Exception('A valid cookie salt is required. Please set Cookie::$salt.');
        }

        $agent = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : 'unknown';

        return sha1($agent.$name.$value.Cookie::$salt);
    }

}

