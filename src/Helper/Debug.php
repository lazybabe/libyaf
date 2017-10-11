<?php
namespace Libyaf\Helper;

class Debug
{
    /**
        * @brief 调试输出变量
        *
        * @param ...$params
        *
        * @return void
     */
    public static function vars(...$params)
    {
        $isCli  = \Yaf\Dispatcher::getInstance()->getRequest()->isCli();

        echo $isCli ? PHP_EOL : '<br>';
        echo '*****************************DEBUG::VARS******************************';
        echo $isCli ? PHP_EOL : '<br>'.PHP_EOL;
        echo $isCli ? '' : '<pre>'.PHP_EOL;

        foreach ($params as $item) {
            var_dump($item);
        }

        $trace = debug_backtrace();

        echo $isCli ? '' : '</pre>'.PHP_EOL;
        echo "from: [file: {$trace[0]['file']} line: {$trace[0]['line']}]";
        echo $isCli ? PHP_EOL : PHP_EOL.'<br>';
        echo '**********************************************************************';
        echo $isCli ? PHP_EOL : '<br>';
    }

}

