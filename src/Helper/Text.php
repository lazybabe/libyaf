<?php
namespace Libyaf\Helper;

class Text
{
    /**
     * @brief 根据指定的类型和长度，生成随机字符串
     *
     * 支持如下类型的字符池
     *  alnum:   大小写字母和数字
     *  alpha:   大小写字母
     *  hexdec   十六进制字符
     *  numeric  纯数字
     *  nozero   非0数字
     *  distinct 不易混淆的大写字母和数字
     *
     * @param $type      mix     字符池类型，或传入一个字符串作为字符池
     * @param $length    int     返回字符串长度
     *
     * @return string
     */
    public static function random($type = null, $length = 8)
    {
        //默认alnum字符池
        if ($type === null) {
            $type = 'alnum';
        }

        //初始化字符池
        switch ($type)
        {
            case 'alnum':
                $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            case 'alpha':
                $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            case 'hexdec':
                $pool = '0123456789abcdef';
                break;
            case 'numeric':
                $pool = '0123456789';
                break;
            case 'nozero':
                $pool = '123456789';
                break;
            case 'distinct':
                $pool = '2345679ACDEFHJKLMNPRSTUVWXYZ';
                break;
            default:
                $pool = (string) $type;
                break;
        }

        $pool = str_split($pool, 1);

        $max = count($pool) - 1;

        //随机从字符池中取足所需字符
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= $pool[mt_rand(0, $max)];
        }

        //alnum类型，保证至少有一个数字或字母
        if ($type === 'alnum' && $length > 1) {
            if (ctype_alpha($str)) { //纯字母随机增加一个数字
                $str[mt_rand(0, $length - 1)] = chr(mt_rand(48, 57));
            } elseif (ctype_digit($str)) {
                //纯数字随机增加一个字母
                $str[mt_rand(0, $length - 1)] = chr(mt_rand(65, 90));
            }
        }

        return $str;
    }

}

