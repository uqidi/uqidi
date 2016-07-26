<?php
/**
 * @fileoverview:   Filter
 * @类型：
 *      整型数值        int     range(min, max)
 *      实数数值        real    range(min, max)
 *      字母数字        alnum   strlen(fun,encoding,min,max)
 *      单纯数字        digit   strlen(fun,encoding,min,max)
 *      单纯字母        alpha   strlen(fun,encoding,min,max)
 *      集合范围        in      arr
 *      正则公式        preg    pattern
 *      方法方式        call    fun
 * @author:         Uqidi
 * @date:           2015-12-19
 * @copyright:      uqidi.com
 */
class T_Filter {

    const TYPE_INT      = 'int';        /* 整型数值        int     range(min, max)              */
    const TYPE_REAL     = 'real';       /* 实数数值        real    range(min, max)              */
    const TYPE_ALNUM    = 'alnum';      /* 字母数字        alnum   strlen(fun,encoding,min,max) */
    const TYPE_DIGIT    = 'digit';      /* 单纯数字        digit   strlen(fun,encoding,min,max) */
    const TYPE_ALPHA    = 'alpha';      /* 单纯字母        alpha   strlen(fun,encoding,min,max) */
    const TYPE_IN       = 'in';         /* 集合范围        in      arr                          */
    const TYPE_PREG     = 'preg';       /* 正则公式        preg    pattern                      */
    const TYPE_CALL     = 'call';       /* 方法方式        call    fun                          */

    /**
     * 判断处理参数
     * @param $value
     * @param $option
     * @return bool
     */
    public static function filter(&$value, $option){
        if(empty($option))
            return true;
        if(empty($value)){
            if(isset($option['default'])){
                $value = $option['default'];
                return true;
            }
            return $option['must'] ? false : true;
        }
        if(!isset($option['type']))
            return true;
        $fun = '_'.$option['type'];
        return self::$fun($value, $option);
    }

    /**
     * 整型数值
     * @param $value
     * @param $option
     * @return bool
     */
    private static function _int(&$value, $option){
        !is_int($value) ? $value = intval($value) : '';
        if(isset($option['range']['max']) && $value>$option['range']['max']){
            return false;
        }
        if(isset($option['range']['min']) && $value<$option['range']['min']){
            return false;
        }
        return true;
    }

    /**
     * 实数数值
     * @param $value
     * @param $option
     * @return bool
     */
    private static function _real(&$value, $option){
        !is_real($value) ? $value = floatval($value) : '';
        if(isset($option['range']['max']) && $value>$option['range']['max']){
            return false;
        }
        if(isset($option['range']['min']) && $value<$option['range']['min']){
            return false;
        }
        return true;
    }

    /**
     * 字母数字
     * @param $value
     * @param $option
     * @return bool
     */
    private static function _alnum(&$value, $option){
        if(!ctype_alnum($value)){
            return false;
        }
        return self::strLenLimit($value, $option);
    }

    /**
     * 单纯数字
     * @param $value
     * @param $option
     * @return bool
     */
    private static function _digit(&$value, $option){
        if(!ctype_digit($value)){
            return false;
        }
        return self::strLenLimit($value, $option);
    }

    /**
     * 单纯字母
     * @param $value
     * @param $option
     * @return bool
     */
    private static function _alpha(&$value, $option){
        if(!ctype_alpha($value)){
            return false;
        }
        return self::strLenLimit($value, $option);
    }

    /**
     * 集合范围
     * @param $value
     * @param $option
     * @return bool
     */
    private static function _in(&$value, $option){
        if(!is_array($option['arr']) || empty($option['arr']))
            return true;
        return in_array($value, $option['arr']);
    }

    /**
     * 正则公式
     * @param $value
     * @param $option
     * @return int
     */
    private static function _preg(&$value, $option){
        return (bool)preg_match($option['pattern'], $value);
    }

    /**
     * 自由方式
     * @param $value
     * @param $option
     * @return mixed
     */
    private static function _call(&$value, $option){
        return call_user_func($option['fun'], array($value, $option));
    }

    /**
     * 字符串长度
     * @param $value
     * @param $option
     * @return bool
     */
    public static function strLenLimit($value, $option){
        if(!isset($option['strlen'])){
            return true;
        }

        $deal_fun = isset($option['strlen']['fun']) ? $option['strlen']['fun'] : 'mb_strlen';
        $encoding = isset($option['strlen']['encoding']) ? $option['strlen']['encoding'] : mb_internal_encoding();
        $strlen = $deal_fun($value, $encoding);

        if(isset($option['strlen']['max']) && $strlen>$option['strlen']['max']){
            return false;
        }
        if(isset($option['strlen']['min']) && $strlen<$option['strlen']['min']){
            return false;
        }
        return true;
    }
}
