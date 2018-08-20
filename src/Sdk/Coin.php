<?php
namespace Libyaf\Sdk;

class Coin extends Base\Client
{
    /**
     * @brief 注册预警设置
     *
     * @param array $terms 预警设置
     *
     * @return array
     */
    public function termReg($terms)
    {
        $params = [
            'terms' => json_encode($terms),
        ];

        $data = $this->request('POST', 'pre_warning_add', $params);

        return json_decode($data, true);
    }

    /**
     * @brief 获取币种信息
     *
     * @param mix $symbol 币名
     *
     * @return array
     */
    public function info($symbol)
    {
        if (is_array($symbol)) {
            $symbol = implode(',', $symbol);
        }

        $params = [
            'symbol' => $symbol,
        ];

        $data = $this->request('GET', 'coin_info', $params);

        return json_decode($data, true);
    }

}


