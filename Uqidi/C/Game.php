<?php
/**
 * @fileoverview:   游戏配置
 * @author:         Uqidi
 * @date:           2015-10-24
 * @copyright:      Uqidi.com
 */

/**
 * 1. 超级代理返点7.8对应的奖金1800，0.3对应的奖金为1950
 * 2. 总代理
 ＊   a. 返点7.7
 ＊   b. 超级代理的直属下级
 ＊   c. 需要超级代理或后台设置为总代理
 ＊   d. 能开出7.7分红号（消耗分红号配额）
 ＊   e. 能开出7.6高点号（消耗高点号配额）
 ＊   f. 不限制开7.5高点号
 * 3. 分红号
 ＊   a. 返点7.7
 ＊   b. 总代理的直属下级
 ＊   c. 需要总代理或后台设置为分红号
 ＊   d. 能开出7.6高点号（消耗高点配额）
 ＊   e. 不限制开7.5高点号
 * 4. 高点号
 ＊   a. 返点7.5和7.6
 ＊   b. 7.6的高点号能开出7.5高点号（消耗高点配额）
 * 5. 后台可以设置是否可以投注，默认为可以投注
 * 6. 返点变化步长：0<x<=12对应的0.5 | x>12对应0.1（1700系列下）
 * 7. 配额：
 *      1. 分红号配额个数
 *      2. 高点号配额个数
 * 8. 奖金设置三个梯度：1700 、1800 、最高奖金
 * 9. 不定位的奖金和返点是不能调整的
 */

abstract class C_Game{
    const TYPE_LO_SSC   = 10;
    const TYPE_LO_LFC   = 20;

    const SERIES_1700   = 1;
    const SERIES_1800   = 2;
    const SERIES_1900   = 4;


    const USER_TOP_PERCENT      = 12.8;
    const USER_BONUS_PERCENT    = 12.7;
    const USER_HIGH_PERCENT     = 12.6;

    const MODE_YUAN     = 1;
    const MODE_JIAO     = 10;
    const MODE_FEN      = 100;


    public static $ctype = array(
        1   => '单式',
        2   => '复式',
    );

    public static $modes = array(
        self::MODE_YUAN => '元',
        self::MODE_JIAO => '角',
        self::MODE_FEN  => '分',
    );

    public static $type_los = array(
        self::TYPE_LO_SSC     => array(
            'key'       => 'ssc',
            'series'    => array(
                self::SERIES_1700   => array(0,     1700),
                self::SERIES_1800   => array(5,     1800),
                self::SERIES_1900   => array(10,    1900),
            ),
        ),
        self::TYPE_LO_LFC     => array(
            'key'       => 'lfc',
            'series'    => array(
                self::SERIES_1700   => array(0,     1700),
                self::SERIES_1800   => array(5,     1800),
            ),
        ),
    );


    public static function fpercent($bonus, $fbonus=1700) {
        return bcdiv($bonus-$fbonus, 20);
    }

    public static function series_list($type_lo, $percent){
        if(!isset(self::$type_los[$type_lo])){
            return false;
        }
        $series_list = self::$type_los[$type_lo]['series'];

        $list = array();
        foreach($series_list as $k=>$v){
            if($percent>=$v[0]){
                $max_percent = bcsub($percent, $v[0], 1);
                if($v[0]>=10 && $max_percent>0){
                    $n = intval($max_percent/0.5);
                    if($n>0){
                        for($i=0;$i<=$n;$i++){
                            $list[] =array(
                                'series'        => $k,
                                'bonus'         => $v[1]+10*$i,
                                'max_percent'   => bcsub($max_percent, bcmul($i,0.5, 1), 1),
                                'option'        => true,
                            );
                        }
                    }
                }else{
                    $list[] = array(
                        'series'        => $k,
                        'bonus'         => $v[1],
                        'max_percent'   => $max_percent,
                    );
                }
            }
        }
        return $list;
    }

    public static function code_pos_list($len=5){
        for($i=1;$i<=$len;$i++){
            $str = str_pad(1, $i, '0', STR_PAD_RIGHT);
            $v = base_convert($str, 2, 10);
            $list[$v] = $v;
        }
        krsort($list);
        return $list;
    }

    public static function code_pos_convert($pos, $len=5, $to=0){
        if($to>0)
            return base_convert($pos, 2, 10);

        return str_pad(base_convert($pos, 10, 2), $len, '0', STR_PAD_LEFT);
    }

}

