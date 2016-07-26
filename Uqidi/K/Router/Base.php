<?php
/**
 * @fileoverview:   路由器抽象类
 * @author:         Uqidi
 * @date:           2015-11-22
 * @copyright:      Uqidi
 */

abstract class R_Base{
    protected $_moduleName = '';        /* 运行路由后的模块名 */
    protected $_controllerName = '';    /* 运行路由后的控制器名 */
    protected $_actionName = '';        /* 运行路由后的动作名 */
    protected $_params = array();       /* 所有的请求参数 */
    protected $_router_config = null;   /* 路由规则配置对象 */
    protected $_Request = '';           /* 获得Request对象 */

    /**
     * 设置模块名字
     */
    public function setModule($moduleName){
        $this->_moduleName = $moduleName;
    }

    /**
     * 获得模块名字
     * @return string
     */
    public function getModule(){
        return $this->_moduleName;
    }

    /**
     * 设置控制器名字
     */
    public function setController($controllerName){
        $this->_controllerName = $controllerName;
    }

    /**
     * 获得控制器名字
     * @return string
     */
    public function getController(){
        return $this->_controllerName;
    }

    /**
     * 设置动作名，为重新分发
     */
    public function setAction($actionName){
        $this->_actionName = $actionName;
    }

    /**
     * 获得动作名，动作是控制器的具体执行函数
     * @return string
     */
    public function getAction(){
        return $this->_actionName;
    }

    /**
     * 设置查询参数
     * @param array $params
     */
    public function setParams($params){
        $this->_params = array_merge($this->_params, $params);
    }

    /**
     * 如果没有指定param返回一个参数数组
     * 如果指定了param 且存在 param:paramname则返回paramname
     * @param string|int $param
     * @return mixed
     */
    public function getParams($param=null){
        if (!empty($param)) {
            if (empty($this->_params[$param])) {
                return '';
            } else {
                return $this->_params[$param];
            }
        } else {
            return $this->_params;
        }
    }

    /**
     * 设置请求对象
     */
    public function setRequest($requestObject){
        $this->_Request = $requestObject;
    }

    /**
     * 获得请求对象
     */
    public function getRequest(){
        if (!$this->_Request) {
            throw new K_Exception('Please run K_Router::run() first! ');
        }
        return $this->_Request;
    }

    /**
     * 获得基本参数名，默认为id
     * @return string
     */
    public function getBaseParam(){
        $param = $this->getBase('param');
        return isset($param) ? $param : 'id';
    }

    /**
     * 获得基本的路由设置
     * @param string $section 哪一段
     * @return mixed
     */
    public function getBase($section=''){
        $config = $this->getConfig();
        if (isset($config['base'])) {
            if ($section && isset($config['base'][$section])) {
                return $config['base'][$section];
            } else {
                return $config['base'];
            }
        } else {
            return null;
        }
    }

    /**
     * 获得基本域
     * @return string
     */
    public function getBaseDomain(){
        $basedomain = $this->getBase('domain');
        if (empty($basedomain)) {
            $domain = $this->_Request->getSever('HTTP_HOST');
            $domainSections = explode('.', $domain);
            $domainparts    = count($domainSections);
            $basedomain     = $domainSections[$domainparts-2] . $domainSections[$domainparts-1];
        }
        return $basedomain;
    }

    /**
     * 获得基本端口
     * @return string
     */
    public function getBasePort(){
        $baseport = $this->getBase('port');
        $port = !empty($baseport) ? $baseport : '';
        return $port;
    }

    /**
     * 获得基本控制器名
     * @return string
     */
    public function getBaseModule(){
        $module = $this->getBase(C('router_var.module'));
        if (!$module) {
            $module = 'index';
        }
        return $module;
    }

    /**
     * 获得基本控制器名
     * @return string
     */
    public function getBaseController(){
        $controller = $this->getBase(C('router_var.controller'));
        if (!$controller) {
            $controller = 'index';
        }

        return $controller;
    }

    /**
     * 获得基本动作名
     *
     * @return string
     */
    public function getBaseAction(){
        $action = $this->getBase(C('router_var.action'));
        if (!$action) {
            $action = 'index';
        }
        return $action;
    }

    /**
     * 获得服务器的PATH_INFO信息
     * @access public
     * @return void
     */
    public static function getPathInfo(){
        if(!empty($_SERVER['PATH_INFO'])){
            $pathInfo = $_SERVER['PATH_INFO'];
            if(0 === strpos($pathInfo,$_SERVER['SCRIPT_NAME']))
                $path = substr($pathInfo, strlen($_SERVER['SCRIPT_NAME']));
            else
                $path = $pathInfo;
        }elseif(!empty($_SERVER['ORIG_PATH_INFO'])) {
            $pathInfo = $_SERVER['ORIG_PATH_INFO'];
            if(0 === strpos($pathInfo, $_SERVER['SCRIPT_NAME']))
                $path = substr($pathInfo, strlen($_SERVER['SCRIPT_NAME']));
            else
                $path = $pathInfo;
        }elseif (!empty($_SERVER['REDIRECT_PATH_INFO'])){
            $path = $_SERVER['REDIRECT_PATH_INFO'];
        }elseif(!empty($_SERVER["REDIRECT_Url"])){
            $path = $_SERVER["REDIRECT_Url"];
            if(empty($_SERVER['QUERY_STRING']) || $_SERVER['QUERY_STRING'] == $_SERVER["REDIRECT_QUERY_STRING"]){
                $parsedUrl = parse_url($_SERVER["REQUEST_URI"]);
                if(!empty($parsedUrl['query'])) {
                    $_SERVER['QUERY_STRING'] = $parsedUrl['query'];
                    parse_str($parsedUrl['query'], $GET);
                    $_GET = array_merge($_GET, $GET);
                    reset($_GET);
                }else {
                    unset($_SERVER['QUERY_STRING']);
                }
                reset($_SERVER);
            }
        }
        $_SERVER['PATH_INFO'] = empty($path) ? '/' : $path;
    }

    abstract function getConfig();

}
