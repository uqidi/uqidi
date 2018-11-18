<?php
/**
 * @fileoverview:   T_WebSocket
 * @author:         Uqidi
 * @date:           2015-11-21
 * @copyright:      Uqidi
 */
class T_Uqidi{
    /**
     * 分表
     * @param int $u    uid
     * @param int $n    分多少个表
     * @return int
     */
    public static function calc_hash_tbl($u, $n = 128){
    	$h  = sprintf("%u", crc32($u));
    	$h1 = intval($h / $n);
    	$h2 = $h1 % $n;
    	return self::n2tbNo($h2);
    }

    /**
     * 分库
     * @param int $u    uid
     * @param int $s    分多少个库
     * @return int 
     */
    public static function calc_hash_db($u, $s = 4){
    	$h  = sprintf("%u", crc32($u));
    	$index = intval(fmod($h, $s));
    	return $index;
    }


    /**
     * 字符串左边补位
     * @param $int
     * @return string
     */
    public static function n2tbNo($int){
        $h3 = base_convert($int, 10, 16);
        return sprintf("%02s", $h3);
    }

    /**
     * 通用hash
     * @param string|int $u
     * @param int $s
     * @return int
     */
    public static function calc_hash($u, $s = 4){
        $h  = sprintf("%u", crc32($u));
        return  intval(fmod($h, $s));
    }
}