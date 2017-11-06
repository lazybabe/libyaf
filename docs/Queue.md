## 简介
> Queue消息队列工具。已支持 <a href="http://kr.github.io/beanstalkd/" target="_blank">Beanstalkd</a>

## 配置
**application.ini**
```ini
queue.default.driver        = 'beanstalk'
queue.default.host          = '127.0.0.1'
queue.default.port          = '11300'
queue.default.timeout       = '3.0'
queue.default.persistent    = false
```

## 如何使用
```php
<?php
use Libyaf\Queue\Queue;

//产生消息
Queue::ins()->push('queue_name', 'message content');

//消费消息（callback无异常则delete该消息，出现异常则该消息延迟执行，默认3秒）
$callback = function ($data) {
    //do something...
    return true;
};
Queue::ins()->pull('queue_name', $callback);

//产生延迟消息，10秒后加入队列
Queue::ins()->delay('queue_name', 'message content', 10);

//获取队列当前长度
Queue::ins()->size('queue_name');
```

## Method
- **push**

- **pull**

- **delay**

- **size**
