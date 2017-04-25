## 简介

> libyaf是提供给yaf框架使用的工具库（如日志工具、DB层、Cache等）

> 在一些维护良好的第三方项目基础上，进行简单封装。其中依赖的第三方项目，通过composer进行管理

> 用yaf框架做服务化应用时，各服务使用统一的基础工具，可以有效降低开发和维护的成本

## 配置
使用libyaf，可以在php.ini中进行如下的简单配置即可

```ini
; libyaf遵循PSR4规范，可以配置include_path进行autoload，开发环境可以配置为自己clone的libyaf目录
include_path="/home/you/devspace/libyaf"

[yaf]
extension = yaf.so
; yaf环境标识
yaf.environ = "dev"
; 使用命名空间
yaf.use_namespace = 1
; 同时也配置yaf的library目录
yaf.library = "/home/you/devspace/libyaf"
```

## 目录
- [Cache](https://github.com/qinyuguang/libyaf/blob/master/docs/Cache.md)
- [Database](https://github.com/qinyuguang/libyaf/blob/master/docs/Database.md)
- [Helper](https://github.com/qinyuguang/libyaf/blob/master/docs/Helper.md)
  - [Arr](https://github.com/qinyuguang/libyaf/blob/master/docs/Helper_Arr.md)
  - [Client](https://github.com/qinyuguang/libyaf/blob/master/docs/Helper_Client.md)
  - [Cookie](https://github.com/qinyuguang/libyaf/blob/master/docs/Helper_Cookie.md)
  - [Daemon](https://github.com/qinyuguang/libyaf/blob/master/docs/Helper_Daemon.md)
  - [Datetime](https://github.com/qinyuguang/libyaf/blob/master/docs/Helper_Datetime.md)
  - [Debug](https://github.com/qinyuguang/libyaf/blob/master/docs/Helper_Debug.md)
  - [Security](https://github.com/qinyuguang/libyaf/blob/master/docs/Helper_Security.md)
  - [Text](https://github.com/qinyuguang/libyaf/blob/master/docs/Helper_Text.md)
  - [Url](https://github.com/qinyuguang/libyaf/blob/master/docs/Helper_Url.md)
- [Kvs](https://github.com/qinyuguang/libyaf/blob/master/docs/Kvs.md)
- [Logkit](https://github.com/qinyuguang/libyaf/blob/master/docs/Logkit.md)
- [Mongo](https://github.com/qinyuguang/libyaf/blob/master/docs/Mongo.md)
- [Queue](https://github.com/qinyuguang/libyaf/blob/master/docs/Queue.md)
- [Sdk](https://github.com/qinyuguang/libyaf/blob/master/docs/Sdk.md)
- [Session](https://github.com/qinyuguang/libyaf/blob/master/docs/Session.md)
- [Vendor](https://github.com/qinyuguang/libyaf/blob/master/docs/Vendor.md)
  - [Guzzle （HTTP Client）](https://github.com/qinyuguang/libyaf/blob/master/docs/Vendor_Guzzle.md)
  - [Pagerfanta （分页）](https://github.com/qinyuguang/libyaf/blob/master/docs/Vendor_Pagerfanta.md)
  - [Upload （上传）](https://github.com/qinyuguang/libyaf/blob/master/docs/Vendor_Upload.md)
  - [Validation （验证）](https://github.com/qinyuguang/libyaf/blob/master/docs/Vendor_Validation.md)
