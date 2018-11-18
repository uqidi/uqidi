<?php
/**
 * @fileoverview:   数组
 * @author:         Uqidi
 * @date:           2015-11-15
 * @copyright:      Uqidi.com
 */
class T_Array{

	const FORMAT_VALUE_ONLY     = 0;    /* 只保留某字段 */
	const FORMAT_KEY2VALUE      = 1;    /* 二维数组格式化为一维，原key -> 某字段 */
	const FORMAT_VALUE2KEY      = 2;    /* 某字段->原KEY */
	const FORMAT_ID2VALUE       = 3;    /* ID主键->某字段 */
	const FORMAT_VALUE2ID       = 4;	/* 某字段 -> id主键 */
	const FORMAT_VALUE2VALUE2   = 5;	/* 某字段->另一字段 */
	const FORMAT_FIELD2ROW      = 6;	/* 某字段->数组*/

    /**
     * 格式换数组
     * @param $array
     * @param $field
     * @param int $format
     * @param string $field2
     * @return array
     */
    static public function format_2d_array($array , $field , $format = self::FORMAT_VALUE_ONLY , $field2 = ''){
		if(is_array($array) && count($array)>0){
			foreach ($array as $key => $tmp){
				switch ($format){
					case self::FORMAT_ID2VALUE :
						$arrRs[$tmp['id']] = $tmp[$field];
                        break;
					case self::FORMAT_KEY2VALUE :
						$arrRs[$key] = $tmp[$field];
                        break;
					case self::FORMAT_VALUE2KEY :
						$arrRs[$tmp[$field]] = $key;
                        break;
					case self::FORMAT_VALUE2ID :
						$arrRs[$tmp[$field]] = $tmp['id'];
                        break;
					case self::FORMAT_VALUE2VALUE2 :
						$arrRs[$tmp[$field]] = $tmp[$field2];
                        break;
					case self::FORMAT_FIELD2ROW :
						$arrRs[$tmp[$field]] = $tmp;
                        break;
					case self::FORMAT_VALUE_ONLY :
					default:
						$arrRs[] = $tmp[$field];
                        break;
				}

			}
		}
		return $arrRs;
    }

    /**
     * 合并数组
     * @author Uqidi
     * @return array
     */
    public static function merge(){
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

}