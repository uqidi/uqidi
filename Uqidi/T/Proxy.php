<?php
/**
 * @fileoverview:   Proxy
 * @author:         Uqidi
 * @date:           2015-11-21
 * @copyright:      Uqidi
 */
class T_Proxy{
    public static function https_proxy($id=0){
        if(empty(C_Http::$https_proxy))
            return '';
        $n = count(C_Http::$https_proxy);
        $id = $id%$n;
        return isset(C_Http::$https_proxy[$id]) ? C_Http::$https_proxy[$id] : '';
    }

    public static function http_proxy($id=0){
        if(empty(C_Http::$http_proxy))
            return '';
        $n = count(C_Http::$http_proxy);
        $id = $id%$n;
        return isset(C_Http::$http_proxy[$id]) ? C_Http::$http_proxy[$id] : '';
    }
}