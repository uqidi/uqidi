<?php
/**
 * @fileoverview:   Loader
 * @author:         Uqidi
 * @date:           2015-12-04
 * @copyright:      Uqidi
 */
class Mcd{
    private $_mc;
    private $_key;
    public function __construct($key){
        $this->_key = $key;
    }

    /**
     * 连接
     * @param $servers
     * @param int $is_short
     * @return Memcached
     * @throws K_Exception
     */
    public function connect($servers, $is_short=0){
        if($is_short)
            $this->_mc = new Memcached();
        else
            $this->_mc = new Memcached($this->_key);


        if (empty($this->_mc)) {
            T_Logger::monitorLog(__CLASS__ ,'Memcached obj failed', T_Logger::LOG_LEVEL_ERROR);
            throw new K_Exception('Memcached obj failed');
        }


        /* 分布式一致性hash算法 */
        $this->_mc->setOption(Memcached::OPT_DISTRIBUTION,Memcached::DISTRIBUTION_CONSISTENT);

        /* 该设置使对key的hash采用md5,一致性hash算法支持权重分配 */
        $this->_mc->setOption(Memcached::OPT_LIBKETAMA_COMPATIBLE, true);


        /* 对于长连接，请求释放后，长连接里面的服务配置是不会释放的，因此非空(第一次)才增加服务配置 */
        if (!count($this->_mc->getServerList()) ) {
            $this->_mc->addServers($servers);

        }
        return $this->_mc;
    }
    /**
     * 关闭服务
     * @return mixed
     */
    public function  close(){
        return true;
    }

    /**
     * @param $methodName
     * @param $arguments
     * @return mixed
     */
    public function __call($methodName, $arguments){
        if($this->_mc) {
            $result = call_user_func_array(array($this->_mc, $methodName), $arguments);
            return $result;
        }
    }
}