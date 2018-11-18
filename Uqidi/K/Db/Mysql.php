<?php
/**
 * @fileoverview:   Mysql
 * @author:         Uqidi
 * @date:           2015-11-22
 * @copyright:      Uqidi
 */

class Mysql extends K_Db{
    static private $ping_time   = 0;    /* ping_mysql time */
    const PING_SPACE            = 1;    /* ping_mysql space time */
    /**
     * connect
     * @param array $conf
     * @return bool
     */
    private function _connect($conf){
        $this->_conn_id = $hash_key = md5($conf['host'].'_'.$conf['user']);
        
        /* not exists or not ping link */
        $flag = true;
        if(!isset(self::$_conns[$hash_key])){
            $flag = false;
            self::$ping_time = $this->_microtime();
        }else{
            $diff = $this->_microtime()-self::$ping_time;
            if($diff>self::PING_SPACE){
                $flag = mysql_ping(self::$_conns[$hash_key]);
                self::$ping_time = $this->_microtime();
            }
        }

        if(!$flag){
        	if(is_resource(self::$_conns[$hash_key])){
        		$this->close($hash_key);
            }

        	$start = $this->_microtime();
        	
        	T_Logger::debugLog(__CLASS__  , 'connect '.$conf['host']);

            /* connect mysql */
            for ($i=0; $i<self::CONN_TRY_CNT; $i++){
                $conn = mysql_connect($conf['host'] , $conf['user'] , $conf['pw']);
                if($conn !== false)
                    break;
            }

            /* connect fail max */
            if(!$conn){
                $this->_error = mysql_error();
                T_Logger::monitorLog(__CLASS__ , 'conn_err '.$conf['host'].' '.$conf['user'].' '.mysql_error() , T_Logger::LOG_LEVEL_ALERM );
                return false;
            }
            
            /* connect fail */
            if($i>0)
                T_Logger::monitorLog(__CLASS__ , 'conn_fail '.$conf['host'].' '.$i.' times' , T_Logger::LOG_LEVEL_NOTICE );
            
            $t = $this->_microtime() - $start;

            /* connect slow */
            if($t>self::CONN_SLOW_TIME)
                T_Logger::monitorLog(__CLASS__ , 'conn_slow '.$conf['host'].' '.$t.' s' , T_Logger::LOG_LEVEL_NOTICE );
            	
            self::$_conns[$hash_key] = $conn;
        }
        
        $this->_conn = self::$_conns[$hash_key];
        
        mysql_set_charset($conf['charset'], $this->_conn);
        
    	/* select database */
        for ($i=0; $i<self::SELECT_DB_TRY_CNT; $i++){
            $rs = mysql_select_db($conf['db_name'] , self::$_conns[$hash_key]);
            if($rs === false){
                $this->_error = mysql_error($this->_conn);
                T_Logger::monitorLog(__CLASS__ , 'select_db_err '.$conf['host'].' '.$conf['user'].' '.$conf['db_name'].' '.mysql_error($this->_conn) , T_Logger::LOG_LEVEL_ALERM );
            }else{
                break;
            }
        }

        return $rs;
    }

    /* *
     * init database
     * @desc: The database operation to prepare before each database operation calls the inside of the package database pool and table logic
     * @param mix $hash_key         On the basis of hash;default is UID
     * @param string $type          database type master slave  or other(user-defined)
     * @return bool                 If the connection is successful
     */
    protected function init_db($type=self::DB_MASTER){
        if(!isset($this->_db_conf['connect'][$type])){
            T_Logger::monitorLog(__CLASS__ , 'conn_type_err '.$type , T_Logger::LOG_LEVEL_ALERM);
            return false;
        }
        
        /* partition database */
        $db_key = 0;
        $n = count($this->_db_conf['connect'][$type]['host']);
        if($n > 1)
            $db_key = T_Uqidi::calc_hash_db($this->_hash_key, $n);
            
        $conf['host']    = $this->_db_conf['connect'][$type]['host'][$db_key];
        $conf['user']    = $this->_db_conf['connect'][$type]['user'];
        $conf['pw']      = $this->_db_conf['connect'][$type]['pw'];
        $conf['db_name'] = $this->_db_conf['connect'][$type]['name'];
        $conf['charset'] = isset($this->_db_conf['charset'][0]) ? $this->_db_conf['charset'][0] : 'utf8';
     
        /* connect database */
        $rs = $this->_connect($conf);

        if(!$rs)
            return false;

        return true;
    }

    public  function set_hash_key($hash_key=0){
        $this->_hash_key = $hash_key;
    }

    /**
     * exec SQL
     * @param string $sql
     * @param array $data
     * @param string $key
     * @return bool  If the operation is successful
     */
    public function query($sql, &$data=array(), $key=''){
        $this->init_db(self::DB_SLAVE);
    	if (DEBUG){
    	    T_Logger::debugLog(__CLASS__ , $sql);
    		$this->lastSQL = $sql;
    		$this->allSQL[] = $sql;
    	}

        /* check connect */
        if(!is_resource($this->_conn)){
            T_Logger::monitorLog(__CLASS__ , 'query_no_conn '.$sql , T_Logger::LOG_LEVEL_ALERM );
            return false;
        }
            
        $start = $this->_microtime();
        $rs = mysql_query($sql , $this->_conn);;

        $t = $this->_microtime() - $start;
        if(false === $rs){
            $this->_error = mysql_error($this->_conn);
            T_Logger::monitorLog(__CLASS__ , 'query_err '.$sql.' '.mysql_error($this->_conn) , T_Logger::LOG_LEVEL_ALERM );
            return false;
        }
        if($t>self::CONN_SLOW_TIME)
            T_Logger::monitorLog(__CLASS__ , 'query_slow '.$sql.' '.$t , T_Logger::LOG_LEVEL_NOTICE );

        $this->_num_rows = mysql_num_rows($rs);
        if($this->_num_rows<=0)
            return true;

        /* return data */
        while(($row = mysql_fetch_assoc($rs)) !== false){
            if(is_string($key) && isset($key[0]))
                $data[$row[$key]] = $row;
            else
                $data[] = $row;
        }

        @mysql_free_result($rs);
        return true;
    }


    /**
     * exec SQL
     * @param string $sql
     * @return bool  If the operation is successful
     */
    public function execute($sql){
        $this->init_db(self::DB_MASTER);
        if ($this->debug){
            T_Logger::debugLog(__CLASS__ , $sql);
            $this->lastSQL = $sql;
            $this->allSQL[] = $sql;
        }

        /* check connect */
        if(!is_resource($this->_conn)){
            T_Logger::monitorLog(__CLASS__ , 'exec_no_conn '.$sql , T_Logger::LOG_LEVEL_ALERM );
            return false;
        }

        $start = $this->_microtime();
        $rs = mysql_query($sql , $this->_conn);;
        $t = $this->_microtime() - $start;
        if(false === $rs){
            $this->_error = mysql_error($this->_conn);
            T_Logger::monitorLog(__CLASS__ , 'exec_err '.$sql.' '.mysql_error($this->_conn) , T_Logger::LOG_LEVEL_ALERM );
            return false;
        }
        if($t>self::CONN_SLOW_TIME)
            T_Logger::monitorLog(__CLASS__ , 'exec_slow '.$sql.' '.$t , T_Logger::LOG_LEVEL_NOTICE );
        return true;
    }

    /**
     * get fields
     * @access public
     */
    public function getFields($tableName) {
        $rs =   $this->query('SHOW COLUMNS FROM '.$this->parseKey($tableName), $result);
        if(false === $rs)
            return false;
        $info   =   array();
        if($result) {
            foreach ($result as $val) {
                $info[$val['Field']] = array(
                    'name'    => $val['Field'],
                    'type'    => $val['Type'],
                    'notnull' => (bool) ($val['Null'] === ''), /* not null is empty, null is yes */
                    'default' => $val['Default'],
                    'primary' => (strtolower($val['Key']) == 'pri'),
                    'autoinc' => (strtolower($val['Extra']) == 'auto_increment'),
                );
            }
        }
        return $info;
    }

    /**
     * get table info
     * @access public
     */
    public function getTables($dbName='') {
        if(!empty($dbName)) {
            $sql    = 'SHOW TABLES FROM '.$dbName;
        }else{
            $sql    = 'SHOW TABLES ';
        }
        $rs =   $this->query($sql,$result);
        if(false === $rs)
            return false;
        $info   =   array();
        foreach ($result as $key => $val) {
            $info[$key] = current($val);
        }
        return $info;
    }

    /**
     * start transaction
     * @access public
     * @return boolen
     */
    public function startTrans() {
        if (!is_resource($this->_conn) ){
            $rs = $this->init_db(self::DB_MASTER);
            if(false === $rs)
                return false;

        }

        if (self::$transTimes[$this->_conn_id] == 0) {
            $result = mysql_query('START TRANSACTION', $this->_conn);
            if(false === $result){
                $this->_error = mysql_error($this->_conn);
                T_Logger::monitorLog(__CLASS__ , __METHOD__.' '.mysql_error($this->_conn) , T_Logger::LOG_LEVEL_ALERM );
                return false;
            }
        }
        self::$transTimes[$this->_conn_id]++;
        return true;
    }

    /**
     * commit transaction
     * @access public
     * @return boolen
     */
    public function commit(){
        if (self::$transTimes[$this->_conn_id] > 0) {
            $result = mysql_query('COMMIT', $this->_conn);
            self::$transTimes[$this->_conn_id] = 0;
            if(false === $result){
                $this->_error = mysql_error($this->_conn);
                T_Logger::monitorLog(__CLASS__ , __METHOD__.' '.mysql_error($this->_conn) , T_Logger::LOG_LEVEL_ALERM );
                return false;
            }
        }
        return true;
    }

    /**
     * rollback transaction
     * @access public
     * @return boolen
     */
    public function rollback(){
        if (self::$transTimes[$this->_conn_id] > 0) {
            $result = mysql_query('ROLLBACK', $this->_conn);
            self::$transTimes[$this->_conn_id] = 0;
            if(false === $result){
                $this->_error = mysql_error($this->_conn);
                T_Logger::monitorLog(__CLASS__ , __METHOD__.' '.mysql_error($this->_conn) , T_Logger::LOG_LEVEL_ALERM );
                return false;
            }
        }
        return true;
    }

    /**
     * last insert id
     *
     * @return int
     */
    public function insert_id(){
        return mysql_insert_id($this->_conn);
    }

    /**
     * affected row
     * @return int
     */
    public function affected_rows(){
        return mysql_affected_rows($this->_conn);
    }

    /**
     * num row
     * @return int
     */
    public function num_rows(){
        return $this->_num_rows;
    }

    public function getError(){
        return $this->_error;
    }
    
    /**
     * last sql
     * @return string
     */
    public function getLastSQL(){
    	if (!$this->debug)
    		return 'please define DEBUG = true';

    	return $this->lastSQL;
    }
    /**
     * Get all sql
     * @return array
     */
    public function getAllSQL(){
   		if (!$this->debug)
    		return 'please define DEBUG = true';

    	return $this->allSQL;
    }

    /* close connect */
    public function close($key=''){
        if(empty($key)){
            $key = $this->_conn_id;
            $conn = $this->_conn;
        }else{
            $conn = self::$_conns[$key];
        }
        if(is_resource($conn)){
            mysql_close($conn);
            self::$_conns[$key] = null;
        }

    }
    /**
     * SQL指令安全过滤
     * @access public
     * @param string $str  SQL字符串
     * @return string
     */
    public function escapeString($str) {
        return mysql_escape_string($str);
//        if($this->_conn) {
//            return @mysql_real_escape_string($str,$this->_conn);
//        }else{
//            return mysql_escape_string($str);
//        }
    }

    /**
     * 字段和表名处理添加`
     * @access protected
     * @param string $key
     * @return string
     */
    protected function parseKey(&$key) {
        $key   =  trim($key);
        if(!is_numeric($key) && !preg_match('/[,\'\"\*\(\)`.\s]/',$key)) {
            $key = '`'.$key.'`';
        }
        return $key;
    }

    private function _microtime(){
        return  microtime(true);
    }

    public function __destruct(){
        $this->close();
    }

}