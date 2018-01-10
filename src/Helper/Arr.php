<?php
namespace Libyaf\Helper;

class Arr
{
    public static $delimiter = '.';

    /**
     * @brief 获取数组内容
     *           // 从 $_POST 中获取 "age", 如果不存在则获取默认值28
     *           $age = Arr::get($_POST, 'age', 28);
     *
     * @param $array
     * @param $key
     * @param $default
     *
     * @return
     */
    public static function get($array, $key, $default = null)
    {
        $value = is_array($array) ? $array[$key] : $array->$key;
        return isset($value) ? $value : $default;
    }

    /**
     * @brief 抽取数组指定下标的值，返回新数组
     *           // 从 $_POST 中获取 "name" "pass", 如果不存在则设置默认值空字符
     *           $user = Arr::extract($_POST, ['name', 'pass'], '');
     *
     * @param $array
     * @param $keys
     * @param $default
     *
     * @return
     */
    public static function extract($array, array $keys, $default = null)
    {
        $type = is_array($array);

        $found = [];
        foreach ($keys as $key) {
            $value = $type ? $array[$key] : $array->$key;
            $found[$key] = isset($value) ? $value : $default;
        }

        return $found;
    }

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
    {
        $result = [];

        if (null === $indexKey) {
            if (null === $columnKey) {
                $result = array_values($input);
            } else {
                foreach ($input as $row) {
                    $result[] = $row[$columnKey];
                }
            }
        } else {
            if (null === $columnKey) {
                foreach ($input as $row) {
                    $result[$row[$indexKey]] = $row;
                }
            } else {
                foreach ($input as $row) {
                    $result[$row[$indexKey]] = $row[$columnKey];
                }
            }
        }

        return $result;
    }

    /**
     * @brief 路径方式取值
     *           //取$array['one']['two']['three']值
     *           $value = Arr::path($array, 'one.two.three');
     *
     * @param $array
     * @param $path       可用通配符*，多层数组取指定列值，比column方便
     * @param $default
     * @param $delimiter
     *
     * @return
     */
    public static function path($array, $path, $default = null, $delimiter = null)
    {
        if (! is_array($array)) {
            return $default;
        }

        if (is_array($path)) {
            $keys = $path;
        } else {
            if (isset($array[$path])) {
                return $array[$path];
            }

            if ($delimiter === null) {
                $delimiter = Arr::$delimiter;
            }

            $path = ltrim($path, "{$delimiter} ");

            $path = rtrim($path, "{$delimiter} *");

            $keys = explode($delimiter, $path);
        }

        do {
            $key = array_shift($keys);

            if (ctype_digit($key)) {
                $key = (int) $key;
            }

            if (isset($array[$key])) {
                if ($keys) {
                    if (is_array($array[$key])) {
                        $array = $array[$key];
                    } else {
                        break;
                    }
                } else {
                    return $array[$key];
                }
            } elseif ($key === '*') {
                //通配符
                $values = [];
                foreach ($array as $arr) {
                    if ($value = Arr::path($arr, implode('.', $keys))) {
                        $values[] = $value;
                    }
                }

                if ($values) {
                    return $values;
                } else {
                    break;
                }
            } else {
                break;
            }
        } while ($keys);

        return $default;
    }

    /**
     * 按路径设置数组值
     *
     * @param array   $array     待更新数组
     * @param string  $path      路径
     * @param mixed   $value     更新值
     * @param string  $delimiter 路径分隔符
     */
    public static function setPath(&$array, $path, $value, $delimiter = null)
    {
        if (! $delimiter) {
            $delimiter = Arr::$delimiter;
        }

        $keys = explode($delimiter, $path);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            if (ctype_digit($key)) {
                $key = (int) $key;
            }

            if ( ! isset($array[$key])) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;
    }

}

