## 简介
> Mongo是基于<a href="http://docs.doctrine-project.org/projects/doctrine-mongodb-odm/en/latest/" target="_blank">doctrine\mongodb-odm</a>，获取连接实例的封装。

## 配置
**application.ini**
```ini
; 配置ODM所需目录，以供生成映射文件，要求目录可写
ODM.proxiesDir      = LIB_PATH"Proxies"
ODM.hydratorsDir    = LIB_PATH"Hydrators"
ODM.documentsDir    = LIB_PATH"Documents"

; 默认default连接
mongo.default.host              = '127.0.0.1'
mongo.default.port              = '27017'
mongo.default.username          = ''
mongo.default.password          = ''
mongo.default.options.timeout   = 3000

; 配置其他连接
mongo.source.host              = '127.0.0.1'
mongo.source.port              = '27018'
mongo.source.username          = ''
mongo.source.password          = ''
mongo.source.options.timeout   = 3000
```

## 如何使用
一个简单的follow计数功能


**Bootstrap.php**
```php
<?php

class Bootstrap extends Yaf\Bootstrap_Abstract
{
    public function _initLoader($dispatcher)
    {
        //注册本地类
        $localNameSpace = array(
            'Documents',
        );

        Yaf\Loader::getInstance()->registerLocalNamespace($localNameSpace);
    }
}
```

**library/Documents/Follow.php (Follow对象映射)**
```php
<?php
namespace Documents;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/** @ODM\Document(db="test", collection="follow") */
class Follow
{
    /** @ODM\Id */
    private $id;

    /** @ODM\String */
    private $from;

    /** @ODM\String */
    private $pos;

    /** @ODM\Int */
    private $count;

    public function setFrom($from)
    {
        $this->from = $from;
    }

    public function setPos($pos)
    {
        $this->pos = $pos;
    }

    public function setCount($count)
    {
        $this->count = $count;
    }
}
```

**Model/Follow.php**
```php
<?php
use Documents\Follow;
use Libyaf\Mongo\ODM;

class FollowModel
{
    public function createFollow($from, $pos)
    {
        $follow = new Follow();

        $follow->setFrom($from);
        $follow->setPos($pos);
        $follow->setCount(1);

        $dm = ODM::ins()->getDocumentManager();
        $dm->persist($follow);
        $dm->flush();

        return $follow;
    }

    public function inc($from, $pos)
    {
        $result = $this->getByFromPos($from, $pos);
        if (! $result) {
            $result = $this->createFollow($from, $pos);
            return $result ? true : false;
        }

        $result = ODM::ins()
            ->getDocumentManager()
            ->createQueryBuilder('Documents\Follow')
            ->update()
            ->field('count')->inc(1)
            ->getQuery()
            ->execute();

        return (bool) $result['ok'];
    }

}
```

## 更多参考
- <a href="http://docs.doctrine-project.org/projects/doctrine-mongodb-odm/en/latest/" target="_blank">ODM 文档</a>
