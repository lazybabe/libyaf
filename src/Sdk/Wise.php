<?php
namespace Libyaf\Sdk;

class Wise extends Base\Client
{
    public function aiResult($params = [])
    {
        if (is_array($params['symbol'])) {
            $params['symbol'] = implode(',', $params['symbol']);
        }

        $data = $this->get('ai', 'result', $params);

        return json_decode($data, true);
    }

    public function effectAnalysis($params = [])
    {
        $data = $this->get('effect', 'analysis', $params);

        return json_decode($data, true);
    }

}

