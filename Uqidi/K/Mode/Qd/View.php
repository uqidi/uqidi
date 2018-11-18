<?php
/**
 * @fileoverview:   渲染代理
 *      根据渲染配置的环境变量选择演染插件，泻染插件必须要有自己的接口以便统一操作
 * @author:         Uqidi
 * @date:           2015-11-29
 * @copyright:      Uqidi
 */

class View{
    static private $_instance = null;       /* 单例 */
    private $_engine = null;                /* 渲染引擎 */

    static public function getInstance(){
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function __construct(){
        $engine = C('viewer_engine');
        $this->_engine = new $engine();
    }

    /**
     * 执行渲染过程
     * @param string $template
     */
    public function display($template=''){
        $this->_engine->display($template);
    }

    /**
     * 执行渲染过程
     * @param string $template
     * @return string
     */
    public function render($template=''){
        $content = $this->_engine->render($template);
        return $content;
    }

    /**
     * 获得模板引擎
     * 根据插件环境变量获得调用哪个具体的渲染实例类
     *
     * @return string
     */
    public function getEngine(){
        return $this->_engine;
    }

    /**
     *
     * @param $methodName
     * @param $arguments
     * @return mixed
     */
    public function __call($methodName, $arguments){
        if ($this->_engine) {
            return  call_user_func_array(array($this->_engine, $methodName), $arguments);
        }
    }

}