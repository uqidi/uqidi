<?php
/**
 * @fileoverview:   Http
 * @author:         Uqidi
 * @date:           2015-11-21
 * @copyright:      uqidi.com
 */
class T_input{
	static  function input($key , $method = '' , $default = null){
		$method = strtoupper($method);
		if('G' == $method || 'GET' == $method){
			$value = isset($_GET[$key]) ? $_GET[$key] : $default;
		}elseif('P' == $method || 'POST' == $method){
			$value = isset($_POST[$key]) ? $_POST[$key] : $default;
		}else{
			$value = isset($_GET[$key]) ? $_GET[$key] : (isset($_POST[$key]) ? $_POST[$key] : $default);
		}
	
		return is_array($value) ? $value : trim($value);
	}
    /**
     * 参数检查
     * @access static public
     * @param $string
     * @param $option
     * @return bool|int|mixed
     */
	static function param_base($string, $option){
		if(!$option)
			return true;
		extract($option);
		if(empty($string)){
			if($is_must)
				return false;
			else
				return true;
		}
		
		switch ($type){
			case SYS_DATATYPE_DIGIT:
				if(!self::length_limit($string, $len_limit))
					return false;
				else
					return ctype_digit($string);
				break;
			case SYS_DATATYPE_ALPHA:
				if(!self::length_limit($string, $len_limit))
					return false;
				else
					return ctype_alpha($string);
				break;
			case SYS_DATATYPE_ALNUM:
				if(!self::length_limit($string, $len_limit))
					return false;
				else
					return  ctype_alnum($string) || filter_var($string, FILTER_VALIDATE_INT) || filter_var($string, FILTER_VALIDATE_FLOAT);
				break;
			case SYS_DATATYPE_EMAIL:
				return filter_var($string, FILTER_VALIDATE_EMAIL);
				break;
			case SYS_DATATYPE_URL:
				return filter_var($string, FILTER_VALIDATE_URL);
				break;
			case SYS_DATATYPE_INARRAY:
				if(!is_array($in))
					return false;
				return in_array($string, $in);
				break;
			case SYS_DATATYPE_PREG:
				return preg_match($reg, $string);
                break;
            default:
                return true;
		}
        return true;
	}

	static function length_limit($string, $len_limit){
		if(!is_array($len_limit))
			return true;
		$str_len = strlen($string);
		if(isset($len_limit['eq']) && ($str_len !== $len_limit['eq']))
			return false;
		if(isset($len_limit['gt']) && ($str_len < $len_limit['gt']))
			return false;
		if(isset($len_limit['lt']) && ($str_len > $len_limit['lt']))
			return false;
		return true;
	}
}
?>