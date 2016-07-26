<?php
class Lib_User extends L_User{

    public static function setCache($uid, $data){
        if(isset($data['status']) && C_Status::DELETE != $data['status'])
            return parent::setCache($uid, $data);
        $user = parent::getCache($uid);
        if(false === $user){
            return false;
        }

        if(isset($data['bind_pay_cnt'])){
            if(isset($user['bind_pay_cnt'])){
                $data['bind_pay_cnt'] = $user['bind_pay_cnt']+$data['bind_pay_cnt'];
            }
            $data['bind_pay_cnt'] = $data['bind_pay_cnt']>0 ? $data['bind_pay_cnt'] : 0;
        }

        if(isset($user['status']) && $user['status'] == C_Status::YES)
            return parent::setCache($uid, $data);
        return true;
    }

    /**
     * 修改状态
     * @param $uid
     * @param $status
     * @return bool|mixed
     */
    public static function status($uid, $status){
        $User = Loader::model('web@User');
        $data = array(
            'status'    => $status,
        );
        $where = array('uid'=>$uid);
        $rs = $User->where($where)->save($data);
        if(false === $rs)
            return false;

        if(C_Status::DELETE == $status){
            parent::delCache($uid);
            return true;
        }

        $user = $User->field('uid,username,nickname,avatar,user_cash,percent,group_id,parents,status')->where($where)->find();

        if(false == $user){
            return false;
        }

        $UserInfo = Loader::model('web@User_Info');
        $user_info = $UserInfo->where($where)->find();
        if(!empty($user_info)){
            $user['email']  = $user_info['email'];
        }

        $UserBank = Loader::model('web@User_Bank');
        $bind_pay_cnt = $UserBank->where($where)->count();
        $user['bind_pay_cnt'] = $bind_pay_cnt;
        return parent::setCache($uid, $user);
    }

    /**
     * 返点可调控范围
     * @param $parent_id
     * @param int $uid
     * @return mixed
     */
    public static function percentLimit($parent_id, $uid=0){
        $parent_id = (int)$parent_id;

        $gameConfig = Loader::loadConfig('game');

        $result['min'] = 0;


        if($uid>0){
            $User = Loader::model('web@User');
            $min = $User->where(array('parent_id'=>$uid))->max('percent');
            if(!empty($min)){
                if($min<$gameConfig['top_vip_percent']){
                    if($min>=4.5) {
                        $result['min'] = $min + 0.1;
                    }else{
                        $result['min'] = $min + 0.5;
                    }
                }else{
                    $result['min'] = $min;
                }
            }
        }

        if($parent_id<=0){
            $result['max'] = $gameConfig['top_svip_percent'];
            return $result;
        }

        $User = Loader::model('web@User');

        $parent_percent = $User->where(array('uid'=>$parent_id))->getField('percent');
        $parent_percent = floatval($parent_percent);
        if($parent_percent >= $gameConfig['top_vip_percent']){
            $result['max'] = $parent_percent;
        }else{
            if($parent_percent>=4.5) {
                $result['max'] = $parent_percent - 0.1;
            }else{
                $result['max'] = $parent_percent - 0.5;
            }
        }
        return $result;
    }

}

