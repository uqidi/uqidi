<?php
/**
 * @fileoverview:   Loader
 * @author:         Uqidi
 * @date:           2015-12-04
 * @copyright:      Uqidi
 */
class Rds{
    const OUTTIME = 2.5;
    static private $conns = array();
	private $_redis;
    private $_key;
	function __construct($key){
        $this->_key = $key;
	}

	public function connect($servers, $id) {
        $hash = T_Uqidi::calc_hash_db($id , count($servers));
        $server = $servers[$hash];
		$key = md5($server['host'].$server['port']);
		if(isset(self::$conns[$key]) && self::$conns[$key]->ping()){
			$this->_redis = self::$conns[$key];
			return true;
		}

        $this->_redis = new Redis();
        $rs = $this->_redis->connect($server['host'] , $server['port'], self::OUTTIME);
        if(false === $rs){
            T_Logger::monitorLog(__CLASS__, "connect '{$this->_key}' fail");
            throw new K_Exception('redis connect failed');
        }
        self::$conns[$key] = $this->_redis;
		return true;
	}

    /**
     * 获取批量字符类型的数据
     * @param $keys
     * @return bool
     */
    public function mget($keys) {
        $rs = $this->_redis->getMultiple($keys);
        if(false === $rs){
            T_logger::monitorLog(__CLASS__, __FUNCTION__.':'.$this->_redis->getLastError(), T_Logger::LOG_LEVEL_ERROR);
            return false;
        }
        return $rs;
	}

    /**
     * @param $methodName
     * @param $arguments
     * @return mixed
     */
    public function __call($methodName, $arguments){
        if($this->_redis) {
            $rs = call_user_func_array(array($this->_redis, $methodName), $arguments);
            if(false === $rs){
                T_logger::monitorLog(__CLASS__, $methodName.':'.$this->_redis->getLastError(), T_Logger::LOG_LEVEL_ERROR);
                return false;
            }
            return $rs;
        }
    }
}