<?php
namespace Libyaf\Sdk;

class Scout extends Base\Client
{
    /**
     * @brief 按登录用户和预警标识删除预警设置
     *
     * @param string $tag 预警标识
     *
     * @return array 执行结果
     */
    public function del($tag)
    {
        $params = [
            'tag' => $tag,
        ];

        $this->setCookies(['S'=>$this->getSessionId()]);

        $data = $this->post('config', 'del', $params);

        return json_decode($data, true);
    }

    private function getSessionId()
    {
        return \Yaf\Dispatcher::getInstance()->getRequest()->getCookie('S');
    }

}

