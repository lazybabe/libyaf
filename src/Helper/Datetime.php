<?php
namespace Libyaf\Helper;

class Datetime
{
    /**
        * @brief 比较时间，获得两个时间的关系
        *
        * @param $target    string  目标时间
        * @param $origin    string  源时间，默认当前时间。
        * @param $level     string  比较级别，超过级别返回目标时间。可选y|m|d|h|i|s
        * @param $format    string  超出级别返回的时间格式
        *
        * @return string
     */
    public static function relative($target, $origin = 'Now', $level = 'd', $format = null)
    {
        $map = [
            'y' => 1,
            'm' => 2,
            'd' => 3,
            'h' => 4,
            'i' => 5,
            's' => 6,
        ];
        $level = isset($map[$level]) ? $map[$level] : 0;

        $datetime1  = new \DateTime($target);
        $datetime2  = new \DateTime($origin);
        if ($datetime1 == $datetime2) {
            return '1秒前';
        }

        $interval   = $datetime1->diff($datetime2);
        $suffix     = $interval->invert ? '后' : '前';

        do {
            if ($interval->y >= 1) {
                if ($level > $map['y']) {
                    break;
                }
                return $interval->y.'年'.$suffix;
            }

            if ($interval->m >= 1) {
                if ($level > $map['m']) {
                    break;
                }
                return $interval->m.'月'.$suffix;
            }

            if ($interval->d >= 1) {
                if ($level > $map['d']) {
                    break;
                }
                return $interval->d.'天'.$suffix;
            }

            if ($interval->h >= 1) {
                if ($level > $map['h']) {
                    break;
                }
                return $interval->h.'小时'.$suffix;
            }

            if ($interval->i >= 1) {
                if ($level > $map['i']) {
                    break;
                }
                return $interval->i.'分钟'.$suffix;
            }

            if ($interval->s >= 1) {
                if ($level > $map['s']) {
                    break;
                }
                return $interval->s.'秒'.$suffix;
            }
        } while (false);

        if ($format) {
            return $datetime1->format($format);
        }

        return $target;
    }

}

