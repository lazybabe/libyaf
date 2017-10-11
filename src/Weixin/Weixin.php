<?php
namespace Libyaf\Weixin;

use Libyaf\Weixin\Exception;
use Libyaf\Weixin\Wechat;

class Weixin
{
    private static $instances = [];

    public static function ins($group = null)
    {
        if (! isset($group)) {
        	$group = 'default';
        }

        if (isset(Weixin::$instances[$group])) {
            return Weixin::$instances[$group];
        };

        $config = \Yaf\Application::app()->getConfig()->weixin;
        $config = isset($config) ? $config->$group : null;

        if (! isset($config)) {
        	throw new Exception('Failed to load Weixin group: '.$group);
        }

        $wechatConfig = [
            'token'             => $config->token,
            'encodingaeskey'    => $config->aesKey,
            'appid'             => $config->appID,
            'appsecret'         => $config->appSecret,
        ];

        Weixin::$instances[$group] = new Wechat($wechatConfig);

        return Weixin::$instances[$group];
    }

}

