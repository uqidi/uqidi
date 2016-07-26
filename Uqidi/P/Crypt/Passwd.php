<?php
/**
 * @fileoverview:   Crypt_Passwd
 * @author:         Uqidi
 * @date:           2016-03-22
 * @copyright:      uqidi.com
 */
class P_Crypt_Passwd{
    /**
     * 加密方法
     * @param $password
     * @param int $salt_len
     * @return string
     */
    public static function password($password, $salt_len=6) {
        $salt = T_String::random($salt_len, '0123456789abcdef');
        return self::passwd($password, $salt).$salt;
    }

    public static function passwd($password, $salt){
        $pw_encode = $salt.$password.substr(md5($password), 7, 2).strlen($password);
        return md5($pw_encode);
    }

    /**
     * 验证密码
     * @param $password 加密前
     * @param $passwd 加密后
     * @param int $salt_len
     * @return bool
     */
    public static function check_password($password, $passwd, $salt_len=6){
        $salt = substr($passwd, 32, $salt_len);
        $password = self::passwd($password, $salt);
        if($passwd === $password.$salt)
            return true;
        return false;
    }

}