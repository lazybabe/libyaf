<?php
namespace Libyaf\Limiter;

class Token
{
    // 令牌周期，单位秒
    public $period;

    // 周期内令牌数
    public $tokens;

    // 过期时间
    public $ttl;

    public function __construct($period, $tokens, $ttl = 3600)
    {
        $this->period   = intval($period);
        $this->tokens   = intval($tokens);
        $this->ttl      = intval($ttl);

        // 数据在令牌周期内不会过期
        if ($this->ttl < $this->period) {
            $this->ttl = $this->period;
        }
    }

}

