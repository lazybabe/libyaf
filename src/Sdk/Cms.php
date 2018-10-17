<?php
namespace Libyaf\Sdk;

class Cms extends Base\Client
{
    /**
     * @brief 获取币种信息
     *
     * @param mix $symbol 币名
     *
     * @return array
     */
    public function coinInfo($symbol)
    {
        if (is_array($symbol)) {
            $symbol = implode(',', $symbol);
        }

        $params = [
            'symbol' => $symbol,
        ];

        $data = $this->get('coin', 'list', $params);

        return json_decode($data, true);
    }

}

