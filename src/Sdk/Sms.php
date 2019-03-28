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
     * @param string $ip         来源IP
     *
     * @return array 执行结果
     */
    public function send($number, $template, array $context = [], $group = 'betime', $ip = '')
    {
        $numbers = is_array($number) ? implode(',', $number) : $number;

        $params = [
            'numbers'   => $numbers,
            'template'  => $template,
            'context'   => json_encode($context),
            'group'     => $group,
            'ip'        => $ip,
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

        $params['alias']    = $this->param2json($extras['alias']);
        $params['tag']      = $this->param2json($extras['tag']);
        $params['tag_and']  = $this->param2json($extras['tag_and']);
        $params['tag_not']  = $this->param2json($extras['tag_not']);
        $params['extras']   = $extras['extras'] ? json_encode($extras['extras']) : null;

        $params = array_filter($params, function($val){
            return ($val !== null);
        });

        $data = $this->post('push', 'send', $params);

        return json_decode($data, true);
    }

    private function param2json($value)
    {
        if ($value) {
            if (is_string($value)) {
                $value = [$value];
            }
            return json_encode($value);
        }

        return null;
    }

}


