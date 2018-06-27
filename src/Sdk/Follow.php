<?php
namespace Libyaf\Sdk;

class Follow extends Base\Client
{
    /**
     * @brief 添加关注
     *
     * @param int       $uid    用户ID
     * @param int       $type   关注类型
     * @param string    $key    关注标识
     * @param string    $source 来源
     *
     * @return array 执行结果
     */
    public function add($uid, $type, $key, $source = '')
    {
        $params = [
            'follow_key'    => $key,
            'source'        => $source,
            'type'          => $type,
            'uid'           => $uid,
        ];

        $data = $this->post('follow', 'add', $params);

        return json_decode($data, true);
    }

    /**
     * @brief 获取关注列表
     *
     * @param int $uid    用户ID
     * @param int $type   关注类型
     *
     * @return array 执行结果
     */
    public function list($uid, $type)
    {
        $params = [
            'type'  => $type,
            'uid'   => $uid,
        ];

        $data = $this->get('follow', 'list', $params);

        return json_decode($data, true);
    }

}

