<?php
/**
 * @fileoverview:   智能路由器
 * 可自动识别模块和操作/module/action/id/1/
 * @author:         Uqidi
 * @date:           2015-11-29
 * @copyright:      Uqidi
 */

class R_Stand extends R_Base{

    /**
     * 解析参数
     * @return array
     */
    private function _parse_params(){
        self::getPathInfo();
        $paths = explode('/', trim($_SERVER['PATH_INFO'], '/'));
        $params['m']  =  array_shift($paths);
        $params['c']  =  array_shift($paths);
        $params['a']  =  array_shift($paths);;
        /* 解析剩余的URL参数 */
        $var  =  array();

        $res = preg_replace('@(\w+)/([^/]+)@e', '$var[\'\\1\']="\\2";', implode('/',$paths));
        if(is_array($var)){
            $params = array_merge($params, $var);
        }
        if(is_array($_GET)){
            $params = array_merge($params, $_GET);
        }
        $_GET = $params;
        $_REQUEST = array_merge($params, $_REQUEST);
        return $params;
    }

    /**
     * 获得模块名字
     * @return string
     */
    protected function _getModule(){
        if (is_array($this->_params) && !empty($this->_params[C('router_var.module')])) {
            return $this->_params[C('router_var.module')];
        } else {
            return $this->getBaseModule();
        }
    }
    /**
     * 获得控制器名字
     * @return string
     */
    protected function _getController(){
        if (is_array($this->_params) && !empty($this->_params[C('router_var.controller')])) {
            return $this->_params[C('router_var.controller')];
        } else {
            return $this->getBaseController();
        }
    }

    /**
     * 获得动作名，动作是控制器的具体执行函数
     * @return string
     */
    protected function _getAction(){
        if (is_array($this->_params) && !empty($this->_params[C('router_var.action')])) {
            return $this->_params[C('router_var.action')];
        } else {
            return $this->getBaseAction();
        }
    }

    /**
     * 如果没有指定param返回一个参数数组
     * 如果指定了param 且存在 param:paramname则返回paramname
     * @param string|int $param
     * @return mixed
     */
    protected function _getParams($param=null){
        if(!empty($param)) {
            return $this->getRequest()->getQuery($param);
        } else {
            return $this->getRequest()->getQuery();
        }
    }

    /**
     * 生成URL
     * @param string $action 动作名
     * @param string $controller 控制器名，可选，默认与当前控制器同名
     * @param string $module
     * @param array $params 传递的参数，参数将以GET方法传递
     * @return string
     */
    public function url($action, $controller='', $module='', $params=array()){
        if ('' == $controller) {
            $controller = $this->getController();
        }
        $url = SITE_URL.'/'.$module.'/'.$controller.'/'.$action.'/';
        if(!empty($params) && is_array($params)){
            foreach($params as $k=>$v){
                $url .= $k.'/'.$v;
            }
        }
        return $url;
    }

    /**
     * 获得路由配置信息
     * @param string $router
     * @return array
     */
    public function getConfig($router=''){
        if(!$this->_router_config){
            $this->_router_config = Loader::loadConfig('router');
        }
        if(empty($router))
            return $this->_router_config;

        return isset($this->_router_config['router'][$router]) ? $this->_router_config['router'][$router] : '';
    }

    /**
     * 设置本对象需要的请求对象
     * @param $Request
     */
    public function execute($Request){
        $this->_parse_params();
        $this->setRequest($Request);

        $this->setParams($this->_getParams());
        $this->_moduleName      = $this->_getModule();
        $this->_controllerName = $this->_getController();
        $this->_actionName     = $this->_getAction();
    }

}
