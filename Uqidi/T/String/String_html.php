<?php
/**
 * @fileoverview:   HTML
 * @author:         Uqidi
 * @date:           2015-11-15
 * @copyright:      Uqidi.com
 */
class T_String_Html{
	
    /**
     * HTML字符串截取
     * 注意事项：
     *   如果正好截到了flash代码中间，自动将后面的补全
     *   如果正好截到了img代码中间，自动补全
     * 修改：
     *   无论内容是否需要截取，都进行HTML补全 2008-04-25
     * @author Uqidi
     * @param string $html
     * @param int $len
     * @return string
     */
	public static function substr_html($html, $len){
		tidy_set_encoding("utf8");

		/* 如果超过要截取的长度才进行 */
		if (strlen($html) > $len)
        {
    		$html_1 = substr($html , 0 , $len);     /* 被截出 */
    		$html_2 = substr($html , $len);         /* 被抛弃 */
    
    		/* 保留被截断的FLASH代码 */
    		$pos_obj = mb_strrpos($html_1 , "<object"); /* strrpos在php4中能找一个字符，所以用MB的，而且不指定编码 */
            $pos_emb = mb_strrpos($html_1 , "<embed");  /* strrpos在php4中能找一个字符，所以用MB的，而且不指定编码 */

    		/* 只可能被截断一个 */
            if($pos_obj !== false){
    		    $pos = $pos_obj;
    			$ppos = strpos($html_1 , "</object>");
    			if($ppos === false){
    				$pos = strpos($html_2 , "</object>");
    				$html_1 .= substr($html_2 , 0 , $pos)."</object>";
    				$html_2 = substr($html_2 , $pos+9);
    				$len = $pos+9;
    			}
    		}elseif($pos_emb !== false){ /* 保留被截断的视频代码 */

				/* 保留被截断的视频代码 */
				if(preg_match ("/<embed[^>]+$/si",$html_1)){
					$pos = strpos($html_2 , ">");
					$html_1 .= substr($html_2 , 0 , $pos).">";
					$html_2 = substr($html_2 , $pos+1);
					$len = $pos+1;
				}
				
				$html = $html_1;
    		}
    
    		/* 保留被截断的IMG代码 */
    		if(preg_match ("/<img[^>]+$/si",$html_1)){
    			$pos = strpos($html_2 , ">");
    			$html_1 .= substr($html_2 , 0 , $pos).">";
    			$html_2 = substr($html_2 , $pos+1);
    			$len = $pos+1;
    		}
    		
    		$html = $html_1;
        }
		
        /* 以下进行HTML补全和基本的替换 */
		$html = preg_replace("/[\n\r\t]/"," ",$html);
		$html = str_replace("\"","'",$html);

		
		$unuse_tag = array('</IMG>'=>'','</HR>'=>'','</BGSOUND>'=>'','</FRAME>'=>'','</COL>'=>'','</BASE>'=>'','</BR>'=>'','</AREA>'=>'','</LINK>'=>'','</WBR>'=>'','</META>'=>'','</PARAM>'=>'');
        $html = strtr($html , $unuse_tag);
        $unuse_tag = array('</img>'=>'','</hr>'=>'','</bgsound>'=>'','</frame>'=>'','</col>'=>'','</base>'=>'','</br>'=>'','</area>'=>'','</link>'=>'','</wbr>'=>'','</meta>'=>'','</param>'=>'');
        $html = strtr($html , $unuse_tag);
        

		/* html代码补全 */
		$conf = array(
		    'output-xhtml'      => true,  /* 本选项会按XHTML标准进行输出，已知的，非标签的 < >会被替换成&gt;形式 */
		    'drop-empty-paras'  => false,
		    'join-classes'      => true,
		    'show-body-only'    => true,
		);
		$html = self::gb2utf($html);
		$tidy = tidy_repair_string($html,$conf);
		$tidy = String::utf2gb($tidy);
		return $tidy;
	}
	
	/**
	 * 格式化HTML
	 * @author Uqidi
	 * @param string $html
	 * @return string
	 */
	function tidy_repair_string($html){
	    tidy_set_encoding("UTF8");
		/* html代码补全 */
		$conf = array(
		    'output-xhtml'      => true,
		    'drop-empty-paras'  => false,
		    'join-classes'      => true,
		    'show-body-only'    => true,
		);

		$html = self::gb2utf($html);
		$tidy = tidy_repair_string($html,$conf);
		$tidy = self::utf2gb($tidy);
		return $tidy;
	}
}