<?php
/**
 * @fileoverview:   DB
 * @author:         Uqidi
 * @date:           2015-11-22
 * @copyright:      Uqidi
 */

class K_Db{
    const CONN_SLOW_TIME    = 1;                    /* Slow connection time */
    const CONN_TRY_CNT      = 3;                    /* Connection retry */
    const SELECT_DB_TRY_CNT = 2;                    /* select database retry */
    const DB_MASTER         = 'master';
    const DB_SLAVE          = 'slave';

    private static $_instances = array();             /* instances */
    protected static $_conns;                         /* connect pool */
    protected $debug;                                 /* debug mode */
    protected $_hash_key           = 0;               /* partition table or database key */
    protected $_db_conf;                              /* database config */
    protected $_conn;                                 /* current database connect */
    protected $_conn_id            = null;            /* current link id */
    protected $_num_rows           = 0;               /* mysql_num_rows */
    protected $_db_type            = 'mysql';         /* database driver type*/
    protected $_error              = array();

    static protected $transTimes = array();                        /* transaction */

    /* database expression */
    protected $exp = array('eq'=>'=','neq'=>'<>','gt'=>'>','egt'=>'>=','lt'=>'<','elt'=>'<=','notlike'=>'NOT LIKE','like'=>'LIKE','in'=>'IN','notin'=>'NOT IN','not in'=>'NOT IN','between'=>'BETWEEN','notbetween'=>'NOT BETWEEN','not between'=>'NOT BETWEEN');
    /* sql expression */
    protected $selectSql  = 'SELECT%DISTINCT% %FIELD% FROM %TABLE%%JOIN%%WHERE%%GROUP%%HAVING%%ORDER%%LIMIT% %UNION%%COMMENT%';
    /**
     * __construct
     * @access private
     * @param array $db_conf
     */
    private function __construct($db_conf=array()){
        $this->_db_conf = $db_conf;
        if(defined('DEBUG') && DEBUG)
            $this->debug = true;
        isset($db_conf['db_type'][0]) ? $this->_db_type = $db_conf['db_type'] : '';
    }

    /**
     * getInstance
     * @access public static
     * @param array $db_conf
     * @return mixed  return database driver
     */
    public static function getInstance($db_conf=array()){
        if(empty($db_conf))
            return false;

        $key = to_guid_string($db_conf);
        if(!isset(self::$_instances[$key]) || !is_resource(self::$_instances[$key])){
            $db_type = ucwords(strtolower($db_conf['db_type']));
            $dbClass = $db_type;
            require_once(UQIDI_PATH.'K/Db/'.$dbClass . '.php');
            self::$_instances[$key] = new $dbClass($db_conf);
        }
        return self::$_instances[$key];
    }

    /**
     * set lock
     * @access protected
     * @param bool $lock
     * @return string
     */
    protected function parseLock($lock=false) {
        if(!$lock) return '';
        if('ORACLE' == $this->_db_type) {
            return ' FOR UPDATE NOWAIT ';
        }
        return ' FOR UPDATE ';
    }

    /**
     * set
     * @access protected
     * @param array $data
     * @return string
     */
    protected function parseSet($data) {
        foreach ($data as $key=>$val){
            if(is_array($val) && 'exp' == $val[0]){
                $set[]  =   $this->parseKey($key).'='.$val[1];
            }elseif(is_scalar($val) || is_null($val)) {
                /* 过滤非标量数据 */
                $set[]  =   $this->parseKey($key).'='.$this->parseValue($val);
            }
        }
        return ' SET '.implode(',',$set);
    }

    /**
     * 字段名分析
     * @access protected
     * @param string $key
     * @return string
     */
    protected function parseKey(&$key) {
        return $key;
    }

    /**
     * value
     * @access protected
     * @param mixed $value
     * @return string
     */
    protected function parseValue($value) {
        if(is_string($value)) {
            $value =  '\''.$this->escapeString($value).'\'';
        }elseif(isset($value[0]) && is_string($value[0]) && strtolower($value[0]) == 'exp'){
            $value =  $this->escapeString($value[1]);
        }elseif(is_array($value)) {
            $value =  array_map(array($this, 'parseValue'),$value);
        }elseif(is_bool($value)){
            $value =  $value ? '1' : '0';
        }elseif(is_null($value)){
            $value =  'null';
        }
        return $value;
    }

    /**
     * field
     * @access protected
     * @param mixed $fields
     * @return string
     */
    protected function parseField($fields) {
        if(is_string($fields) && strpos($fields,',')) {
            $fields    = explode(',',$fields);
        }

        if(is_array($fields)) {
            /* Perfect array transmission field support */
            /* Support 'field1'=>'field2' this field alias definition */
            $array   =  array();
            foreach ($fields as $key=>$field){
                if(!is_numeric($key))
                    $array[] =  $this->parseKey($key).' AS '.$this->parseKey($field);
                else
                    $array[] =  $this->parseKey($field);
            }
            $fieldsStr = implode(',', $array);
        }elseif(is_string($fields) && !empty($fields)) {
            $fieldsStr = $this->parseKey($fields);
        }else{
            $fieldsStr = '*';
        }

        return $fieldsStr;
    }

    /**
     * table
     * @access protected
     * @param $tables
     * @return string
     */
    protected function parseTable($tables) {
        if(is_array($tables)) {
            /* 支持别名定义 */
            $array   =  array();
            foreach ($tables as $table=>$alias){
                if(!is_numeric($table))
                    $array[] =  $this->parseKey($table).' '.$this->parseKey($alias);
                else
                    $array[] =  $this->parseKey($table);
            }
            $tables  =  $array;
        }elseif(is_string($tables)){
            $tables  =  explode(',',$tables);
            array_walk($tables, array(&$this, 'parseKey'));
        }
        $tables = implode(',',$tables);
        return $tables;
    }

    /**
     * where
     * @access protected
     * @param mixed $where
     * @return string
     */
    protected function parseWhere($where) {
        $whereStr = '';
        if(is_string($where)) {
            /* 直接使用字符串条件 */
            $whereStr = $where;
        }else{
            /* 使用数组表达式 */
            $operate  = isset($where['_logic'])?strtoupper($where['_logic']):'';
            if(in_array($operate,array('AND','OR','XOR'))){
                /* 定义逻辑运算规则 例如 OR XOR AND NOT */
                $operate    =   ' '.$operate.' ';
                unset($where['_logic']);
            }else{
                /* 默认进行 AND 运算 */
                $operate    =   ' AND ';
            }
            foreach ($where as $key=>$val){
                if(is_numeric($key)){
                    $key  = '_complex';
                }
                if(0===strpos($key,'_')) {
                    /* 解析特殊条件表达式 */
                    $whereStr  .= $this->parseSpWhere($key,$val);
                }else{
                    /* 查询字段的安全过滤 */
                    if(!preg_match('/^[A-Z_\|\&\-.a-z0-9\(\)\,]+$/',trim($key))){
                        $this->_error = '_EXPRESS_ERROR_:'.$key;
                        return false;
                    }
                    /* 多条件支持 */
                    $multi  = is_array($val) &&  isset($val['_multi']);
                    $key    = trim($key);
                    if(strpos($key,'|')) {
                        /* 支持 name|title|nickname 方式定义查询字段 */
                        $array =  explode('|',$key);
                        $str   =  array();
                        foreach ($array as $m=>$k){
                            $v =  $multi?$val[$m]:$val;
                            $str[]   = $this->parseWhereItem($this->parseKey($k),$v);
                        }
                        $whereStr .= '( '.implode(' OR ',$str).' )';
                    }elseif(strpos($key,'&')){
                        $array =  explode('&',$key);
                        $str   =  array();
                        foreach ($array as $m=>$k){
                            $v =  $multi?$val[$m]:$val;
                            $str[]   = '('.$this->parseWhereItem($this->parseKey($k),$v).')';
                        }
                        $whereStr .= '( '.implode(' AND ',$str).' )';
                    }else{
                        $whereStr .= $this->parseWhereItem($this->parseKey($key),$val);
                    }
                }
                $whereStr .= $operate;
            }
            $whereStr = substr($whereStr,0,-strlen($operate));
        }
        return empty($whereStr)?'':' WHERE '.$whereStr;
    }

    /**
     * where子单元分析
     * @access protected
     * @param $key
     * @param $val
     * @return string
     */
    protected function parseWhereItem($key,$val) {
        $whereStr = '';
        if(is_array($val)) {
            if(is_string($val[0])) {
                $exp	=	strtolower($val[0]);
                if(preg_match('/^(EQ|NEQ|GT|EGT|LT|ELT)$/i',$val[0])) {
                    /* 比较运算 */
                    $whereStr .= $key.' '.$this->exp[$exp].' '.$this->parseValue($val[1]);
                }elseif(preg_match('/^(NOTLIKE|LIKE)$/i',$val[0])){
                    /* 模糊查找 */
                    if(is_array($val[1])) {
                        $likeLogic  =   isset($val[2])?strtoupper($val[2]):'OR';
                        if(in_array($likeLogic,array('AND','OR','XOR'))){
                            $like       =   array();
                            foreach ($val[1] as $item){
                                $like[] = $key.' '.$this->exp[$exp].' '.$this->parseValue($item);
                            }
                            $whereStr .= '('.implode(' '.$likeLogic.' ',$like).')';
                        }
                    }else{
                        $whereStr .= $key.' '.$this->exp[$exp].' '.$this->parseValue($val[1]);
                    }
                }elseif('exp'==$exp){
                    /* 使用表达式 */
                    $whereStr .= $key.' '.$val[1];
                }elseif(preg_match('/^(NOTIN|NOT IN|IN)$/i',$val[0])){
                    /* IN 运算 */
                    if(isset($val[2]) && 'exp'==$val[2]) {
                        $whereStr .= $key.' '.$this->exp[$exp].' '.$val[1];
                    }else{
                        if(is_string($val[1])) {
                            $val[1] =  explode(',',$val[1]);
                        }
                        $zone      =   implode(',',$this->parseValue($val[1]));
                        $whereStr .= $key.' '.$this->exp[$exp].' ('.$zone.')';
                    }
                }elseif(preg_match('/^(NOTBETWEEN|NOT BETWEEN|BETWEEN)$/i',$val[0])){
                    /* BETWEEN 运算 */
                    $data = is_string($val[1])? explode(',',$val[1]):$val[1];
                    $whereStr .=  $key.' '.$this->exp[$exp].' '.$this->parseValue($data[0]).' AND '.$this->parseValue($data[1]);
                }else{
                    $this->_error = '_EXPRESS_ERROR_:'.$val[0];
                    return false;
                }
            }else {
                $count = count($val);
                $rule  = isset($val[$count-1]) ? (is_array($val[$count-1]) ? strtoupper($val[$count-1][0]) : strtoupper($val[$count-1]) ) : '' ;
                if(in_array($rule,array('AND','OR','XOR'))) {
                    $count  = $count -1;
                }else{
                    $rule   = 'AND';
                }
                for($i=0;$i<$count;$i++) {
                    $data = is_array($val[$i])?$val[$i][1]:$val[$i];
                    if('exp'==strtolower($val[$i][0])) {
                        $whereStr .= $key.' '.$data.' '.$rule.' ';
                    }else{
                        $whereStr .= $this->parseWhereItem($key,$val[$i]).' '.$rule.' ';
                    }
                }
                $whereStr = '( '.substr($whereStr,0,-4).' )';
            }
        }else {
            $whereStr .= $key.' = '.$this->parseValue($val);
        }
        return $whereStr;
    }

    /**
     * Special condition analysis
     * @access protected
     * @param string $key
     * @param mixed $val
     * @return string
     */
    protected function parseSpWhere($key,$val) {
        $whereStr   = '';
        switch($key) {
            case '_string':
                /* The query string pattern */
                $whereStr = $val;
                break;
            case '_complex':
                /* Complex query conditions */
                $whereStr   =   is_string($val)? $val : substr($this->parseWhere($val),6);
                break;
            case '_query':
                /* The query string pattern */
                parse_str($val,$where);
                if(isset($where['_logic'])) {
                    $op   =  ' '.strtoupper($where['_logic']).' ';
                    unset($where['_logic']);
                }else{
                    $op   =  ' AND ';
                }
                $array   =  array();
                foreach ($where as $field=>$data)
                    $array[] = $this->parseKey($field).' = '.$this->parseValue($data);
                $whereStr   = implode($op,$array);
                break;
        }
        return $whereStr;
    }

    /**
     * limit
     * @access protected
     * @param $limit
     * @return string
     */
    protected function parseLimit($limit) {
        return !empty($limit)?   ' LIMIT '.$limit.' ':'';
    }

    /**
     * join
     * @access protected
     * @param mixed $join
     * @return string
     */
    protected function parseJoin($join) {
        $joinStr = '';
        if(!empty($join)) {
            $joinStr    =   ' '.implode(' ',$join).' ';
        }
        return $joinStr;
    }

    /**
     * order
     * @access protected
     * @param mixed $order
     * @return string
     */
    protected function parseOrder($order) {
        if(is_array($order)) {
            $array   =  array();
            foreach ($order as $key=>$val){
                if(is_numeric($key)) {
                    $array[] =  $this->parseKey($val);
                }else{
                    $array[] =  $this->parseKey($key).' '.$val;
                }
            }
            $order   =  implode(',',$array);
        }
        return !empty($order)?  ' ORDER BY '.$order:'';
    }

    /**
     * group
     * @access protected
     * @param mixed $group
     * @return string
     */
    protected function parseGroup($group)
    {
        return !empty($group)? ' GROUP BY '.$group:'';
    }

    /**
     * having分析
     * @access protected
     * @param string $having
     * @return string
     */
    protected function parseHaving($having)
    {
        return  !empty($having)?   ' HAVING '.$having:'';
    }

    /**
     * comment分析
     * @access protected
     * @param string $comment
     * @return string
     */
    protected function parseComment($comment) {
        return  !empty($comment)?   ' /* '.$comment.' */':'';
    }

    /**
     * distinct分析
     * @access protected
     * @param mixed $distinct
     * @return string
     */
    protected function parseDistinct($distinct) {
        return !empty($distinct)?   ' DISTINCT ' :'';
    }

    /**
     * union分析
     * @access protected
     * @param mixed $union
     * @return string
     */
    protected function parseUnion($union) {
        if(empty($union)) return '';
        if(isset($union['_all'])) {
            $str  =   'UNION ALL ';
            unset($union['_all']);
        }else{
            $str  =   'UNION ';
        }
        foreach ($union as $u){
            $sql[] = $str.(is_array($u)?$this->buildSelectSql($u):$u);
        }
        return implode(' ',$sql);
    }
    /**
     * 插入记录
     * @access public
     * @param mixed $datas 数据
     * @param array $options 参数表达式
     * @param boolean $replace 是否replace
     * @return false | integer
     */
    public function insertAll($datas,$options=array(),$replace=false) {
        if(!is_array(reset($datas))) return false;
        $fields = array_keys($datas[0]);
        array_walk($fields, array($this, 'parseKey'));
        $values  =  array();
        foreach ($datas as $data){
            $value   =  array();
            foreach ($data as $key=>$val){
                $val   =  $this->parseValue($val);
                if(is_scalar($val)) { // 过滤非标量数据
                    $value[]   =  $val;
                }
            }
            $values[]    = '('.implode(',', $value).')';
        }
        $sql   =  ($replace?'REPLACE':'INSERT').' INTO '.$this->parseTable($options['table']).' ('.implode(',', $fields).') VALUES '.implode(',',$values);
        return $this->execute($sql);
    }


    /**
     * 插入记录
     * @access public
     * @param mixed $data 数据
     * @param array $options 参数表达式
     * @param boolean $replace 是否replace
     * @return false | integer
     */
    public function insert($data,$options=array(),$replace=false) {
        $values  =  $fields    = array();
        $this->model  =   $options['model'];
        foreach ($data as $key=>$val){
            if(is_array($val) && 'exp' == $val[0]){
                $fields[]   =  $this->parseKey($key);
                $values[]   =  $val[1];
            }elseif(is_scalar($val) || is_null($val)) {
                /* 过滤非标量数据 */
                $fields[]   =  $this->parseKey($key);
                $values[]   =  $this->parseValue($val);
            }
        }
        $sql   =  ($replace?'REPLACE':'INSERT').' INTO '.$this->parseTable($options['table']).' ('.implode(',', $fields).') VALUES ('.implode(',', $values).')';
        $sql   .= $this->parseLock(isset($options['lock'])?$options['lock']:false);
        $sql   .= $this->parseComment(!empty($options['comment'])?$options['comment']:'');
        return $this->execute($sql);
    }

    /**
     * 替换记录
     * @access public
     * @param mixed $data 数据
     * @param array $options 参数表达式
     * @return false | integer
     */
    public function replace($data,$options=array()) {
        foreach ($data as $key=>$val){
            $value   =  $this->parseValue($val);
            if(is_scalar($value)) {
                /* 过滤非标量数据 */
                $values[]   =  $value;
                $fields[]     =  $this->parseKey($key);
            }
        }
        $sql   =  'REPLACE INTO '.$this->parseTable($options['table']).' ('.implode(',', $fields).') VALUES ('.implode(',', $values).')';
        return $this->execute($sql);
    }

    /**
     * Record insertion by Select
     * @access public
     * @param string $fields
     * @param string $table
     * @param array $options
     * @return false | integer
     */
    public function selectInsert($fields,$table,$options=array()) {
        if(is_string($fields))   $fields    = explode(',',$fields);
        array_walk($fields, array($this, 'parseKey'));
        $sql   =    'INSERT INTO '.$this->parseTable($table).' ('.implode(',', $fields).') ';
        $sql   .= $this->buildSelectSql($options);
        return $this->execute($sql);
    }

    /**
     * Update records
     * @access public
     * @param mixed $data
     * @param array $options
     * @return false | integer
     */
    public function update($data,$options) {
        $where = $this->parseWhere(isset($options['where'])?$options['where']:'');
        if(false === $where)
            return false;
        $sql   = 'UPDATE '
            .$this->parseTable($options['table'])
            .$this->parseSet($data)
            .$where
            .$this->parseOrder(isset($options['order'])?$options['order']:'')
            .$this->parseLimit(isset($options['limit'])?$options['limit']:'')
            .$this->parseLock(isset($options['lock'])?$options['lock']:false)
            .$this->parseComment(!empty($options['comment'])?$options['comment']:'');
        return $this->execute($sql);
    }

    /**
     * Delete records
     * @access public
     * @param array $options
     * @return false | integer
     */
    public function delete($options=array()){
        $where = $this->parseWhere(isset($options['where'])?$options['where']:'');
        if(false === $where)
            return false;
        $sql   = 'DELETE FROM '
            .$this->parseTable($options['table'])
            .$where
            .$this->parseOrder(isset($options['order'])?$options['order']:'')
            .$this->parseLimit(isset($options['limit'])?$options['limit']:'')
            .$this->parseLock(isset($options['lock'])?$options['lock']:false)
            .$this->parseComment(!empty($options['comment'])?$options['comment']:'');
        return $this->execute($sql);
    }

    /**
     * Find records
     * @access public
     * @param array $options
     * @param array $data
     * @param string $key
     * @return array
     */
    public function select($options=array(), &$data=array(), $key='') {
        $sql        =   $this->buildSelectSql($options);
        return $this->query($sql,$data, $key);
    }

    /**
     * 生成查询SQL
     * @access public
     * @param array $options 表达式
     * @return string
     */
    public function buildSelectSql($options=array()) {
        if(isset($options['page'])) {
            /* 根据页数计算limit */
            list($page,$listRows)   =   $options['page'];
            $page = intval($page);
            $listRows = intval($listRows);
            $page    =  $page>0 ? $page : 1;
            $listRows=  $listRows>0 ? $listRows : (is_numeric($options['limit'])?$options['limit']:20);
            $offset  =  $listRows*($page-1);
            $options['limit'] =  $offset.','.$listRows;
        }

        $sql  =     $this->parseSql($this->selectSql,$options);
        $sql .=     $this->parseLock(isset($options['lock'])?$options['lock']:false);
        return $sql;
    }

    /**
     * 替换SQL语句中表达式
     * @access public
     * @param $sql
     * @param array $options 表达式
     * @return string
     */
    public function parseSql($sql,$options=array()){
        $where = $this->parseWhere(!empty($options['where'])?$options['where']:'');
        if(false === $where)
            return false;
        $sql   = str_replace(
            array('%TABLE%','%DISTINCT%','%FIELD%','%JOIN%','%WHERE%','%GROUP%','%HAVING%','%ORDER%','%LIMIT%','%UNION%','%COMMENT%'),
            array(
                $this->parseTable($options['table']),
                $this->parseDistinct(isset($options['distinct'])?$options['distinct']:false),
                $this->parseField(!empty($options['field'])?$options['field']:'*'),
                $this->parseJoin(!empty($options['join'])?$options['join']:''),
                $where,
                $this->parseGroup(!empty($options['group'])?$options['group']:''),
                $this->parseHaving(!empty($options['having'])?$options['having']:''),
                $this->parseOrder(!empty($options['order'])?$options['order']:''),
                $this->parseLimit(!empty($options['limit'])?$options['limit']:''),
                $this->parseUnion(!empty($options['union'])?$options['union']:''),
                $this->parseComment(!empty($options['comment'])?$options['comment']:'')
            ),$sql);
        return $sql;
    }

    /**
     * SQL指令安全过滤
     * @access public
     * @param string $str
     * @return string
     */
    public function escapeString($str) {
        return addslashes($str);
    }

    /**
     * __destruct
     * @access public
     */
    public function __destruct() {
        $this->close();
    }

    /* 关闭数据库 由驱动类定义 */
    public function close(){}
}