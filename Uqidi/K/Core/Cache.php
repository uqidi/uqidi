<?php
/**
 * @fileoverview:   Loader
 * @author:         Uqidi
 * @date:           2015-12-04
 * @copyright:      Uqidi
 */
class K_Cache{
    private static $_instances = array();       /* instances */
	private $_Cache;                            /* 当前对象 */
    const CLASS_MCD     = 'Mcd';                /* Memcached */
    const CLASS_RDS     = 'Rds';                /* Redis */

    /**
     * 获取对象
     * @param string $key
     * @param string $class
     * @param int $id
     * @param array $servers
     * @return mixed
     */
    public static function getInstance($key='default', $class=self::CLASS_RDS, $id=0, $servers=array()){
        if(!isset(self::$_instances[$class]) || !is_resource(self::$_instances[$class][$key])){
            self::$_instances[$class][$key] = new self($key, $class, $id, $servers);
        }
        return self::$_instances[$class][$key];
    }

	public function __construct($key, $class, $id, $servers=array()){
        $class_path = UQIDI_PATH.'K/Cache/'.$class . '.php';
        if(!is_file($class_path)){
            throw new K_Exception('init cache '.$class);
        }

        require_once($class_path);
        if(empty($servers))
            $servers = Loader::loadConfig(strtolower($class), $key);
        if(empty($servers)){
            throw new K_Exception('config cache '.$class);
        }
        $this->_Cache = new $class($key);
        $this->connect($servers, $id);
	}

    /**
     * 连接服务
     * @param $servers
     * @param int $id
     * @throws K_Exception
     * @return mixed
     */
    public function connect($servers, $id=0) {
        if(empty($servers)){
            throw new K_Exception('servers not null');
        }

		return $this->_Cache->connect($servers, $id);
	}

    /**
     * 关闭服务
     * @return mixed
     */
    public function  close(){
		return $this->_Cache-> close();
	}

    /**
     *
     * @param $methodName
     * @param $arguments
     * @return mixed
     */
    public function __call($methodName, $arguments){
        if($this->_Cache) {
            $result = call_user_func_array(array($this->_Cache, $methodName), $arguments);
            return $result;
        }
    }
}
