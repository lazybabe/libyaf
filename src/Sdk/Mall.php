<?php
namespace Libyaf\Sdk;

class Mall extends Base\Client
{
    public function permission($uid)
    {
        $params = [
            'uid' => $uid,
        ];

        $data = $this->get('product', 'permission', $params);

        $result = json_decode($data, true);

        return ($result['errno'] === 0) ? $result['data'] : [];
    }

    public function checkPermission($uid, $permission)
    {
        $list = $this->permission($uid);

        return $list[$permission] ?? false;
    }

}


