<?php
namespace Libyaf\Sdk;

class Sms extends Base\Client
{
    /**
     * @brief 发送短信
     *
     * @param mix    $number     单个或多个手机号
     * @param string $template   模板名称如captcha
     * @param array  $context    模板上下文变量
     * @param string $group      来源如betime
     *
     * @return array 执行结果
     */
    public function send($number, $template, $context, $group)
    {
        $numbers    = is_array($number) ? $number : [$number];

        $params     = [
            'numbers'   => $numbers,
            'template'  => $template,
            'context'   => $context,
            'group'     => $group,
        ];

        $data = $this->post('message', 'send', $params);

        return json_decode($data, true);
    }

}


