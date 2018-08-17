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
    public function send($number, $template, array $context = [], $group = 'betime')
    {
        $numbers = is_array($number) ? implode(',', $number) : $number;

        $params = [
            'numbers'   => $numbers,
            'template'  => $template,
            'context'   => json_encode($context),
            'group'     => $group,
        ];

        $data = $this->post('message', 'send', $params);

        return json_decode($data, true);
    }

    /**
     * @brief 发送推送
     *
     * @param string $content    发送内容
     * @param string $group      配置组如betime
     * @param string $extras     自定义设置 如 alias|extras|title
     *
     * @return array 执行结果
     */
    public function push($content, $group = 'betime', array $extras = [])
    {
        $params = [
            'content'   => $content,
            'group'     => $group,
        ];

        $params = $params + $extras;

        if ($extras['alias']) {
            if (is_string($extras['alias'])) {
                $extras['alias'] = [$extras['alias']];
            }
            $params['alias'] = json_encode($extras['alias']);
        }

        if ($extras['extras'] && is_array($extras['extras'])) {
            $params['extras'] = json_encode($extras['extras']);
        }

        $data = $this->post('push', 'send', $params);

        return json_decode($data, true);
    }

}


