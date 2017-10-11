<?php
namespace Libyaf\Sdk\Base;

use Respect\Validation\Validator as V;
use Libyaf\Helper\Arr;

class Conf
{
    //基础URI
    public $baseUri = '';

    //超时秒数,支持小数
    public $timeout = 2.0;

    //代理
    public $proxy;

    //基础认证信息
    public $auth = ['user'=>'', 'pass'=>''];

    //日志实体
    public $logger;

    public function __construct(array $options)
    {
        $options = [
            'baseUri'   => Arr::get($options, 'baseUri', $this->baseUri),
            'timeout'   => Arr::get($options, 'timeout', $this->timeout),
            'proxy'     => Arr::get($options, 'proxy', $this->proxy),
            'auth'      => Arr::get($options, 'auth', $this->auth),
            'logger'    => Arr::get($options, 'logger'),
        ];

        try {
            V::arrayVal()
                ->key('baseUri', V::url()->notEmpty())
                ->key('timeout', V::floatVal()->min(0))
                ->key('proxy', V::optional(V::url()))
                ->key(
                    'auth',
                    V::arrayVal()
                    ->key('user', V::stringType())
                    ->key('pass', V::stringType())
                )
                ->key('logger', V::instance('\Psr\Log\LoggerInterface'))
                ->assert($options);
        } catch (\InvalidArgumentException $e) {
            $errors = array_filter($e->findMessages([
                'baseUri'   => 'Required correct baseUri',
                'timeout'   => 'Required correct timeout',
                'proxy'     => 'Required correct proxy',
                'auth'      => 'Required correct authuser',
                'logger'    => 'Required a logger instance of psr\log',
            ]));
            $errmsg = array_shift($errors);
            throw new Exception($errmsg);
        }


        $this->baseUri  = $options['baseUri'];
        $this->timeout  = $options['timeout'];
        $this->proxy    = $options['proxy'];
        $this->auth     = $options['auth'];
        $this->logger   = $options['logger'];
    }

}

