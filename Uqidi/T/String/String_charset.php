<?php
/**
 * @fileoverview:   字符集
 * @author:         Uqidi
 * @date:           2015-11-15
 * @copyright:      Uqidi.com
 */
class T_String_Charset extends T_String {
    /**
     * 检查是否是UTF8
     * @author Uqidi
     * @param $string
     * @return bool
     */
    function is_utf8($string){
		return preg_match('%^(?:
              [\x09\x0A\x0D\x20-\x7E]            # ASCII
            | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
            | \xE0[\xA0-\xBF][\x80-\xBF]         # excluding overlongs
            | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
            | \xED[\x80-\x9F][\x80-\xBF]         # excluding surrogates
            | \xF0[\x90-\xBF][\x80-\xBF]{2}      # planes 1-3
            | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
            | \xF4[\x80-\x8F][\x80-\xBF]{2}      # plane 16
            )*$%xs', $string) > 0;
	}
	
	/**
	 * 将实体字转为utf-8
     * @author  Uqidi
	 * @param string $str
	 * @return sring
	 */
	function html2utf($str){
		return preg_replace(
            '/(&#?[0-9a-z]{2,7};)/e' ,
            "mb_convert_encoding('\\1' , 'utf-8' , 'HTML-ENTITIES')" ,
            $str);
	}
	
	/**
	 * 检查字符中是否含有中文
	 * @author Uqidi
	 * @param string $string
	 * @return bool
	 */
	function check_cn($string){
		$string = strip_tags($string);
		$string = preg_replace("/[\xa1-\xa3][\xA0-\xff]/",'', $string);	/* 先将全角的标点和英文替换成空 */

		$arr["&"] = "&amp;";
		$arr['"'] = '&quot;';
		$arr["'"]=  '&#039;';
		$arr["<"] = "&lt;" ;
		$arr[">"] = "&gt;";
		$arr[""] = "&nbsp;";

		$key = array_keys($arr);
		$value = array_values($arr);
		$string = str_replace($value,$key,$string);   /* 先把实体替换成字符 */

		$lenth = strlen(trim($string));

		if($lenth < 100){		/* 如果小于100个字符，直接先放，否则后放 */
			return true;
		}

		$string = preg_replace("/[0-9!@#$%^&*()-+=|:;\"'\?<>,\.\/\\~\[\]]/",'', $string);	/* 把半角符号和数字替换为空 */

		if (trim($string) ==""){
			return true;
		}else{
			if(preg_match('/[\x81-\xA0\xB0-\xFE][\x40-\xFE]/',$string, $match)){		/* 匹配替换后的字符串是否含有中文 */

				return true;
			}
			else{
			    return false;
            }
		}
	}
}
