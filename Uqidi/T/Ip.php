<?php
/**
 * @fileoverview:   IP
 * @author:         Uqidi
 * @date:           2015-11-21
 * @copyright:      Uqidi
 */

class T_Ip{

    /**
     * 判断URL中是IP
     * @param $url
     * @return bool
     */
    public static function urlIsIp($url){
        $parse_url = parse_url($url);
        if(filter_var($parse_url['host'], FILTER_VALIDATE_IP))
            return true;
        return false;
    }

    /**
     * 取本服务器IP地址
     * @param bool $is_inner_net
     * @param bool $cache
     * @return string
     */
    public static function getLocalLastIp($is_inner_net=false , $cache=true){
        $ip_cache = SYS_CACHE_PATH.'PHP5_CACHED_IP_CONFIG';
        $ips = array();
    	if($cache && file_exists($ip_cache)){
    		$arr= parse_ini_file($ip_cache);
    		if(isset($arr['IPADDR'])){
    			$ips[] = $arr['IPADDR'];
    		}
    	}
    	if(empty($ips)){
    		$handle = popen("/sbin/ifconfig|grep 'inet addr'", 'r');
    		while ($s = fgets($handle,1024)){
    			if (preg_match("/inet addr:([0-9.]+)/", $s, $match)){
    				$ips[] = $match[1];
    			}
    		}
    	}

    	foreach ($ips as $ip){
    		$sub_ip = substr($ip, 0, strpos($ip, '.', 4));
    		if ((!$is_inner_net && (!in_array($sub_ip , C_Http::$inner_segment))) || ($is_inner_net && (in_array($sub_ip , C_Http::$inner_segment)))){
                if(!is_dir(SYS_CACHE_PATH)){
                    mkdir(SYS_CACHE_PATH , 0755 , true);
                }

    			if(!is_file($ip_cache)){
    				file_put_contents($ip_cache, 'IPADDR="'.$ip.'"');
    			}
    			return $ip;
    		}
    	}
    }

    /**
     * 获取用户IP
     * @return mixed|string
     */
    static function get_real_ip(){
    
    	if(getenv('HTTP_X_FORWARDED_FOR') != '' ){
    		$client_ip = !empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : (!empty($HTTP_ENV_VARS['REMOTE_ADDR']) ? $HTTP_ENV_VARS['REMOTE_ADDR'] : '');
    		$entries = explode(',', getenv('HTTP_X_FORWARDED_FOR'));
    		reset($entries);
    		while (list(, $entry) = each($entries)){
    			$entry = trim($entry);
                $ip_list = array();
    			if (preg_match('/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/', $entry, $ip_list) ){
    				$private_ip = array('/^0\./', '/^127\.0\.0\.1/', '/^192\.168\..*/', '/^172\.((1[6-9])|(2[0-9])|(3[0-1]))\..*/', '/^10\..*/', '/^224\..*/', '/^240\..*/');
    				$found_ip = preg_replace($private_ip, $client_ip, $ip_list[1]);
    
    				if ($client_ip != $found_ip){
    					$client_ip = $found_ip;
    					break;
    				}
    			}
    		}
    	}else{
    		$client_ip = (!empty($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : ((!empty($HTTP_ENV_VARS['REMOTE_ADDR']) ) ? $HTTP_ENV_VARS['REMOTE_ADDR'] : '' );
    	}
    
    	return   $client_ip ;

    }

    /**
     * 获取用户IP的整型
     * @param string $ip
     * @return string
     */
    static function get_real_ip_int($ip=''){
        if(empty($ip)){
            $ip = self::get_real_ip();
        }
    	return sprintf("%u", ip2long($ip));
    }

    /**
     * 域名转换IP
     * @param $domain
     * @return array
     */
    static public function domain2ip($domain)
	{
		$cmd = "dig ".$domain." | grep 'IN A' | awk '{print $5}'";
		$rs = T_cmd::run_cmd($cmd);
		$rs = explode("\n" , $rs);
		foreach ($rs as $k => $ip){
			if(empty($ip))
				unset($rs[$k]);
		}
		return $rs;
	}
}