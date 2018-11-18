<?php
/**
 * @fileoverview:   Router
 * @author:         Uqidi
 * @date:           2015-11-29
 * @copyright:      Uqidi
 */

class Router{
    static private $_instance  = null;  /* 单例 */
    static private $_engine  = null;    /* 路由引擎 */

    static public function getInstance(){
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    protected function __construct(){
        $engine = C('router_engine');
        self::$_engine = new $engine();
        $request = Request::getInstance();
        self::$_engine->execute($request);
    }

    public function __call($methodName, $arguments){
        if (self::$_engine) {
            $result = call_user_func_array(array(self::$_engine, $methodName), $arguments);
            return $result;
        }
    }

}