<?php
class T_Order{

    /**
     * 创建订单号
     * @param $type     订单类型
     * @param $uid      用户UID
     * @param array $data
     *      time    订单时间
     *      bcf     前天当天后天   0前一天 1当天 2后一天 -1表示自行判断
     *      sn      随机编号
     * @return string
     */
    public static function makeNo($type, $uid, &$data = array()) {
        if(!$uid)
            return false;

        $data['type']   = $type;
        $data['uid']    = $uid;

        if(isset($data['time']) && $data['time']>0){
            $time = $data['time'];
        }else{
            $time = time();
        }

        $data['time']   = $time;

        if(isset($data['date']) || isset($data['date'][0])){
            $data['bcf']    = self::bcf($data['date']);
        }else{
            $data['date']   = date('Ymd', $time);
            $data['bcf']    = 5;
        }

        if(isset($data['sn'])){
            $data['sn'] = str_pad(strtoupper(base_convert(intval($data['sn']), 10, 36)), 2,  '0', STR_PAD_LEFT);
        }else{
            $data['sn'] = T_String::random(2, '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ');
        }


        $time   = $time - RUN_START_TIME;
        $uid    = str_pad(strtoupper(base_convert($uid,    10, 36)),    7,  '0', STR_PAD_LEFT);
        $time   = str_pad(strtoupper(base_convert($time,   10, 36)),    6,  '0', STR_PAD_LEFT);

        return $type.$uid.$time.$data['bcf'].$data['sn'];
    }

    function parseNo($orderNo) {
        $data['type']   = substr($orderNo, 0, 2);
        $data['uid']    = base_convert(substr($orderNo, 2, 7),   36, 10);
        $data['time']   = base_convert(substr($orderNo, 9, 6),   36, 10) + RUN_START_TIME;
        $data['bcf']    = substr($orderNo, 15, 1);
        $data['sn']     = substr($orderNo, 16, 2);
        $data['date']   = self::orderDate($data['time'], $data['bcf']);
        return $data;
    }

    public static function bcf($date, $time=0){
        !$time ? $time = time() : '';
        return strcmp($date, date('Ymd', $time))+5;
    }

    public static function orderDate($datetime, $bcf=5){
        $bcf -= 5;
        if($bcf>0){
            $datetime = strtotime("+$bcf day", $datetime);
        }elseif($bcf<=0){
            $datetime = strtotime("$bcf day", $datetime);
        }

        return date('Ymd', $datetime);
    }
}
