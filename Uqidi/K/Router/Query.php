<?php
/**
 * @fileoverview:   普通路由器
 *      http://域名?controller=default&action=index&app=default
 * @author:         Uqidi
 * @date:           2015-11-29
 * @copyright:      Uqidi
 */
class R_Query extends R_Base{
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
     * 获得路由配置信息
     * @param $router
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
     * 拼接URL
     * @param $action
     * @param string $controller
     * @param string $module
     * @param array $param
     * @return string
     */
    public function url($action, $controller='', $module='', $param=array()){
        if (empty($controller))
            $controller = $this->getController();


        if(empty($module))
            $module = $this->getModule();

        $params['m'] = $module;
        $params['c'] = $controller;
        $params['a'] = $action;
        $params = array_merge($params, $param);
        return SITE_URL . '?' . http_build_query($params);
    }

    /**
     * 设置本对象需要的请求对象
     * @param  $Request
     */
    public function execute($Request){
        $this->setRequest($Request);

        $this->setParams($this->_getParams());
        $this->_moduleName     = $this->_getModule();
        $this->_controllerName = $this->_getController();
        $this->_actionName     = $this->_getAction();
    }


}


