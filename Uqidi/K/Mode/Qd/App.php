<?php
/**
 * @fileoverview:   Action
 * @author:         Uqidi
 * @date:           2015-12-04
 * @copyright:      Uqidi
 */

class APP{

    static private function initConfig(){
        C(Loader::loadConfig('config', '', false, false));
    }


    static private function initAutoLoad(){
        Loader::setAutoLoad();
    }

    /**
     * 加载函数文件
     * 可扩展，只需要在配置文件中添加
     * functions = array('文件名'=>1系统 0是应用的);
     */
    static private function initFunction(){
        $list = C('functions');
        if(empty($list))
            return true;
        foreach($list as $k=>$v){
            include_once(APP_INCLUDE_PATH.'/function/'.$k.'.php');
        }
        return true;
    }

    static private function initDispatcher(){
        $dispatcher = Dispatcher::getInstance();
        $dispatcher->dispatch();
    }

    public static function run(){
        Timer::start('total');
        self::initConfig();
        self::initAutoLoad();
        self::initFunction();
        self::initDispatcher();
        Timer::end('total');
        T_Logger::setLoginfo(C('log_attribute'));
        T_Logger::requestLog();
    }

}