<?php
namespace Libyaf\Sdk;

class Channel extends Base\Client
{
    /**
     * @brief 注册预警设置
     *
     * @param int $uid      用户ID
     * @param mix $message  推送消息
     *
     * @return array 执行结果
     */
    public function push($uid, $message)
    {
        if (is_array($message)) {
            $message = json_encode($message);
        }

        $params = [
            'cmd' => '1',
            'uid' => $uid,
            'msg' => $message,
        ];

        $data = $this->request('POST', 'push', $params);

        return json_decode($data, true);
    }
}

