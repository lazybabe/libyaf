## 简介

> Daemon类可以创建守护进程来执行任务，常用来作队列的消费者
>
> 守护进程包含master和worker
>
> master负责监控并启动worker，当worker意外退出时可以及时启动新worker
>
> master自身无监控，需配合其他方法进行监控

## 配置
**application.ini**
```ini
; 进程pid文件路径，默认在/var/run目录中生成
daemon.default.pid          = '/var/run/demo/'USER'/daemon.default.pid'
; worker进程数，建议与CPU核数相同
daemon.default.worker       = 24
; worker运行用户和用户组，默认为执行脚本用户，非当前用户需root权限运行
daemon.default.user         = 'qinyuguang'
daemon.default.group        = 'qinyuguang'
```

## 如何使用
```php
<?php
use Libyaf\Helper\Daemon;

$config = Yaf\Registry::get('config')->daemon->default->toArray();

$daemon = new Daemon($config);

//job可获取当前worker的信息，目前基本没用到
$job = function ($worker) {
    static $i = 0;
    echo $i++;
    sleep(1);
};

//设置要执行的JOB(callable)
$daemon->setJob($job);

//重复运行JOB。参数表示每个worker执行多少次JOB后重启，不填参数则一直执行不重启。
$daemon->loop(3);

//启动daemon
$daemon->run();
```

**启动停止命令**

将以上脚本保存为demo.php

执行命令如下

php demo.php start [-d]

php demo.php stop

php demo.php restart [-d]

php demo.php kill

-d参数表示作为守护进程运行，不加-d参数为调试模式，可以看到worker的输出内容。

**PS**： job程序修改后需要restart
