<?php
namespace Libyaf\Helper;

use Libyaf\Session\Session;

class Security
{
    //同时允许的最大有效token数
    const MAX_TOKEN_NUM = 10;

    //session name
    const TOKEN_NAME    = 'securityToken';

    /**
        * @brief 生成一个有效token，并存入session
        *
        * @return string
     */
    public static function token()
    {
        //生成token
        $token = sha1(uniqid(null, true));

        //读取已存在token
        $exist = Session::ins()->get(self::TOKEN_NAME);

        $exist = $exist ? : [];

        //超过最大有效token数，移出第一个token
        if (count($exist) >= self::MAX_TOKEN_NUM) {
            array_shift($exist);
        }

        array_push($exist, $token);

        //保存token
        Session::ins()->set(self::TOKEN_NAME, $exist);

        //立即写入session
        Session::ins()->write();

        return $token;
    }

    /**
        * @brief 校验是否有效token
        *
        * @param $token string 待检查token
        *
        * @return boolean
     */
    public static function check($token)
    {
        //读取已存在token
        $exist = Session::ins()->get(self::TOKEN_NAME);

        if (! $exist || ! is_array($exist)) {
            return false;
        }

        $key = array_search($token, $exist, true);

        if ($key !== false) {
            //校验成功删除对应token
            unset($exist[$key]);

            //保存token
            Session::ins()->set(self::TOKEN_NAME, $exist);

            return true;
        } else {
            return false;
        }
    }

}

