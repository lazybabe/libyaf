<?php
namespace Libyaf\Sdk;

class Coin extends Base\Client
{
    /**
     * @brief 注册预警设置
     *
     * @param array $terms 预警设置
     *
     * @return array 执行结果
     */
    public function termReg($terms)
    {
        $params = [
            'terms' => json_encode($terms),
        ];

        $data = $this->request('POST', 'pre_warning_add', $params);

        return json_decode($data, true);
    }

}


