<?php
namespace Libyaf\Sdk;

class Point extends Base\Client
{
    public function getPoint($uid)
    {
        $params = [
            'uid' => $uid,
        ];

        $data = $this->get('user', 'point', $params);

        return json_decode($data, true);
    }

    public function getLevel($uid)
    {
        $params = [
            'uid' => $uid,
        ];

        $data = $this->get('user', 'level', $params);

        return json_decode($data, true);
    }

    public function consume($uid, $point, $context)
    {
        $params = [
            'uid'       => $uid,
            'point'     => $point,
            'context'   => is_array($context) ? json_encode($context) : $context,
        ];

        $data = $this->post('user', 'consume', $params);

        return json_decode($data, true);
    }

    public function increase($uid, $point, $symbol, $context)
    {
        $params = [
            'uid'       => $uid,
            'point'     => $point,
            'symbol'    => $symbol,
            'context'   => is_array($context) ? json_encode($context) : $context,
        ];

        $data = $this->post('user', 'increase', $params);

        return json_decode($data, true);
    }

}


