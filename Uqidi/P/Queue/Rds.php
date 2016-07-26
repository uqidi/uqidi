<?php
class P_Queue_Rds{
    private	$_rsKey;
    private $_server;
    private $_Rds;
    private	$_rsIsConn = false;

    public function connect(){
        $this->_Rds = K_Cache::getInstance($this->getKey(), C('queue_type'), 1, $this->getServer());
    	if(!$this->_Rds){
    		$this->_rsIsConn = false;
    		return false;
    	}
    	$this->_rsIsConn = true;
    	return true;
    }

    public function getVersion(){
        return 'Redis';
    }
    public function getServer(){
        return empty($this->_server) ?  $this->_server = Loader::loadConfig('queue', $this->getKey()) : $this->_server;

    }

    public function isConn(){
        return $this->_rsIsConn;
    }

    public function getKey(){
        return $this->_rsKey;
    }
    
    public function setKey($key){
    	$this->_rsKey = $key;
    }

    public function setServer($server=array()){
        $this->_server = empty($server) ?  Loader::loadConfig('queue', $this->getKey()) : $server;
    }

    public function set($value){
        $rs = $this->_Rds->lPush($this->_rsKey, $value);
        return $rs;
    }

    public function get(){
        $rs = $this->_Rds->rPop($this->_rsKey);
        return $rs;
    }

    public function close(){
        $this->_Rds->close();
    }

    public function __destruct(){
        $this->close();
    }

    public function getSize(){
        return $this->_Rds->lSize($this->_rsKey);
    }
}