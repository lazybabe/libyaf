<?php
namespace Libyaf\Sdk;

class Journal extends Base\Client
{
    /**
     * @brief 通用打点
     *
     * @param array $data 打点数据
     *
     * @return array
     */
    public function general($data)
    {
        $params = [
            'data' => json_encode($data),
        ];

        $data = $this->post('general', '', $params);

        return json_decode($data, true);
    }

}

