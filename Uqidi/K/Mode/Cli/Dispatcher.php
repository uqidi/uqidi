<?php
/**
 * @fileoverview:   Dispatcher
 * @author:         Uqidi
 * @date:           2015-11-22
 * @copyright:      Uqidi
 */

class Dispatcher{
    static protected $_instance         = null;         /* 单例 */

    static public function getInstance(){
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function dispatch(){
        $module_name        = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : 'index';
        $controller_name    = isset($_SERVER['argv'][2]) ? $_SERVER['argv'][2] : 'Index';
        define('__MODULE_NAME__'    , $module_name);
        define('__CONTROLLER_NAME__', $controller_name);

        !isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] = $module_name.'/'.$controller_name : '';

        $controller = ucfirst($controller_name);
        $classPath = APP_ACTION_PATH .$module_name.'/'. $controller . ".php";
        if (!is_file($classPath)) {
            throw new K_Exception('Error 404, error "'.$classPath.'" page not found!', 1);
        }

        require_once($classPath);

        /* 生成控制器、动作名 */
        $controllerClassName = $controller . 'Action';

        $controller =  new $controllerClassName();

        if (!method_exists($controller, 'run')) {
            throw new K_Exception('Page <strong>[' . run . ']</strong>
            not found! The reason cause this error may be Method not exist
            in the Controller <strong>[' . $controllerClassName . ']</strong>', 1);
        }


        $rs = $controller->init();

        /* 执行动作 */
        if($rs){
            $response = $controller->run();
        }

        return true;
    }

}
