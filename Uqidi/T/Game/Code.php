<?php
/**
 * @fileoverview:   玩法号码
 * @author:         Uqidi
 * @date:           2015-11-15
 * @copyright:      Uqidi.com
 */
class T_Game_Code{

    public static function getNoteNo($functionName, $codes, &$error=array()) {
        $codes = trim($codes, '|');
        $noteNo = 0;
        if(empty($codes)){
            $error = T_Output::return_error('PARAM', '您没有购买任何号码');
            return false;
        }
        if(strpos($codes, '|')) {
            $code_array = explode('|', $codes);
            $code_array = array_filter($code_array, 'T_String::is_empty');

            if(empty($code_array)){
                $error = T_Output::return_error('PARAM', '您没有购买任何号码');
                return false;
            }

            foreach($code_array as $code){
            	$code = trim($code);
                $result = self::$functionName($code, $error);
                if(false === $result) {
                    return false;
                }
                $noteNo += $result;
            }
            return $noteNo;
        }else{
            $result = self::$functionName($codes);
            return $result;
        }
    }

    /**
     * 三星组三
     * @param $codes
     * @param array $error
     * @return mixed
     */
    public static function group_three($codes, &$error=array()){
        if(!is_numeric($codes)){
            $error = T_Output::return_error('PARAM', '您输入的有违法字符');
            return false;
        }

        $numcode = strlen($codes);
        $num = count(array_unique(str_split($codes)));
        if($numcode !== $num){
            $error = T_Output::return_error('PARAM', '您购买号码有重复数字');
            return false;
        }

        if($numcode < 2){
            $error = T_Output::return_error('PARAM', '您购买的号码不正确');
            return false;
        }

        return $numcode*($numcode-1);
    }

    /**
     * 三星组六
     * @param $codes
     * @param array $error
     * @return mixed
     */
    public static function group_six($codes, &$error=array()){
        if(!is_numeric($codes)){
            $error = T_Output::return_error('PARAM', '您输入的有违法字符');
            return false;
        }

        $numcode = strlen($codes);
        $num = count(array_unique(str_split($codes)));
        if($numcode !== $num){
            $error = T_Output::return_error('PARAM', '您购买号码有重复数字');
            return false;
        }

        if($numcode<3){
            $error = T_Output::return_error('PARAM', '您购买的号码不正确');
            return false;
        }

        return ($numcode*($numcode-1)*($numcode-2))/6;
    }

    /**
     * 三星混合组选
     * @param $codes
     * @param array $error
     * @return bool|int
     */
    public static function group_mix($codes, &$error=array()){
        if(!is_numeric($codes)){
            $error = T_Output::return_error('PARAM', '您输入的有违法字符');
            return false;
        }

        $numcode = strlen($codes);

        if($numcode !== 3){
            $error = T_Output::return_error('PARAM', '您购买的号码不正确');
            return false;
        }

        $num = count(array_unique(str_split($codes)));
        if($num === 1){
            $error = T_Output::return_error('PARAM', '您购买号码有重复数字');
            return false;
        }
        return 1;
    }


    /**
     * 二星组选[复式]
     * @param $codes
     * @param array $error
     * @return mixed
     */
    public static function group_two_complex($codes, &$error=array()){
        if(!is_numeric($codes)){
            $error = T_Output::return_error('PARAM', '您输入的有违法字符');
            return false;
        }

        $numcode = strlen($codes);
        $num = count(array_unique(str_split($codes)));
        if($numcode !== $num){
            $error = T_Output::return_error('PARAM', '您购买号码有重复数字');
            return false;
        }

        if($numcode < 2){
            $error = T_Output::return_error('PARAM', '您购买的号码不正确');
            return false;
        }
        return ($numcode*($numcode-1))/2;
    }

    /**
     * 二星组选[单式]
     * @param $codes
     * @param array $error
     * @return mixed
     */
    public static function group_two_single($codes, &$error=array()){
        if(!is_numeric($codes)){
            $error = T_Output::return_error('PARAM', '您输入的有违法字符');
            return false;
        }

        $numcode = strlen($codes);

        if($numcode !== 2){
            $error = T_Output::return_error('PARAM', '您购买的号码不正确');
            return false;
        }

        $num = count(array_unique(str_split($codes)));
        if($num !== 2){
            $error = T_Output::return_error('PARAM', '您购买号码有重复数字');
            return false;
        }
        return 1;
    }



    /**
     * 不定位
     * @param $codes
     * @param array $error
     * @return mixed
     */
    public static function unlocated($codes, &$error=array()){
        if(!is_numeric($codes)){
            $error = T_Output::return_error('PARAM', '您输入的有违法字符');
            return false;
        }

        $numcode = strlen($codes);
        $num = count(array_unique(str_split($codes)));
        if($numcode !== $num){
            $error = T_Output::return_error('PARAM', '您购买号码有重复数字');
            return false;
        }
        return $numcode;
    }

    /**
     * 定位胆
     * @param $codes
     * @param array $error
     * @return bool|int
     */
    public static function orientation($codes, &$error=array()) {
        $code_array = explode(",",$codes);
        $code_len = count($code_array);

        if(!in_array($code_len, array(3,5))){
            $error = T_Output::return_error('PARAM', '您购买号码错误');
            return false;
        }

        $code_array = array_filter($code_array, 'T_String::is_empty');

        if(empty($code_array)){
            $error = T_Output::return_error('PARAM', '您没有购买任何号码');
            return false;
        }

        $numcode = 0;
        foreach($code_array as $v){
            if(!is_numeric($v)){
                $error = T_Output::return_error('PARAM', '您输入的有违法字符');
                return false;
            }
            $num = count(array_unique(str_split($v)));
            if(strlen($v) !== $num){
                $error = T_Output::return_error('PARAM', '您购买号码有重复数字');
                return false;
            }
            $numcode += $num;
        }

        return $numcode;
    }

    /**
     * 二星直选[复式]
     * @param $codes
     * @param array $error
     * @return bool
     */
    public static function direct_two_complex($codes, &$error=array()){
       return self::direct_complex($codes, 2, $error);
    }

    /**
     * 二星直选[单式]
     * @param $codes
     * @param array $error
     * @return bool
     */
    public static function direct_two_single($codes, &$error=array()){
        return self::direct_single($codes, 2, $error);
    }


    /**
     *  三星直选[复式]
     * @param $codes
     * @param array $error
     * @return mixed
     */
    public static function direct_three_complex($codes, &$error=array()){
       return self::direct_complex($codes, 3, $error);
    }

    /**
     * 三星直选[单式]
     * @param $codes
     * @param array $error
     * @return mixed
     */
    public static function direct_three_single($codes, &$error=array()){
        return self::direct_single($codes, 3, $error);
    }

    /**
     * 四星直选[复式]
     * @param $codes
     * @param array $error
     * @return bool
     */
    public static function direct_four_complex($codes, &$error=array()){
        return self::direct_complex($codes, 4, $error);
    }

    /**
     * 四星直选[单式]
     * @param $codes
     * @param array $error
     * @return bool
     */
    public static function direct_four_single($codes, &$error=array()){
        return self::direct_single($codes, 4, $error);
    }

    /**
     * 四星直选[复式]
     * @param $codes
     * @param array $error
     * @return bool
     */
    public static function direct_five_complex($codes, &$error=array()){
        return self::direct_complex($codes, 5, $error);
    }

    /**
     * 四星直选[单式]
     * @param $codes
     * @param array $error
     * @return bool
     */
    public static function direct_five_single($codes, &$error=array()){
        return self::direct_single($codes, 5, $error);
    }

    /**
     *  直选[复式]
     * @param $codes
     * @param int $len
     * @param array $error
     * @return mixed
     */
    public static function direct_complex($codes, $len=3, &$error=array()){
        $code_array = array_filter(explode(",",$codes), 'T_String::is_empty');

        if(count($code_array) !== $len){
            $error = T_Output::return_error('PARAM', '您购买号码错误');
            return false;
        }

        $numcode = 1;
        foreach($code_array as $v){
            if(!is_numeric($v)){
                $error = T_Output::return_error('PARAM', '您输入的有违法字符');
                return false;
            }
            $num = count(array_unique(str_split($v)));
            if(strlen($v) !== $num){
                $error = T_Output::return_error('PARAM', '您购买号码有重复数字');
                return false;
            }
            $numcode = $numcode * $num;
        }

        return $numcode;
    }

    /**
     * 直选[单式]
     * @param $codes
     * @param int $len
     * @param array $error
     * @return mixed
     */
    public static function direct_single($codes, $len=3, &$error=array()){
        if(!is_numeric($codes)){
            $error = T_Output::return_error('PARAM', '您输入的有违法字符');
            return false;
        }

        if(strlen($codes) !== $len){
            return amfReturn(CE_CODE_PARAM, '您购买的号码不正确！');
        }
        return 1;
    }

}