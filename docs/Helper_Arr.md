## 简介
> Arr提供对数组的几种易用操作
>
> 详细使用方法见注释


## get
```php
/**
 * @brief 获取数组内容
 *           // 例如，从 $_POST 中获取 "age", 如果不存在则获取默认值28
 *           $age = Arr::get($_POST, 'age', 28);
 *
 * @param $array
 * @param $key
 * @param $default
 *
 * @return
 */
public static function get($array, $key, $default = null)
```

## extract
```php
/**
 * @brief 抽取数组指定下标的值，返回新数组
 *           // 例如，从 $_POST 中获取 "name" "pass", 如果不存在则设置默认值空字符
 *           $user = Arr::extract($_POST, array('name', 'pass'), '');
 *
 * @param $array
 * @param $keys
 * @param $default
 *
 * @return
 */
public static function extract($array, array $keys, $default = null)
```

## path
```php
/**
 * @brief 路径方式取值
 *           //例如，取$array['one']['two']['three']值
 *           $value = Arr::path($array, 'one.two.three');
 *
 * @param $array
 * @param $path       可用通配符*，多层数组取指定列值，比column方便
 * @param $default
 * @param $delimiter
 *
 * @return
 */
public static function path($array, $path, $default = NULL, $delimiter = null)
```

## column
```php
/**
 * @brief 数组转换
 *           $columnKey:null      $indexKey:null       返回全部列,自然数组
 *           $columnKey:notnull   $indexKey:null       返回指定列,自然数组
 *           $columnKey:null      $indexKey:notnull    返回全部列,以$indexKey为下标的关联数组
 *           $columnKey:notnull   $indexKey:notnull    返回指定列,以$indexKey为下标的关联数组
 *
 * @param $input
 * @param $columnKey
 * @param $indexKey
 *
 * @return
 *
 * @deprecated PHP5.5后增加了array_column函数,使用array_column来替代本方法
 */
public static function column(array $input, $columnKey, $indexKey = null)
```
