<?php
namespace Libyaf\Sdk;

class Wise extends Base\Client
{
    public function aiResult()
    {
        $data = $this->get('ai', 'result');

        return json_decode($data, true);
    }

    public function effectAnalysis($params = [])
    {
        $data = $this->get('effect', 'analysis', $params);

        return json_decode($data, true);
    }

}

