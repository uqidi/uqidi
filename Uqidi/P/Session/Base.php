<?php
/**
 * @fileoverview:   session base
 * @author:         Uqidi
 * @date:           2015-11-15
 * @copyright:      Uqidi.com
 */
abstract class P_Session_Base{

    protected $_sid         = '';       /* PHPSESSID的值, PHPSESSID为php.ini配置的session.name的值 */
    protected $_sname       = '';
    protected $_data        = '';       /* session的值 */
    protected $_parsed      = false;    /* 判断是否已经解析过 */

    /**
     * 初始化
     * @param string $name
     * @param string $save_handler
     * @param string $save_path
     * @return bool
     */
    public function init($save_handler='', $name = '', $save_path=''){
        ini_set('session.save_handler', $save_handler);
        if(isset($save_path[0]))
            ini_set('session.save_path', $save_path);

        if(isset($name[0]) && session_name()!= $name) {
            $this->_sname = $name;
            session_name($name);
        }else{
            $this->_sname = session_name();
        }

        return true;
    }


    /**
     * 创建一个当前会话
     * @author Uqidi
     * @param array $params
     */
    public function start($params=array()){
        if (empty($_COOKIE[session_name()])) {
            session_id(md5(uniqid(microtime())));
        }

        !isset($params['domain'])   ? $params['domain']     =    SITE_HOST: '';

        if($pos = strpos($params['domain'], ':')){
            $params['domain'] = substr($params['domain'], 0, $pos);
        }

        !isset($params['lifetime']) ? $params['lifetime']    = 0        : '';
        !isset($params['path'])     ? $params['path']        = '/'      : '';
        !isset($params['secure'])   ? $params['secure']     = false     : '';
        !isset($params['httponly']) ? $params['httponly']   = false     : '';

        session_set_cookie_params(
            $params['lifetime'],
            $params['path'],
            $params['domain'],
            (bool)$params['secure'],
            (bool)$params['httponly']
        );
        session_start();
    }

    /**
     * 解析会话数据到内存数组
     */
    private function _parse(){
        if($this->_parsed)
            return true;
        $this->_parsed = true;
        if(isset($_SESSION['__SESDATA']) && !empty($_SESSION['__SESDATA'])){
            $this->_data = unserialize($_SESSION['__SESDATA']);
        }else{
            $this->_data = array();
        }
        return true;
    }

    /**
     * 获取SESSION_NAME
     * @return string
     */
    public function getName() {
        return $this->_sname;
    }

    /**
     * 获取SESSIONID
     * @return string
     */
    public function getSid() {
        $this->_sid = session_id();
        return $this->_sid;
    }

    /**
     * 获取保存在session中变量的值
     * @param $key
     * @return bool|string|array
     */
    public function get($key=''){
        $this->_parse();
        if(empty($key)){
            return $this->_data;
        }
        if(isset($key))
            return isset($this->_data[$key]) ? $this->_data[$key] : false;
        return $this->_data;
    }

    /**
     * 设置session变量的值
     * @param string $key
     * @param mixed $val
     * @return $this
     */
    public function set($key, $val=''){
        $this->_parse();
        if(is_array($key)){
            if(is_array($this->_data)){
                $this->_data = array_merge($this->_data, $key);
            }else{
                $this->_data = $key;
            }
        }else{
            $this->_data[$key] = $val;
        }

        return $this;
    }

    /**
     * 保存到session中。
     * 在$this->setVal 和 $this->unregister之后调用
     */
    public function save(){
        if(!empty($this->_data)){
            $_SESSION['__SESDATA'] = @serialize($this->_data);
        }else{
            $_SESSION['__SESDATA'] = '';
        }
    }

    /**
     * 删除session变量
     * @param string $key
     * @return $this
     */
    public function unregister($key){
        $this->_parse();
        if(isset($this->_data[$key])){
            unset($this->_data[$key]);
        }
        return $this;
    }

    /**
     * 注销当前session会话
     */
    public function destroy() {
        session_unset();
        session_destroy();
        setcookie($this->_sname, '', time()-3600);
    }

    public function __destruct(){

    }

}
