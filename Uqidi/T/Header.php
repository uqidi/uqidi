<?php
/**
 * @fileoverview:   Header
 * @author:         Uqidi
 * @date:           2015-11-15
 * @copyright:      uqidi.com
 */
class T_Header{
    /**
     * 无缓存
     * @author Uqidi
     */
    public static function no_cache(){
        if (!headers_sent()){
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');               /* Data in the past */
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');  /* Modified */
            header('Cache-Control: no-store, no-cache, must-revalidate');   /* HTTP/1.1 */
            header('Cache-Control: post-check=0, pre-check=0', false);
            header('Pragma: no-cache');                                     /* HTTP/1.0 */
        }
        return;
    }

    /**
     * 设置头部类型
     * @author Uqidi
     * @param string $type
     */
    public static function set_type($type='text/html'){
        if(isset($_SERVER['HTTP_ORIGIN']) && !empty($_SERVER['HTTP_ORIGIN'])){
            foreach(C('cross') as $access){
                if(false !== strpos($_SERVER['HTTP_ORIGIN'], $access)){
                    header("Access-Control-Allow-Credentials: true");
                    header('Access-Control-Allow-Origin: '.$_SERVER['HTTP_ORIGIN']);
                    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
                    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
                    break;
                }
            }
        }
        header('content-type:'.$type.';charset=utf8');
    }

    public static function user_agent($agent=''){
        if(empty($agent))
            return array();
        $regex = '/^(.+)\/[a-zA-z]?([\d.]+)\s?\((.+)[;]\s?(\w+)\s?([\d.]+)[;]?\s?.*\)\s?$/';
        $rs =  preg_match($regex, $agent, $matches);
        $result['agent']    = $agent;
        if(false != $rs){
            $result['app_name']     = strtolower($matches[1]);
            $result['app_version']  = strtolower($matches[2]);
            $result['dev_name']     = strtolower($matches[3]);
            $result['os_name']      = strtolower($matches[4]);
            $result['dev_version']  = $matches[5];
        }

        return $result;
    }
}