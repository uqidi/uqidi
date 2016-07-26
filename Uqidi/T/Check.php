<?php

class T_Check{
    /**
     * 帐号
     * @param $data
     * @return int
     */
    public static function username($data) {
        if(is_array($data)){
            $name = $data[0];
        }
    	return (bool)preg_match('/^[a-zA-Z][a-zA-Z0-9_]{4,19}$/u', $name);
    }

    /**
     * 登录密码
     * 1、密码为6-16位，数字、字母和特殊字符，区分大小写
     * 2、仅允许以下特殊字符  ~!@#$%^*(){}[]:";',.?|
     * 3、密码不能含全角、空格、中文等非法字符
     * 4、密码不能为纯数字、纯字母或纯字符（口令至少需包含2个特征组：小写字母，数字，大写字母，特殊符号)
     * 5、密码不能与登录名相同
     * @param $data
     * @return int
     */
    /**
     *

     */
    public static function password($data){
        if(is_array($data)){
            $password = $data[0];
            if(isset($data[1]) && isset($data[1]['data']) && !empty($data[1]['data']) && $data[1]['data'] === $password){
                return false;
            }
        }
    	return (bool)preg_match('/^(?!^\d+$)(?!^[a-z]+$)(?!^[A-Z]+$)(?!^[~!@#$%^*(){}\[\]:";\',.?|]+$)([0-9a-zA-Z]|[~!@#$%^*(){}\[\]:";\',.?|]){6,16}$/u', $password);
    }

    /**
     * 真实姓名
     * @param $data
     * @return int
     */
    public static function realname($data){
        if(is_array($data)){
            $name = $data[0];
        }
    	return (bool)preg_match('/^[a-zA-Z0-9_\-\x{4e00}-\x{9fa5}.·]{2,30}$/u', $name);
    }

    /**
     * 交易密码
     * 1、密码为8-16位，数字、字母和特殊字符，区分大小写
     * 2、仅允许以下特殊字符  ~!@#$%^*(){}[]:";',.?|
     * 3、密码不能含全角、空格、中文等非法字符
     * 4、密码不能为纯数字、纯字母或纯字符（口令至少需包含2个特征组：小写字母，数字，大写字母，特殊符号)
     * 5、密码不能与登录名相同
     * @param $data
     * @return int
     */
    public static function trader_password($data){
        if(is_array($data)){
            $password = $data[0];
            if(isset($data[1]) && isset($data[1]['data']) && !empty($data[1]['data']) && $data[1]['data'] === $password){
                return false;
            }
        }
        return (bool)preg_match('/^(?!^\d+$)(?!^[a-z]+$)(?!^[A-Z]+$)(?!^[~!@#$%^*(){}\[\]:";\',.?|]+$)([0-9a-zA-Z]|[~!@#$%^*(){}\[\]:";\',.?|]){8,16}$/u', $password);
    }

    public static function bank_code($data){
        if(is_array($data)){
            $code = $data[0];
        }
        return (bool)preg_match('/^\d{8,20}$/u', $code);
    }

    /**
     * 邮箱
     * @param $data
     * @return bool
     */
    public static function email($data){
        if(is_array($data)){
            $email = $data[0];
        }
        return (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * 手机号
     * @param $data
     * @return bool
     */
    public static function phone($data){
        if(is_array($data)){
            $phone = $data[0];
        }
        return (bool)preg_match('/^1[\d]{10}$/u', $phone);
    }
}