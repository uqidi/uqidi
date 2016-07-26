<?php
/**
 * @fileoverview:   日志工具类
 * @author:         Uqidi
 * @date:           2015-11-15
 * @copyright:      Uqidi.com
 */

class T_Logger{
    const LOG_LEVEL_ALERM = 'alarm';
    const LOG_LEVEL_ERROR = 'error';
    const LOG_LEVEL_NOTICE = 'notice';
    static public $request_nolog = array('password', 'callback', '_');
    
    const LOG_SEP = ' | ';

    private static $_save = array();
    
    /**
     * 调试debug
     * @author Uqidi
     * @param string $type
     * @param string $msg
     * @param bool $force
     * @return bool
     */
    public static function debugLog($type, $msg , $force = false){
        if(DEBUG || $force){
	        $dir = SYS_LOG_DEBUG_PATH . "/" . $type;
	        if(is_array($msg))
                $msg = implode(' ', $msg);
	        return self::_writeLogToFile($dir, $msg);
        }
    }

    /**
     * 监控LOG 根据LOG进行系统运行状态监控
     * @author Uqidi
     * @param string $type
     * @param string $msg
     * @param string $level
     * @return bool|int
     */
    public static function monitorLog ($type , $msg , $level=self::LOG_LEVEL_ERROR){
        $dir = SYS_LOG_MONITOR_PATH . "/" . $type;
        if(is_array($msg))
            $msg = implode(' ', $msg);

        $msg = '['.$level.'] '.$type.SYS_LOG_SEPARATE.T_Ip::getLocalLastIp().SYS_LOG_SEPARATE.$msg;
        return self::_writeLogToFile($dir, $msg);
    }

    /**
     * 数据投放 需要长期跟进的 需要设置数据回收机制
     * @author Uqidi
     * @param string $type
     * @param string $msg
     * @param bool $force
     * @param bool $savebydate
     * @param bool $extinfo
     * @return bool
     */
    public static function dataLog ($type , $msg , $force = true , $savebydate = true , $extinfo = false){
        if(DEBUG || $force){
	        $dir = SYS_LOG_DATA_PATH . "/" . $type;
            if(is_array($msg)) $msg = implode(' ', $msg);
	        return self::_writeLogToFile($dir, $msg , $savebydate , $extinfo);
        }
        return true;
    }

    /**
     * 行为日志
     * @author Uqidi
     * @param $type
     * @param $usersign
     * @param $action_code
     * @param $data
     * @return bool|int
     */
    public static function actionLog($type, $usersign , $action_code , $data){
        $dir = SYS_LOG_ACTION_PATH . "/" . $type ;
        if(is_array($data)){
            $data = self::_formatActLog($data);
        }
        $sep = SYS_LOG_SEPARATE;
        $msg = date('Y-m-d H:i:s')
            .$sep. T_Ip::get_real_ip()
            .$sep. $usersign
            .$sep. $action_code
            .$sep. $_SERVER['SCRIPT_NAME']
            .$sep. T_Ip::getLocalLastIp()
            .$sep. $data;
        return self::_writeLogToFile($dir, $msg , true , false);
    }
    
    
    /**
     * 心跳LOG
     * @author Uqidi
     * @param string $name
     * @return bool
     */
    public static function heartbeatLog($name){
        $file = SYS_LOG_DATA_PATH . "/heartbeat";
        T_File::check_path($file);
        $file .= '/'.$name.'.log';
            
        return touch($file);
    }

    /**
     * 写debug日志共用方法体
     * @author Uqidi
     * @param $path
     * @param string $msg 日志信息
     * @param bool $savebydate
     * @param bool $extinfo
     * @return bool|int
     */
    private static function _writeLogToFile ($path, $msg, $savebydate = true , $extinfo = true) {
        $now = time();
        $now_date = date("Y-m-d", $now);
        $now_time = date("Y-m-d H:i:s", $now);
        if ($savebydate) {
            $fname = $path . '/' . $now_date . "_" . T_Ip::getLocalLastIp() . '.v7.log';
        } else  {
            $fname = $path . '/file.log';
        }

        T_File::check_path($fname);

        if ($extinfo){
            $msg = T_Ip::get_real_ip().SYS_LOG_SEPARATE.$now_time.SYS_LOG_SEPARATE.$msg . SYS_LOG_SEPARATE.$_SERVER['REQUEST_URI'];
        }
        $logid = 'logid['.SYSDEF_LOG_ID.']';
        $msg = $logid.SYS_LOG_SEPARATE.$msg."\n";

        $fp = fopen($fname, 'a');
        if ($fp){
            $r = fwrite($fp, $msg);
            fclose($fp);
            return $r;
        }
        return false;
    }
    
    private static function _formatActLog($array){
    	return implode(',' , $array);
    }
    
    public static function oneLog($type , $name , $content){
        $dir = SYS_LOG_DEBUG_PATH . "/" . $type;

        T_File::check_path($dir);
        
        $path = $dir.'/'.$name;
        file_put_contents($path , $content);
        return true;
    }

    public static function getParam(){
        if(isset($_REQUEST['param']))
            return json_decode($_REQUEST['param'], true);
        else
            return $_REQUEST;

    }

    public static function getRequest(){
        $req = ('POST' == $_SERVER["REQUEST_METHOD"]) ? $_POST : $_GET;
        if(empty($req)){
            return '';
        }

        foreach(self::$request_nolog as $vo){
            unset($req[$vo]);
        }
        $str = json_encode($req);
        $str = str_replace('\"', '"', $str);

        if (strlen($str) <= 200) {
            return $str;
        } else {
            return substr($str, 0, 200) . '...';

        }
    }

    public static function pushLog($key,$value){
        self::$_save[$key] = $value;
    }

    public static  function setLoginfo($keys){
        $data = self::getParam();
        foreach($data as $k=>$v){
            if(in_array($k, $keys)){
                self::pushLog($k, $v);
            }
        }
    }

    /**
     * 请求日志 每个请求只会有一个
     */
    public static function requestLog(){
        $log = '';
        $len = count(self::$_save);
        $idx = 1;
        foreach (self::$_save as $k=>$v) {
            $log .= "$k:$v";
            if ( $idx++ < $len ) { $log .= self::LOG_SEP; }
        }

        $msg = sprintf("req[%s]".SYS_LOG_SEPARATE."loginfo[%s]".SYS_LOG_SEPARATE."time[%s]", self::getRequest(), $log, Timer::toString());

        return self::monitorLog('request', $msg, self::LOG_LEVEL_NOTICE);
    }

    /**
     * 任务日志，每次任务只会有一个
     */
    public static function cronLog(){
        $log = '';
        $len = count(self::$_save);
        $idx = 1;
        foreach (self::$_save as $k=>$v) {
            $log .= "$k:$v";
            if ( $idx++ < $len ) { $log .= self::LOG_SEP; }
        }

        $msg = sprintf("loginfo[%s]".SYS_LOG_SEPARATE."time[%s]", $log, Timer::toString());
        return self::monitorLog('cron', $msg, self::LOG_LEVEL_NOTICE);
    }
}