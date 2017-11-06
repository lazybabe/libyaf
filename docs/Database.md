## 简介

> Database是基于<a href="http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/" target="_blank">doctrine\dbal</a>，获取连接实例的封装。
>
> 目前driver有pdomysql。
>
> 推荐使用Query Builder或SQL预处理来构建查询，以防止SQL注入。

## 配置
**application.ini**
```ini
; 默认default连接
database.default.driver     = 'PDOMysql'
database.default.host       = '127.0.0.1'
database.default.port       = '3306'
database.default.username   = 'user'
database.default.password   = 'password'
database.default.dbname     = 'cs_'USER
database.default.charset    = 'utf8'

; 配置其他连接
database.slave.driver       = 'PDOMysql'
database.slave.host         = '127.0.0.1'
database.slave.port         = '3306'
database.slave.username     = 'user'
database.slave.password     = 'password'
database.slave.dbname       = 'cs_'USER
database.slave.charset      = 'utf8'
```

## 如何使用
```php
<?php
use Libyaf\Database\Database;

//Query Builder方式构建查询
$data = Database::ins()
    ->createQueryBuilder()
    ->select('name,id,age')
    ->from('test')
    ->where('name=:name')
    ->setParameter(':name', 'gin')
    ->execute()
    ->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_UNIQUE);

//SQL预处理构建查询
$sql        = 'select name,id,age from test where name = :name';
$statement  = Database::ins()->prepare($sql);
$statement->bindValue('name', 'gin');
$statement->execute();
$data       = $statement->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_UNIQUE);


//连接第二个实例 slave
$data = Database::ins('slave')
    ->createQueryBuilder()
    ->select('name,id,age')
    ->from('test')
    ->where('name=:name')
    ->setParameter(':name', 'gin')
    ->execute()
    ->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_UNIQUE);
```

## 更多参考
- <a href="http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/" target="_blank">Doctrine\DBAL 文档</a>
