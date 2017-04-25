## 简介
> Pagerfanta是一个强大的分页库。
>
> 包含多种数据适配器，如Array、DBAL、ORM、Mongo等，可以方便的使用各种数据。
>
> 视图层支持多种方式渲染，有默认、可选、Bootstrap和Bootstrap3。

## 使用方法
```php
<?php
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineDbalSingleTableAdapter;
use Pagerfanta\View\TwitterBootstrap3View;

//数据接口用DBAL适配器
$builder = Database::ins()
    ->createQueryBuilder()
    ->select('*')
    ->from('task', 't');

$adapter    = new DoctrineDbalSingleTableAdapter($builder, 't.id');

//创建分页
$pagerfanta = new Pagerfanta($adapter);

//设置每页最大条数
$pagerfanta->setMaxPerPage(20);

//设置当前页数
$pagerfanta->setCurrentPage(1);

//使用Bootstrap3结构渲染
$view       = new TwitterBootstrap3View();

//设置分页路由
$routeGenerator = function($page) {
    return '?page='.$page;
};

//可以对渲染内容做配置，如样式、文字，具体配置项参考手册说明
$options    = array(
    'proximity'     => 3,
    'prev_message'  => '上一页',
    'next_message'  => '下一页',
);

//获取html结构
$html  = $view->render($pagerfanta, $routeGenerator, $options);

//获取当前分页的数据
$data = $pagerfanta->getCurrentPageResults();
```

## 更多参考
- <a href="https://github.com/whiteoctober/Pagerfanta" target="_blank">Pagerfanta 文档</a>
