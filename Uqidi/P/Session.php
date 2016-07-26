<?php
/**
 * @fileoverview:   Session
 * @author:         Uqidi
 * @date:           2015-11-29
 * @copyright:      Uqidi.com
 */
class P_Session{
    private static $_inst = null;

    static public function getInstance() {
        if(self::$_inst){
            return self::$_inst;
        }
        $configs = Loader::loadConfig('session');
        $class_name = empty($configs['class']) ? 'File' :$configs['class'];
        $class_name = 'P_Session_'.$class_name;
        $save_handler = empty($configs['save_handler']) ? 'files' : $configs['save_handler'];
        self::$_inst = new $class_name();
        self::$_inst->init($save_handler, $configs['name'], $configs['save_path']);
        self::$_inst->start();
        return self::$_inst;
    }
}