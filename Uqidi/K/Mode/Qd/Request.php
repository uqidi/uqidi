<?php
/**
 * @fileoverview:   Request
 * @author:         Uqidi
 * @date:           2015-11-29
 * @copyright:      Uqidi
 */

class Request{
    static protected $_instance = null;     /* 单例实例对象 */
    private $_uri = null;                   /* 网址 */
    private $_agent = null;                 /* user agent */

    /**
     * 实例化本程序
     * @return object of this class
     */
    static public function getInstance(){
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    function __construct(){
        $this->filterDatas($_GET);
        $this->filterDatas($_POST);
        $this->filterDatas($_REQUEST);
        $this->filterDatas($_COOKIE);
    }

    /**
     * 获得系统定义的全局级变量
     * @author Uqidi
     * @param  string $key
     * @return string
     */
    public function __get($key){
        switch (true) {
            case isset($_GET[$key]):
                return $_GET[$key];
            case isset($_POST[$key]):
                return $_POST[$key];
            case isset($_REQUEST[$key]):
                return $_REQUEST[$key];
            default:
                return null;
        }
    }

    /**
     * 返回$key对应的值
     * @author Uqidi
     * @param string $key
     * @return string
     */
    public function get($key){
        return $this->__get($key);
    }

    /**
     * 设置URI
     * @author Uqidi
     * @param string $requestUri
     * @return string
     */
    public function setUri($requestUri = null){
        if ($requestUri === null) {
            if (isset($_SERVER['HTTP_X_REWRITE_URL'])) { // check this first so IIS will catch
                $requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
            } elseif (isset($_SERVER['REQUEST_URI'])) {
                $requestUri = $_SERVER['REQUEST_URI'];
            } elseif (isset($_SERVER['ORIG_PATH_INFO'])) { // IIS 5.0, PHP as CGI
                $requestUri = $_SERVER['ORIG_PATH_INFO'];
                if (!empty($_SERVER['QUERY_STRING'])) {
                    $requestUri .= '?' . $_SERVER['QUERY_STRING'];
                }
            } else {
                return $this;
            }
        } elseif (!is_string($requestUri)) {
            return $this;
        } else {
            $_GET = array();
            if (false !== ($pos = strpos($requestUri, '?'))) {
                $query = substr($requestUri, $pos + 1);
                parse_str($query, $vars);
                $_GET = $vars;
            }
        }

        $this->_uri = $requestUri;
        return $this;
    }

    /**
     * 获得除域名外的网址
     * $author Uqidi
     * @return string
     */
    public function getUri(){
        if (empty($this->_uri)) {
            $this->setUri();
        }
        return $this->_uri;
    }

    /**
     * 获得网址
     * @author Uqidi
     * @return string
     */
    public function getUrl(){
        $url = SITE_URL.$this->getUri();
        return $url;
    }

    /**
     * 获得请求提交数据
     * @author Uqidi
     * @param  string $key
     * @return array
     */
    public function getPost($key=''){
        if ('' == $key) {
            return $_POST;
        }elseif (isset($_POST[$key])) {
            return $_POST[$key];
        }
        return '';
    }

    /**
     * 获得请求get数据
     * @author Uqidi
     * @param  string $key
     * @return array
     */
    public function getQuery($key=''){
        if($key == ''){
            return $_GET;
        }elseif (isset($_GET[$key])) {
            return $_GET[$key];
        }else{
            return '';
        }
    }

    /**
     * 获得请求数据
     * @author Uqidi
     * @param  string $key
     * @return array
     */
    public function getParam($key=''){
        if($key == ''){
            return $_REQUEST;
        }elseif (isset($_REQUEST[$key])) {
            return $_REQUEST[$key];
        }else{
            return '';
        }

    }

    /**
     * 过滤数据
     *
     * @param  mixed $data
     * @return mixed
     */
    public function filterDatas(&$data){
        if (is_array($data)) {
            foreach ($data as $key=>$value){
                $data[$key] = $this->filterDatas($value);
            }
            return $data;
        } elseif (is_string($data)) {
            return $this->_filterData($data);
        }
    }

    /**
     * 过滤请求数据
     * @author Uqidi
     * @param mixed $value
     * @return mixed|string
     */
    protected function _filterData(&$value){
        $value = trim($value);
        return $value;
    }

    /**
     * 魔术方法
     * 是否（POST,GET,HEAD,DELETE,PUT）请求
     * @author Uqidi
     * @param string $method
     * @param array $parms
     * @return boolean
     */
    public function __call($method, $parms){
        $method = strtolower($method);
        if (in_array(strtolower($method), array('ispost','isget','ishead','isdelete','isput'))){
            return strtolower($this->getServer('REQUEST_METHOD')) == strtolower(substr($method, 2));
        }
    }

    /**
     * 是否AJAX请求, 参考zend framework
     * @author Uqidi
     * @return boolean
     */
    public function isXmlHttpRequest(){
        return ($this->getHeader('X_REQUESTED_WITH') == 'XMLHttpRequest');
    }

    /**
     * 是否flash请求, 参考zend framework
     * @author Uqidi
     * @return boolean
     */
    public function isFlashRequest(){
        $header = strtolower($this->getHeader('USER_AGENT'));
        return (strstr($header, ' flash')) ? true : false;
    }

    /**
     * 获取客户端IP, 参考zend framework
     * @author Uqidi
     * @param  boolean $checkProxy  是否检查代理
     * @return string
     */
    public function getClientIp($checkProxy = true){
        if ($checkProxy && $this->getHeader('CLIENT_IP') != null) {
            $ip = $this->getHeader('CLIENT_IP');
        } else if ($checkProxy && $this->getHeader('X_FORWARDED_FOR') != null) {
            $ip = $this->getHeader('X_FORWARDED_FOR');
        } else {
            $ip = $this->getServer('REMOTE_ADDR');
        }
        return $ip;
    }

    /**
     * 获取头信息，参考zend framework
     * @author Uqidi
     * @param  string $header HTTP头信息名称
     * @return string|false  HTTP头信息, 或者头信息不存在返回false
     */
    public function getHeader($header){
        $temp = 'HTTP_' . strtoupper(str_replace('-', '_', $header));
        if (!empty($_SERVER[$temp])) {
            return $_SERVER[$temp];
        }
        return false;
    }

    /**
     * 获取$_SERVER中的信息
     * 如果不指定名称，则返回$_SERVER
     * @author Uqidi
     * @param string $key
     * @param mixed $default 默认值
     * @return mixed 不存在返回null
     */
    public function getServer($key = null, $default = null){
        if (null === $key) {
            return $_SERVER;
        }
        $key = strtoupper($key);
        return isset($_SERVER[$key]) ? $_SERVER[$key] : $default;
    }

    /**
     * 获取请示协议
     * @author Uqidi
     * @return string
     */
    public function getScheme(){
        return ($this->getServer('HTTPS') == 'on') ? 'https' : 'http';
    }

    /**
     * 获取上传文件参数
     * @param string $key
     * @return string
     */
    public function getFiles($key=''){
        if($key == ''){
            return $_FILES;
        }elseif (isset($_FILES[$key])) {
            return $_FILES[$key];
        }else{
            return '';
        }
    }

    /**
     * get agent
     * Returns true if any type of mobile device app
     * @return array
     */
    public function getAgent(){
        $agent = $this->getHeader('USER_AGENT');
        $this->_agent = T_Header::user_agent($agent);
        return $this->_agent;
    }

}