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

    /**
     * @brief 获取合约信息
     *
     * @param array $data 请求数据
     *
     * @return array
     */
    public function futrueInfo($data)
    {
        if (is_array($data)) {
            $data = json_encode($data);
        }

        $params = [
            'coin_infos' => $data,
        ];

        $data = $this->request('POST', 'ccnm', $params);

        return json_decode($data, true);
    }

    /**
     * @brief 获取币种历史价格
     *
     * @param string    $symbol     币种标志
     * @param int       $timestamp  时间戳
     *
     * @return array
     */
    public function historyPrice($symbol, $timestamp)
    {
        $params = [
            'asset' => $symbol,
            'ts'    => $timestamp,
        ];

        $data = $this->request('GET', 'history_coin_price', $params);

        return json_decode($data, true);
    }

}


