<?php
/**
 * @fileoverview:   Http
 * @author:         Uqidi
 * @date:           2015-11-15
 * @copyright:      uqidi.com
 */
class T_Http{

    private static $_curlopt = array(); /* curlopt 参数 */

    /**
     * 设置CURL参数
     * @param $name
     * @param $value
     */
    public static function setOpt($name, $value=''){
        if(is_array($name)){
            foreach($name as $k=>$v){
                self::setOpt($k, $v);
            }
        }else{
            if($name == CURLOPT_PROXY){
                self::$_curlopt[CURLOPT_HTTPPROXYTUNNEL] = 1;
            }
            self::$_curlopt[$name] = $value;
        }
    }

    /**
     * GET
     * @param $url
     * @param $data
     * @param int $timeout
     * @return bool|mixed
     */
    public static function get($url, $data='', $timeout = 5){
        return self::_curlGet($url, $data, $timeout);
    }

    /**
     * POST
     * @param $url
     * @param $aPost
     * @param int $timeout
     * @return mixed
     */
    public static function post($url, $aPost, $timeout = 5){
        return self::_curlPost($url, $aPost, $timeout);
    }

    /**
     * @param $url
     * @param string $data
     * @param $timeout
     * @return bool|mixed
     */
    private function _curlGet($url, $data='', $timeout=5){
        if(!empty($data)){
            $data = is_array($data) ? self::http_build_query($data) : $data;
            $url .= '?'.$data;
        }

        $curl = curl_init();

        $parse_url = parse_url($url);
        /* 判断HTTPS请求 */
        if('https' === strtolower($parse_url['scheme'])){
            $curl_opts = array(
                CURLOPT_RETURNTRANSFER      => true,
                CURLOPT_BINARYTRANSFER      => true,
                CURLOPT_SSL_VERIFYPEER      => false,
                CURLOPT_SSL_VERIFYHOST      => false,
                CURLOPT_TIMEOUT             => $timeout,
            );
        }else{
            $curl_opts = array(
                CURLOPT_RETURNTRANSFER  => true,
                CURLOPT_URL             => $url,
                CURLOPT_HTTPGET         => 1,
                CURLOPT_HEADER          => false,
                CURLOPT_TIMEOUT         => $timeout,
            );
        }


        $curl_opts = array_merge($curl_opts, self::$_curlopt);
        curl_setopt_array($curl, $curl_opts);


        $start = self::_microtime();
    	$rs =curl_exec($curl);
    	$conn_time = round(self::_microtime() - $start , 3);


    	if($rs === false){
            T_Logger::monitorLog(__CLASS__ , 'curl_get_err '.$url.' '.$conn_time.' '.curl_error($curl) , T_Logger::LOG_LEVEL_ALERM);
            curl_close($curl);
            return false;
        }
        curl_close($curl);

    	if($conn_time > 1)
            T_Logger::monitorLog(__CLASS__ , 'curl_get_time '.$url.' '.$conn_time , T_Logger::LOG_LEVEL_NOTICE);
    	else
            T_Logger::debugLog(__CLASS__ , 'curl_get_time '.$url.' '.$conn_time);

    	return $rs;
    }

    /**
     * @param $url
     * @param $aPost
     * @param $timeout
     * @return bool|mixed
     */
    private function _curlPost($url , $aPost , $timeout=5){
        $curl = curl_init();

        $parse_url = parse_url($url);
        /* 判断HTTPS请求 */
        if('https' === strtolower($parse_url['scheme'])){
            $curl_opts = array(
                CURLOPT_RETURNTRANSFER      => true,
                CURLOPT_BINARYTRANSFER      => true,
                CURLOPT_SSL_VERIFYPEER      => false,
                CURLOPT_SSL_VERIFYHOST      => false,
                CURLOPT_POST                => 1,
                CURLOPT_POSTFIELDS          => $aPost,
                CURLOPT_TIMEOUT             => $timeout,
            );
        }else{
            $curl_opts = array(
                CURLOPT_RETURNTRANSFER  => true,
                CURLOPT_URL             => $url,
                CURLOPT_POST            => 1,
                CURLOPT_POSTFIELDS      => $aPost,
                CURLOPT_TIMEOUT         => $timeout,
            );
        }



        $curl_opts = array_merge($curl_opts, self::$_curlopt);
        curl_setopt_array($curl, $curl_opts);

    	$start = self::_microtime();
    	$rs =curl_exec($curl);
    	$conn_time = round(self::_microtime() - $start , 3);


    	if($rs === false){
            T_Logger::monitorLog(__CLASS__ , 'curl_post_err '.$url.' '.$conn_time.' '.curl_error($curl), T_Logger::LOG_LEVEL_ALERM);
            curl_close($curl);
            return false;
    	}

        curl_close($curl);

        if($conn_time > 1)
            T_Logger::monitorLog(__CLASS__ , 'curl_post_time '.$url.' '.$conn_time , T_Logger::LOG_LEVEL_NOTICE);
        else
            T_Logger::debugLog(__CLASS__ , 'curl_post_time '.$url.' '.$conn_time);

    	return $rs;
    }

    /**
     * 时间统计
     * @return mixed
     */
    private function _microtime(){
    	return microtime(true);
    }
}