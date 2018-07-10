## 简介
> Queue消息队列工具。已支持 <a href="http://kr.github.io/beanstalkd/" target="_blank">Beanstalkd</a> 、<a href="http://kafka.apache.org/" target="_blank">Kafka</a>

依赖包：
 - beanstalkd：composer require pda/pheanstalk
 - kafka：composer require nmred/kafka-php

## 配置
**application.ini**
```ini
queue.default.driver        = 'beanstalk'
queue.default.host          = '127.0.0.1'
queue.default.port          = '11300'
queue.default.timeout       = '3.0'
queue.default.persistent    = false

queue.kafka.driver          = 'kafka'
queue.kafka.brokers         = 'kafka:9092'
queue.kafka.topics          = 'test'
queue.kafka.group           = 'test'
queue.kafka.offset_reset    = 'earliest'
queue.kafka.refresh_ms      = '10000'
queue.kafka.version         = '1.0.0'
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
//beanstalk传递第一个参数表示tube名称，kafka在配置中指定消费topics
Queue::ins()->pull('queue_name', $callback);

//产生延迟消息，10秒后加入队列（beanstalkd支持）
Queue::ins()->delay('queue_name', 'message content', 10);

//获取队列当前长度（beanstalkd支持）
Queue::ins()->size('queue_name');
```

## Method
- **push**

- **pull**

- **delay（部分支持）**

- **size（部分支持）**
