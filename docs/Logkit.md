## 简介

> 基于<a href="https://seldaek.github.io/monolog/" target="_blank">monolog</a>的日志工具类，遵循**<a href="http://www.php-fig.org/psr/psr-3/" target="_blank">PSR3</a>**规范
>
> 目前driver包括file和syslog

## 配置
**application.ini**
```ini
; 项目名，通过此配置来分割不同项目的日志，保存到独立目录
projName    = 'cs'

; 配置日志记录级别，只会记录优先级高于此级别的日志
log.level   = 'debug'

; 配置日志写入syslog
log.driver  = 'syslog'

; 或者配置写入文件
log.driver      = 'file'
log.filepath    = '/data/logs/project'
```
**日志级别配置说明**

| 日志级别      | 说明                  | 场景                                          |
|-----------    |--------------------   |--------------------------------------------   |
| emergency     | 系统不可用            |                                               |
| alert         | 需要立即处理的操作    | 如网站挂掉、数据库连不上等，应触发短信报警    |
| critical      | 重要信息              | 应用的部分模块不可用，意外的异常              |
| error         | 错误信息              | 一般不需要立即处理，但是应该记录和监控        |
| warning       | 警告信息              | 不规范或不支持的调用API，不一定是错误         |
| notice        | 提示信息              | 正常但比较特别的事件                          |
| info          | 一般信息              | 用户日志、SQL日志等                           |
| debug         | 调试信息              | curl返回值等                                  |

## 如何使用
```php
<?php
use Libyaf\Logkit\Logger;

//test作为namespace，syslog-ng切割时，作为二级目录名
$logger = Logger::ins('test');

//PSR3 context用法
$logger->info('test info message by {name}', array('name'=>'qinyuguang'));

$logger->error('test error message by {name}', array('name'=>'qinyuguang'));
// ...
```
**日志内容**
> 2015-09-10 10:36:30 cs test INFO: test info message by qinyuguang {"name":"qinyuguang"} {"process_id":21206}
>
> 2015-09-10 10:36:30 cs test ERROR: test error message by qinyuguang {"name":"qinyuguang"} {"process_id":21206}

## 更多参考
- <a href="http://gitlab.alibaba-inc.com/gaode.search/libyaf/wikis/logkit_project_syslog_conf" target="_blank">syslog-ng 配置参考</a>
