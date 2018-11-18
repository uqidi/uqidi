<?php
/**
 * @fileoverview:   Dispatcher
 * @author:         Uqidi
 * @date:           2015-11-22
 * @copyright:      Uqidi
 */

class Dispatcher{
    static protected $_instance         = null;         /* 单例 */
    public $_router                     = null;         /* 路由器 */

    static public function getInstance(){
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function __construct(){
        $this->_router = Router::getInstance();
    }

    /**
     * 得到模块名字
     * @return string
     */
    public function getModuleName(){
        return $this->_router->getModule();
    }

    /**
     * 得到控制器名字
     * @return string
     */
    public function getControllerName(){
        return $this->_router->getController();
    }

    /**
     * 获得动作名字
     * @return string
     */
    public function getActionName(){
        return $this->_router->getAction();
    }


    public function dispatch(){
        $moduleName     = $this->getModuleName();
        $actionName     = $this->getActionName();
        $controllerName = $this->getControllerName();

        $this->_dispatch($moduleName, $controllerName, $actionName);
    }

    private function _initLang(){
        $langSet = C('lang');
        if (C('lang_auto')){
            if(isset($_GET[C('lang_var')])){
                $langSet = $_GET[C('lang_var')];
                cookie('language',$langSet);
            }elseif(cookie('language')){
                $langSet = cookie('language');
            }elseif(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])){
                preg_match('/^([a-z\-]+)/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);
                $langSet = strtolower($matches[1]);
                cookie('language',$langSet);
            }
        }

        $langs = Loader::loadConfig('lang');
        isset($langs[$langSet]) ? $langSet = $langs[$langSet] : '';

        define('LANG_SET',strtolower($langSet));

        if(is_file(UQIDI_PATH.'/Lang/'.LANG_SET.'.php'))
            L(include UQIDI_PATH.'/Lang/'.LANG_SET.'.php');

        if (is_file(APP_LANG_PATH.LANG_SET.'/common.php'))
            L(include(APP_LANG_PATH.LANG_SET.'/common.php'));
        Loader::loadLang(__MODULE_NAME__, LANG_SET);
    }

    /**
     * 分发过程
     * @param $moduleName
     * @param string $controllerName
     * @param string $actionName
     * @throws K_Exception
     * @return bool
     */
    protected function _dispatch($moduleName, $controllerName, $actionName){
        $controller = ucfirst($controllerName);
        $classPath = APP_ACTION_PATH .$moduleName.'/'. $controller . ".php";
        if (!is_file($classPath)) {
            throw new K_Exception('Error 404, error page not found!', 1);
        }

        define('__MODULE_NAME__'    , $moduleName);
        define('__ACTION_NAME__'    , $actionName);
        define('__CONTROLLER_NAME__', $controllerName);

        /* 系统名和应用 */
        $agent = $this->_router->getRequest()->getAgent();
        if(isset($agent['app_name'])&& C_Device::$atypes[$agent['app_name']]){
            define('__OTYPE__', C_Device::$otypes[$agent['os_name']]);
            define('__ATYPE__', C_Device::$atypes[$agent['app_name']]);
        }else{
            define('__OTYPE__', C_Device::OTYPE_WINDOWS);
            define('__ATYPE__', C_Device::ATYPE_WEB);
        }

        $uri = $this->_router->getRequest()->getUri();
        define('__URI__', $uri);

        $this->_initLang();
        require_once($classPath);

        /* 生成控制器、动作名 */
        $controllerClassName = $controller . 'Action';

        $controller =  new $controllerClassName();

        if (!method_exists($controller, $actionName)) {
            throw new K_Exception('Page <strong>[' . $actionName . ']</strong>
            not found! The reason cause this error may be Method not exist
            in the Controller <strong>[' . $controllerClassName . ']</strong>', 1);
        }

        /* 执行动作 */
        $response = $controller->$actionName();
        return true;
    }

}
