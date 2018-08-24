<?php
namespace Libyaf\Sdk;

class User extends Base\Client
{
    /**
     * @brief 匿名登录
     *
     * @param string $uuid 唯一标识
     *
     * @return array
     */
    public function anonymous($uuid)
    {
        if (! $uuid) {
            return [];
        }

        $params = [
            'user_name' => $uuid,
        ];

        $data = $this->post('login', 'login', $params);

        return json_decode($data, true);
    }

    /**
     * @brief 发送短信验证码
     *
     * @param string $phoneNumber 手机号码
     * @param string $sessionId
     *
     * @return array
     */
    public function captcha($phoneNumber, $sessionId = '')
    {
        if (! $sessionId) {
            $sessionId = $this->getSessionId();
        }

        if (! $phoneNumber || ! $sessionId) {
            return [];
        }

        $params = [
            'phone_number' => $phoneNumber,
        ];

        $this->setCookies(['S' => $sessionId]);

        $data = $this->get('sms', 'sendsms', $params);

        return json_decode($data, true);
    }

    /**
     * @brief 手机登录
     *
     * @param string $phoneNumber   手机号码
     * @param string $verfCode      短信验证码
     * @param string $sessionId
     *
     * @return array
     */
    public function phoneLogin($phoneNumber, $verfCode, $sessionId = '')
    {
        if (! $sessionId) {
            $sessionId = $this->getSessionId();
        }

        if (! $phoneNumber || ! $verfCode || ! $sessionId) {
            return [];
        }

        $params = [
            'user_name'   => $phoneNumber,
            'verify_code' => $verfCode,
        ];

        $this->setCookies(array('S' => $sessionId));

        $data = $this->post('login', 'login', $params);

        return json_decode($data, true);
    }

    /**
     * @brief 获取用户信息
     *
     * @param string $sessionId
     *
     * @return array
     */
    public function getUserInfo($sessionId = '')
    {
        if (! $sessionId) {
            $sessionId = $this->getSessionId();
        }

        if (! $sessionId) {
            return [];
        }

        $this->setCookies(['S' => $sessionId]);

        $data = $this->get('member', 'getmemberinfo');

        return json_decode($data, true);
    }

    /**
     * @brief  通过UID获取用户信息
     *
     * @param int $uid 用户ID
     *
     * @return array
     */
    public function getUserInfoByUid($uid)
    {
        $params = [
            'uid' => $uid,
        ];

        $data = $this->get('member', 'getmember', $params);

        return json_decode($data, true);
    }

    private function getSessionId()
    {
        return \Yaf\Dispatcher::getInstance()->getRequest()->getCookie('S');
    }

}
