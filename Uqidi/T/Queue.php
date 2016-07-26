<?php
/**
 * @fileoverview:   队列插件
 * @author:         Uqidi
 * @date:           2015-11-15
 * @copyright:      Uqidi.com
 */
class T_Queue{
    static private $_arrMq = array();   /* 对象池 */
    static private $_oMq;               /* 当前对象 */

    /**
     * 初始化队列资源对象
     * @author Uqidi
     * @param $key
     * @return bool
     */
    static private function _init($key){
        $servers = Loader::loadConfig('queue', $key);
        if(empty(self::$_arrMq[$key])){
            self::$_oMq = K_Cache::getInstance($key, C('queue_type'), 1, $servers);
            if(!self::$_oMq){
                return false;
            }
            self::$_arrMq[$key] = self::$_oMq;
        }else{
            self::$_oMq = self::$_arrMq[$key];
            if(!self::$_oMq){
                self::$_oMq = null;
                return false;
            }
        }
        return true;
    }

    /**
     * 数据进队列
     * @author Uqidi
     * @param string $key 队列KEY
     * @param array $data
     * @return bool
     */
    public static function into($key, $data=array()){
        $data['__meta__'] = array(
            't' => time(),
            'sip' => T_Ip::getLocalLastIp()
        );
        $data = serialize($data);
        if(self::_init($key) && self::$_oMq->lPush($key, $data)){
            T_Logger::dataLog('QUEUE_SUCC' , $data, true, true);
            return true;
        }
        T_Logger::dataLog('QUEUE_FAIL' , $data, true, true);
        return false;
    }

    /**
     * 数据出队列
     * @author Uqidi
     * @param string $key 队列KEY
     * @return bool
     */
    public static function out($key){
        if(self::_init($key)){
            $data = self::$_oMq->rPop($key);
            if(false === $data){
                return false;
            }
            return unserialize($data);
        }
        return false;
    }


    public static function __callStatic($name, $arguments){
        list($pre, $key) = explode('_', $name, 2);
        if($pre === 'into'){
            var_dump($key, $arguments[0]);
            return self::into($key, $arguments[0]);
        }elseif($pre === 'out'){
            return self::out($key);
        }
    }

}