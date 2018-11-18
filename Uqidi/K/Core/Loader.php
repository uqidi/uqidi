<?php
/**
 * @fileoverview:   Loader
 * @author:         Uqidi
 * @date:           2015-11-22
 * @copyright:      Uqidi
 */

class Loader{
    /**
     * 加载语言
     * @param string $module
     * @param string $lang
     */
    static public function loadLang($module='', $lang=''){
        if(empty($lang))
            $lang = LANG_SET;
        $path = APP_LANG_PATH.$lang.'/'.strtolower($module).'.php';
        if(is_file($path))
            L(include($path));
    }

    /**
     * 加载缓存
     * @param string $key
     * @param $class
     * @param int $id
     * @param array $servers
     * @return mixed
     */
    static public function cache($key='default', $class=self::CLASS_RDS, $id=0, $servers=array()){
        return K_Cache::getInstance($key, $class, $id, $servers);
    }

    /**
     * 加载model
     * @param $name
     * @return bool
     * @throws K_Exception
     */
    static public function model($name){
        static $_models = array();
        if(isset($_models[$name]) && is_object($_models[$name]))
            return $_models[$name];

        $config = self::modelConfig($name);
        if(false === $config)
            return false;

        if(empty($config['db_type']))
            $config['db_type'] = C('db_type');
        $name = $config['model']['name'];
        $class_name = $config['model']['ClassName'].'Model';
        $class_path = APP_INCLUDE_PATH.'model/'.$config['model']['dbName'].'/'.$class_name.'.php';
        if(is_file($class_path)){
            require_once($class_path);
            if(class_exists($class_name))
                $o = new $class_name($name, $config);
            else
                throw new K_Exception("类 '$class_name' 不存在");
            $_models[$name] = $o;
            return $_models[$name];
        }

        $class_name = 'M_'.$name;
        if(class_exists($class_name)){
            $o = new $class_name($name, $config);
        }else{
            $o = new K_Model($name, $config);
        }

        if(false === $o){
            throw new K_Exception("加载 '$class_name' 失败");
        }

        $_models[$name] = $o;

        return $_models[$name];
    }

    /**
     * 加载model配置文件
     * @param $name
     * @return array|bool
     */
    static public function modelConfig($name){
        if(false === strpos($name, '@')){
            $dbName = C('db_name');
            $tableName = $name;
        }else{
            list($dbName, $tableName) = explode('@', $name, 2);
        }

        $dbName     = strtolower($dbName);
        $tableName  = strtolower($tableName);


        $databases = self::loadConfig('db');
        if(!isset($databases[$dbName])){
            $dbName = C('db_name');
        }

        $db = $databases[$dbName];

        $confs = self::loadConfig('model');



        if(!isset($confs[$dbName]) || !isset($confs[$dbName][$tableName]) || empty($confs[$dbName][$tableName])){
            $conf = array(
                'tb_n'      => 1,
                'tb_prefix' => $db['db_prefix'].$tableName,
                'db'        => $dbName,
            );
        }else{
            $conf = $confs[$dbName][$tableName];

            if(!isset($conf['tb_n'])){
                $conf['tb_n'] = 1;
            }

            if(!isset($conf['tb_prefix'])){
                $conf['tb_prefix'] = $db['db_prefix'].$tableName;
            }

            if(!isset($conf['db'])){
                $conf['db'] = $dbName;
            }
        }

        $conf['connect'] = $db;
        $conf['model']['dbName'] = $dbName;
        if(false === strpos($tableName, '_')){
            $tableName = ucfirst($tableName);
            $conf['model']['ClassName'] = $tableName;
        }else{
            $tableName = explode('_', $tableName);
            array_walk($tableName, function(&$v, $k){$v=ucfirst($v);});
            $tableName = implode('', $tableName);
            $conf['model']['ClassName'] = $tableName;
        }

        $conf['model']['name'] = ucfirst($dbName).'_'.$tableName;
        return $conf;
    }

    /**
     * 加载过滤器配置文件
     * @param $file
     * @param $pname
     * @return bool
     */
    public static function loadFilter($file, $pname=''){

        static $_filters = array();

        if(isset($_filters[$file])){
            if(empty($pname))
                return $_filters[$file];
            else
                return isset($_filters[$file][$pname]) ? $_filters[$file][$pname] : '';
        }

        $explode = explode('_', $file);
        $file_name = array_pop($explode);
        $file_name = $file_name.'.php';
        if(!empty($explode)){
            $file_name = implode('/', $explode).'/'.$file_name;
        }

        $file_path = APP_FILTER_PATH.$file_name;
        if(!is_file($file_path)){
            $_filters[$file] = array();
        }else{

            $_filters[$file] = include($file_path);
        }

        if(empty($pname))
            return $_filters[$file];
        else
            return isset($_filters[$file][$pname]) ? $_filters[$file][$pname] : '';
    }

    /**
     * 加载配置文件
     * @param $file
     * @param $key
     * @param bool $reload 是否重新加载
     * @param bool $is_cache
     * @return bool
     */
    public static function loadConfig($file, $key='', $reload=false, $is_cache=true){

        static $_configs = array();

        if($is_cache && !$reload && isset($_configs[$file])){
            if(isset($key[0]))
                return isset($_configs[$file][$key]) ? $_configs[$file][$key] : '';
            return $_configs[$file];
        }

        $explode = explode('_', $file);
        $file_name = array_pop($explode);
        $file_name = $file_name.'.php';
        if(!empty($explode)){
            $file_name = implode('/', $explode).'/'.$file_name;
        }
        $file_path = UQIDI_PATH.'Config/'.$file_name;
        $config = array();
        if(is_file($file_path)){
            $tmp = include($file_path);
            if(is_array($tmp))
                $config = array_merges($config, $tmp);
        }

        $file_path = APP_CONFIG_PATH.$file_name;
        if(is_file($file_path)){
            $tmp = include($file_path);
            if(is_array($tmp))
                $config = array_merges($config, $tmp);
        }

        if($is_cache)
            $_configs[$file] = $config;

        if(isset($key[0]))
            return isset($config[$key]) ? $config[$key] : '';

        return $config;
    }

    /**
     * 自动加载类
     * @author Uqidi
     * @param  string $className
     * @throws K_Exception
     * @return void
     */
    public static function loadClass($className){
        $load_dirs = array(
            'T'     => UQIDI_PATH.'T/',
            'K'     => UQIDI_PATH.'K/Core/',
            'R'     => UQIDI_PATH.'K/Router/',
            'V'     => UQIDI_PATH.'K/View/',
            'C'     => UQIDI_PATH.'C/',
            'P'     => UQIDI_PATH.'P/',
            'M'     => UQIDI_PATH.'M/',
            'L'     => UQIDI_PATH.'L/'
        );

        $explode = explode('_', $className);
        $file_name = array_pop($explode);
        if(isset($load_dirs[$explode[0]])){
            $dir = $load_dirs[$explode[0]];
            array_shift($explode);
            if(!empty($explode)){
                $dir .= implode('/', $explode).'/';
            }
        }else{
            $dir = APP_INCLUDE_PATH;
            $file_name = strtolower($file_name);
            if(!empty($explode)){
                $dir .= strtolower(implode('/', $explode)).'/';
            }
        }

        $path = $dir.$file_name.'.php';

        if(!is_file($path)){
            throw new K_Exception("类文件 '$path' 没有找到");
        }
        require_once($path);
        if (!class_exists($className, false) && !interface_exists($className, false)) {
            throw new K_Exception("类 '$className' 不存在");
        }
        return true;

    }

    public static function loadApp(){
        if(defined('MODE_NAME')){
            $mode_name = MODE_NAME;
        }else{
            $mode_name = 'qd';
        }
        $mode_loads = require(UQIDI_PATH.'K/Mode/'.strtolower($mode_name).'.php');
        foreach($mode_loads as $v){
            require $v;
        }
    }

    /**
     * 自动加载
     * @param $className
     * @return bool
     */
    public static function autoLoad($className){
        try{
            self::loadClass($className);
            return $className;
        }catch(Exception $e){
            return false;
        }
    }

    /**
     * 自动注册
     * @throws K_Exception
     * @return void
     */
    public static function setAutoLoad(){
        if (!function_exists('spl_autoload_register')) {
            throw new K_Exception('spl_autoload');
        }
        spl_autoload_register(array(__CLASS__, 'autoload'));
    }
}