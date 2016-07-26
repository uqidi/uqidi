<?php
class IndexAction extends Action_Cron{
    public function run(){
        $today = $this->getParam(3);
        $this->save_parent($today);
        //$this->save_bonus($today);
        //$this->save_rebate($today);
        //$this->save_nbonus($today);
    }

    private function save_parent($today){
        $Tshare = Loader::model('Tshare');
        $where['today'] = $today;
        $list = $Tshare->where($where)->rkey('uid')->findAll();

        if(empty($list))
            return true;
        $uids = array_keys($list);
        $Users = Loader::model('Users');
        $wheres['id'] = array('in', $uids);
        $users = $Users->where($wheres)->field('id,parent_id')->rkey('id')->findAll();
        $array = array();
        foreach($list as $k=>$v){
            $parent_id = $users[$v['uid']]['parent_id'];
            if($parent_id>0){
                $array[$parent_id]['bonus'] = intval($array[$parent_id]['bonus']) + $v['bonus'];
                $array[$parent_id]['bonus_1700'] = intval($array[$parent_id]['bonus_1700']) + $v['bonus_1700'];
                $array[$parent_id]['bonus_1800'] = intval($array[$parent_id]['bonus_1800']) + $v['bonus_1800'];
            }
        }
        $bonus_1700 = 0;
        $bonus_1800 = 0;
        foreach($array as $k=>$v){
            $bs_1700 = $v['bonus']-$v['bonus_1700'];
            if($bs_1700>0){
                $bonus_1700 += $bs_1700*0.35;
            }
            $bs_1800 = $v['bonus']-$v['bonus_1800'];
            if($bs_1800>0){
                $bonus_1800 += $bs_1800*0.17;
            }
        }
        var_dump($bonus_1700, $bonus_1800);
    }

    /**
     * Organic123_
     * 35 17
     * 28 15
     * SELECT today,sum(bet),sum(bonus),sum(rebate),sum(bonus_1700),sum(share_1700),sum(bonus_1800),sum(share_1800),sum(bonus_1950),sum(share_1950) FROM `qd_tshare`  GROUP BY today
     */

    private function save_nbonus($today){
        $Gamelist = Loader::model('Gamelist');
        $where['prize']    = array('gt',0);
        $where['is_return']     = 0;
        $list = $Gamelist->setHashkey($today)->field("user_id as uid,again,prizenum,game_type,prize")->where($where)->findAll();
        if(empty($list))
            return false;
        $save_list = array();
        $bonuses = Loader::loadConfig('bonus');

        foreach($list as $v){
            $lottery_type = str_replace(array('_complex', '_single'), array('', ''), $v['game_type']);

            if(isset($bonuses[$lottery_type])){
                $bonus_1700 = $bonuses[$lottery_type][1]*$v['again']*$v['prizenum'];
                $bonus_1800 = $bonuses[$lottery_type][2]*$v['again']*$v['prizenum'];
                $bonus_1950 = $bonuses[$lottery_type][3]*$v['again']*$v['prizenum'];
            }else{
                $bonus_1700 = $bonus_1800 = $bonus_1950 = $v['prize'];
            }


            $save_list[$v['uid']] = array(
                'uid'           => $v['uid'],
                'today'         => $today,
                'bonus_1700'    => intval($save_list[$v['uid']]['bonus_1700'])+$bonus_1700,
                'bonus_1800'    => intval($save_list[$v['uid']]['bonus_1800'])+$bonus_1800,
                'bonus_1950'    => intval($save_list[$v['uid']]['bonus_1950'])+$bonus_1950,
            );
        }
        $Tshare = Loader::model('Tshare');
        foreach($save_list as $v){
            $where = array(
                'uid'   => $v['uid'],
                'today' => $v['today'],
            );
            $tshare = $Tshare->where($where)->find();
            $df_1700 = $tshare['bet'] - $v['bonus_1700'];
            $df_1800 = $tshare['bet'] - $v['bonus_1800'];
            $df_1950 = $tshare['bet'] - $v['bonus_1950'];
            if($df_1700>0){
                $v['share_1700'] = 0.28*$df_1700;
            }
            if($df_1800>0){
                $v['share_1800'] = 0.15*$df_1800;
            }
            if($df_1950>0){
                $v['share_1950'] = 0.03*$df_1950;
            }

            $Tshare->where($where)->save($v);
        }
        return true;
    }

    private function save_rebate($today){
        $Accountfeed = Loader::model('Accountfeed');
        $where['type'] = array('in', array(12,14,15));
        $list = $Accountfeed->setHashkey($today)->field("user_id as uid,'$today' as today,sum(money) as rebate")->where($where)->group('user_id')->findAll();

        if(empty($list))
            return false;
        $this->save_data($list);
        return true;
    }
    private function save_data($list){
        $Tshare = Loader::model('Tshare');
        foreach($list as $k=>$v){
            $where = array(
                'uid'   => $v['uid'],
                'today' => $v['today'],
            );
            $tshare = $Tshare->where($where)->find();
            if(empty($tshare)){
                $rs = $Tshare->add($v);
            }else{
                $rs = $Tshare->where($where)->save($v);
            }
        }
    }

    private function save_bonus($today){
        $Gamelist = Loader::model('Gamelist');
        $where['is_return']     = 0;
        $list = $Gamelist->setHashkey($today)->field("user_id as uid,sum(money*again) as bet,sum(prize) as bonus,'$today' as today")->where($where)->group('user_id')->findAll();
        if(empty($list))
            return false;
        $this->save_data($list);
        return $list;
    }

    private function save_lottery(){
        $Lottery = Loader::model('Lottery');
        $list = $Lottery->findAll();
        $lotterys = array();
        foreach($list as $v){
            $lotterys[$v['type']][$v['lottery_code']] = $v;
        }

        foreach($lotterys as $k=>$v){
            T_FileData::set('Lottery_'.$k, $v);
        }
        return $lotterys;
    }
}