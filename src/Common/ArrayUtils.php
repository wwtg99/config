<?php
/**
 * Created by PhpStorm.
 * User: wwt
 * Date: 2016/7/30 0030
 * Time: 上午 11:30
 */

namespace Wwtg99\Config\Common;


class ArrayUtils
{

    /**
     * @param array $arr
     * @param string $name
     * @param $defval
     * @return mixed
     */
    public static function getInArray($arr, $name, $defval = null)
    {
        $name = trim($name);
        $dot = strpos($name, '.');
        if ($dot > 0) {
            $key = substr($name, 0, $dot);
            $rname = substr($name, $dot + 1);
        } else {
            $key = $name;
            $rname = null;
        }
        if (is_array($arr)) {
            if (array_key_exists($key, $arr)) {
                if (is_null($rname)) {
                    return $arr[$key];
                } elseif (is_array($arr[$key])) {
                    return self::getInArray($arr[$key], $rname, $defval);
                }
            }
        }
        return $defval;
    }

    /**
     * @param $arr
     * @param $name
     * @return bool
     */
    public static function hasInArray($arr, $name)
    {
        $name = trim($name);
        $dot = strpos($name, '.');
        if ($dot > 0) {
            $key = substr($name, 0, $dot);
            $rname = substr($name, $dot + 1);
        } else {
            $key = $name;
            $rname = null;
        }
        if (is_array($arr)) {
            if (array_key_exists($key, $arr)) {
                if (is_null($rname)) {
                    return true;
                } elseif (is_array($arr[$key])) {
                    return self::hasInArray($arr[$key], $rname);
                }
            }
        }
        return false;
    }

    /**
     * @param $arr
     * @param $name
     * @param $value
     * @return array
     */
    public static function setInArray(&$arr, $name, $value)
    {
        $name = trim($name);
        $dot = strpos($name, '.');
        if ($dot > 0) {
            $key = substr($name, 0, $dot);
            $rname = substr($name, $dot + 1);
        } else {
            $key = $name;
            $rname = null;
        }
        if (is_array($arr)) {
            if (is_null($rname)) {
                $arr[$key] = $value;
                return $arr;
            } else {
                if (!isset($arr[$key])) {
                    $arr[$key] = [];
                }
                self::setInArray($arr[$key], $rname, $value);
            }
        }
        return $arr;
    }
}