## 简介
> Datetime 是日期/时间的辅助工具，可以方便的获取时间偏移量、相对描述等信息

## relative
```php
/**
    * @brief 比较时间，获得两个时间的关系字符串，例如：1秒前、2个月前、3天后...
    *
    * @param $target    string  目标时间
    * @param $origin    string  源时间，默认当前时间。
    * @param $level     string  比较级别，超过级别返回目标时间。可选y|m|d|h|i|s
    * @param $format    string  超出级别返回的时间格式
    *
    * @return string
 */
public static function relative($target, $origin = 'Now', $level = 'd', $format = null)
```
