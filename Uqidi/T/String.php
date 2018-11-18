<?php
/**
 * @fileoverview:   String
 * @author:         Uqidi
 * @date:           2015-11-21
 * @copyright:      Uqidi
 */
class T_String{

    public static function is_empty($v) {
        $v = trim($v);
        if('' === $v)
            return false;
        return true;
    }
    /**
     * 获取随机字符串
     * @param $length
     * @param string $chars
     * @return string
     */
    public static function random($length=6, $chars=''){
        if(empty($chars))
            $chars = '0123456789abcdefghijklmnopqrstuvwxyz';
        return substr(str_shuffle($chars), 0, $length);
    }

	/**
	 * 计算字符串宽度
	 * @param string $str
	 * @param string $encoding
	 * @return int
	 */
	function str_width($str , $encoding = 'utf-8'){
		return mb_strwidth($str , $encoding);
	}

    /**
     * 字符串截取 按字符宽度进行截取
     *
     * @param string $str
     * @param int $start
     * @param int $offset
     * @param string $encoding 编码方式：'gbk' 'utf-8'...
     * @param string $end
     * @return string
     */
	function substr_by_width($str , $start , $offset , $encoding = 'utf-8',$end = ''){
		if (!function_exists("mb_strimwidth")){
			return $str ;
		}
		return mb_strimwidth($str , $start , $offset , $end , $encoding);
	}

	/**
     * 中文字符串截取 按字数截取
     * 一个中文算一个
     * @param string $str
     * @param int $start
     * @param int $offset
     * @param string $encoding 编码方式：'gbk' 'utf-8'...
     * @return string
     */
	function substr_by_charater($str , $start , $offset , $encoding = 'utf-8'){
		if (!function_exists("mb_substr")){
			return $str ;
		}
		return mb_substr($str , $start , $offset , $encoding);
	}

    /**
     * 检测是否是UTF8
     * @param $string
     * @return bool
     */
    public function isUtf8($string){
        return preg_match('%^(?:
            [\x09\x0A\x0D\x20-\x7E]              # ASCII
            | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
            |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
            | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
            |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
            |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
            | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
            |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
        )*$%xs', $string) > 0;
    }
}
