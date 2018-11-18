<?php
/**
 * 根据PHP各种类型变量生成唯一标识号
 * @author  Uqidi
 * @param   $mix
 * @return  string
 */
function to_guid_string($mix) {
    if (is_object($mix) && function_exists('spl_object_hash')) {
        return spl_object_hash($mix);
    } elseif (is_resource($mix)) {
        $mix = get_resource_type($mix) . strval($mix);
    } else {
        $mix = serialize($mix);
    }
    return md5($mix);
}

/**
 * 设置和获取配置
 * @param null $name
 * @param null $value
 * @return array|null
 */
function C($name=null, $value=null) {
    static $_config = array();
    if (empty($name))
        return $_config;

    if (is_array($name))
        return $_config = array_merge($_config, array_change_key_case($name));

    if (is_string($name)) {
        if (false === strpos($name, '.')) {
            $name = strtolower($name);
            if (is_null($value))
                return isset($_config[$name]) ? $_config[$name] : null;
            $_config[$name] = $value;
            return;
        }else{
            $name = explode('.', $name);
            $name[0] = strtolower($name[0]);
            if (is_null($value))
                return isset($_config[$name[0]][$name[1]]) ? $_config[$name[0]][$name[1]] : null;
            $_config[$name[0]][$name[1]] = $value;
            return;
        }


    }
    return null;
}

/**
 * dump打印
 * @param $var
 * @param bool $echo
 * @param null $label
 * @param bool $strict
 * @return mixed|null|string
 */
function dump($var, $echo=true, $label=null, $strict=true) {
    $label = ($label === null) ? '' : rtrim($label) . ' ';
    if (!$strict) {
        if (ini_get('html_errors')) {
            $output = print_r($var, true);
            $output = "<pre>" . $label . htmlspecialchars($output, ENT_QUOTES) . "</pre>";
        } else {
            $output = $label . print_r($var, true);
        }
    } else {
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        if (!extension_loaded('xdebug')) {
            $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        }
    }
    if ($echo) {
        echo($output);
        return null;
    }else
        return $output;
}

/**
 * 合并数组函数
 * @return array
 */
function array_merges(){
    $list = func_get_args();
    if(empty($list))
        return array();
    $data = array();
    foreach($list as $vo){
        if(!is_array($vo)){
            continue;
        }
        if(empty($data)){
            $data = $vo;
        }else{
            foreach($vo as $k=>$v){
                $data[$k] = $v;
            }
        }
    }

    return $data;
}