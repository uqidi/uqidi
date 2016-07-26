<?php

/**
 * Class T_Game_Open
 * 开奖号码
 */
class T_Game_Open{
    /**
     * 开奖匹配
     * @param $game
     * @param $opencode
     * @param $codes
     * @return bool
     */
    public static function winMatch($game,$opencode,$codes) {
        if(empty($game) || empty($opencode) || is_null($codes)) {
            return false;
        }
        return self::$game($opencode,$codes);
    }

    /**
     * 直选--复式[前三][后三][前二][后二]
     * @param $opencode 开奖号码
     * @param $codes 购买号码
     * @return bool|int
     */
    private static function direct_complex($opencode,$codes) {
        $opencode = str_split($opencode);
        $code_array = explode("|",trim($codes,'|'));
        $n = 0;
        $len = count($opencode);
        foreach($code_array as $key => $value){
            $i = 0;
            foreach(explode(",",$value) as $k => $val){
                if(false !== strpos($val, $opencode[$k]))
                    $i++;
            }
            if($i == $len)$n++;
        }
        return empty($n) ? false : $n;
    }

    /*
    * 直选--单式[前三][后三][前二][后二]
    * $opencode 开奖号码
    * $codes 购买号码
    */
    private static function direct_single($opencode, $codes) {
        $code_array = explode("|",trim($codes,'|'));
        $n = 0;
        foreach($code_array as $key => $value){
            if($value == $opencode) $n++;
        }
        return empty($n) ? false : $n;
    }


    /*
    * 三星组三[前三][后三]
    * $opencode 开奖号码
    * $codes 购买号码
    */
    private static function group_selection_g3($opencode, $codes) {
        $opencode = str_split($opencode);
        $opencode = array_unique($opencode);

        if(count($opencode) != 2){
            return false;
        }

        $codes_array = explode("|",trim($codes,'|'));
        $n = 0;
        foreach($codes_array as $value){
            $i = 0;
            foreach($opencode as $val){
                if(false !== strpos($value,$val)) $i++;
            }
            if($i == 2) $n++;
        }
        return empty($n) ? false : $n;
    }

    /*
    * 三星组六[前三][后三]
    * $opencode 开奖号码
    * $codes 购买号码
    */
    private static function group_selection_g6($opencode, $codes) {
        $opencode = str_split($opencode);
        $opencode = array_unique($opencode);

        if(count($opencode) != 3){
            return false;
        }

        $code_array = explode("|",$codes);
        $n = 0;
        foreach($code_array as $value){
            $i = 0;
            foreach($opencode as $val){
                if(false !== strpos($value,$val)) $i++;
            }
            if($i == 3) $n++;
        }
        return empty($n) ? false : $n;
    }


    /*
    * 混合组选[前三][后三]
    * $opencode 开奖号码
    * $codes 购买号码
    */
    private static function group_selection_gh($opencode, $codes) {
        $opencode = str_split($opencode);
        $ocode = array_unique($opencode);
        sort($opencode);
        $ocode_str = implode('', $opencode);
        $ocount = count($ocode);
        if( $ocount != 2 && $ocount != 3){
            return false;
        }

        $code_array = explode("|", trim($codes, '|'));
        $n = 0;
        foreach($code_array as $key => $code){
            $code = str_split($code);
            $cell = array_unique($code);
            if($ocount != count($cell)) continue;
            sort($code);
            $cell_str = implode('', $code);
            if($ocode_str == $cell_str){
                $n++;
            }
        }
        return empty($n) ? false : $n;
    }



    /*
    * 不定位[前三][后三]
    * $opencode 开奖号码
    * $codes 购买号码
    */
    private static function unlocated_block($opencode, $codes) {
        $code_array = explode("|",trim($codes, '|'));
        $opencode = str_split($opencode);
        $opencode = array_unique($opencode);
        $n = 0;
        foreach($code_array as $value){
             foreach($opencode as $val) {
                 if(false !== strpos($value, $val)) $n++;
             }
        }
        return empty($n) ? false : $n;
    }


    //定位胆
    private static function orientation($opencode, $codes) {
        $opencode = str_split($opencode);
        $code_array = explode("|", trim($codes, "|"));
        $n = 0;
        foreach($code_array as $key => $value){
            foreach(explode(",",$value) as $k => $val){
                if(false !== strpos($val, $opencode[$k]))
                    $n++;
            }
        }
        return empty($n) ? false : $n;
    }

    /*
    * 二星组选[后二]
    * $opencode 开奖号码
    * $codes 购买号码
    */
    private static function group2_complex($opencode,$codes) {
        $opencode = str_split($opencode);
        $opencode = array_unique($opencode);
        if(count($opencode) != 2){
            return false;
        }

        $code_array = explode("|",$codes);
        $n = 0;
        foreach($code_array as $value){
            $i = 0;
            foreach($opencode as $val){
                if(false !== strpos($value,$val)) $i++;
            }
            if($i == 2) $n++;
        }
        return empty($n) ? false : $n;
    }

    public static function winHunhe($bonus, $opencode){
        $bonus = explode(',', $bonus);
        $opencode = array_unique(str_split($opencode));
        if(2 === count($opencode)){
            return $bonus[0];
        }else{
            return $bonus[1];
        }

    }
}