<?php
namespace Libyaf\Limiter;

class Bucket
{
    private $scope;

    private $token;

    private $storage;

    private $logger;

    /**
     * @brief 创建一个token bucket
     *
     * @param string    $scope      token作用域
     * @param Token     $token      token实例
     * @param Storage   $storage    存储实例
     *
     * @return
     */
    public function __construct($scope, Token $token, Storage\Storage $storage, Psr\Log\LoggerInterface $logger = null)
    {
        $this->scope    = $scope;
        $this->token    = $token;
        $this->storage  = $storage;
        $this->logger   = $logger;
    }

    /**
     * @brief 消费token
     *
     * @param float     $tokens   消费token数量
     * @param string    $id       独立标识, 如客户端IP等
     *
     * @return bool 是否成功消费
     */
    public function consume($tokens = 1.0, $id = '')
    {
        $timeKey    = $this->getKey('time', $id);
        $tokensKey  = $this->getKey('tokens', $id);

        $recent     = $this->storage->fetch($timeKey);
        $current    = time();

        // 没有记录或最近记录超过一个时间周期, 刷新token
        if ($recent === false || ($current - $recent) > $this->token->period) {
            // 消耗过大, 用掉周期内全部token
            if ($tokens > $this->token->tokens) {
                $tokens = $this->token->tokens;
            }

            $this->storage->save($timeKey, time(), $this->token->ttl);
            $this->storage->save($tokensKey, $this->token->tokens - $tokens, $this->token->ttl);

            return true;
        } else {
            $left = $this->storage->decr($tokensKey, $tokens);

            if ($left < 0) {
                // 如果消费前token仍有剩余, 恢复消费前token数量
                // 更符合逻辑的做法是把消费的token全部进行恢复, 以免并发时不能正常恢复剩余
                // 但为了减少存储的操作次数，故判断如果原来有剩余才进行恢复
                $remain = $left + $tokens;
                if ($remain > 0) {
                    $this->storage->incr($tokensKey, $tokens);
                }

                return false;
            } else {
                return true;
            }
        }
    }

    private function getKey($type, $id)
    {
        return md5('tokenbucket:'.$this->scope.':'.$type.':'.$id);
    }

}

