<?php
class UserAction extends Action_Auth{
    protected function _assign_field($name){
        $Form = Tool_Form::getInstance($this);
        $status_select = C_Status::$status;
        foreach($status_select as $k=>$v){
            $status_select[$k] = L($v);
        }
        $status_select = $Form->select($status_select, 'status', 'status');

        $banks = Loader::loadConfig('pay', 'banks');

        foreach($banks as $k=>$v){
            $banks[$k] = $v['name'].'('.$v['code'].')';
        }

        $banks_select = $Form->select($banks, 'bank_type', 'bank_type');

        $switch = array(
            1   => L('on'),
            0   => L('off'),
        );

        $pay_sub_radio     = $Form->radio($switch, 'pay_sub',   'pay_sub');
        $look_cash_radio   = $Form->radio($switch, 'look_cash', 'look_cash');


        $groups = T_FileData::get('Group_list');

        $group_list[] = L('select_group');
        foreach($groups as $k=>$v){
            $group_list[$k] = $v['group_name'];
        }

        $group_select   = $Form->select($group_list, 'group_id', 'group_id');
        $group_search   = $Form->select($group_list, 'search_group_id', 'search[group_id]');

        $this->assign('pay_sub_radio',      $pay_sub_radio);
        $this->assign('look_cash_radio',    $look_cash_radio);

        $this->assign('group_search', $group_search);
        $this->assign('group_select', $group_select);

        $this->assign('status_select',  $status_select);
        $this->assign('banks_select',   $banks_select);
    }
    public function index(){
        $page = (int)$this->getQuery('page');
        $pre = (int)$this->getQuery('pre');
        $sort   = urldecode($this->getQuery('sort'));
        empty($page)    ? $page = 1 : '';
        empty($pre)     ? $pre = 20 : '';
        empty($sort)    ? $sort= 'uid desc' : '';

        $search = $this->getQuery('search');
        if($this->getParam('do')){
            $User = Loader::model('web@User');
            $data = array(
                'page'  => $page,
                'pre'   => $pre,
            );

            $map = array();

            if(isset($search['status']) && !empty($search['status'])){
                $map['status'] = $search['status'];
            }else{
                $map['status'] = array('lt', C_Status::DELETE);
            }

            if(isset($search['username']) && !empty($search['username'])){
                $map['username'] = array('like', $search['username'].'%');
            }

            if(isset($search['group_id']) && $search['group_id']>0){
                $map['group_id'] = $search['group_id'];
            }

            $data['total'] = (int)$User->where($map)->count();

            if($data['total']>0){
                $data['list'] = $User->where($map)->page($page, $pre)->order($sort)->findAll();
                $parent_ids = array();
                foreach($data['list'] as $v){
                    if($v['parent_id']>0)
                        $parent_ids[] = $v['parent_id'];
                    $uids[] = $v['uid'];
                }
                if($parent_ids){
                    array_unique($parent_ids);
                    $parents = $User->where(array('uid'=>array('in', $parent_ids)))->rkey('uid')->findAll();
                }else{
                    $parents = array();
                }


                $groups = T_FileData::get('Group_list');
                foreach($data['list'] as $k=>$v){
                    $data['list'][$k]['parent'] = $v['parent_id']>0 ? $parents[$v['parent_id']]['username'] : '';
                    $data['list'][$k]['status'] = L(C_Status::$status[$v['status']]);
                    $data['list'][$k]['login_time'] = $v['login_time']>0 ? date('Y-m-d H:i:s', $v['login_time']) : '';
                    $data['list'][$k]['group_name'] = $v['group_id']>0 ? $groups[$v['group_id']]['group_name'] : '';
                }

                $data['total_page'] = ceil($data['total']/$pre);
            }else{
                $data['list'] = array();
                $data['total_page'] = 0;
            }

            $this->api_output('SUCC', $data);
        }else{
            $data = array(
                'page'  => $page,
                'pre'   => $pre,
                'sort'  => $sort,
            );
            !empty($search) ? $data['search'] = $search : '';
            $this->_assign_field('web@User');
            $this->assign('params', $data);
            $this->display('user/user_list');
        }
    }

    public function show(){
        $id = $this->getParam('id');
        $User = Loader::model('web@User');
        $where['uid'] = $id;
        $data = $User->where($where)->find();

        $data['status'] = (int)$data['status'];
        $data['avatar'] = empty($data['avatar']) ? 'user-4.png' : $data['avatar'];
        $show_data['username'] = $data['username'];
        if($data['group_id']>0){
            $group = T_FileData::get('Group_'.$data['group_id']);
            $show_data['group_name'] = $group['group_name'];
        }
        $show_data['nickname'] = $data['nickname'];
        $show_data['avatar'] = '<img src="'.C('ria_url').'images/'.$data['avatar'].'" alt="user-img" id="form_avatar" class="img-cirlce img-responsive img-thumbnail"/>';
        if($data['parent_id']>0){
            $parents = $User->where(array('uid'=>array('in', $data['parents'])))->rkey('uid')->getField('uid,username');
            $show_data['parents'] = implode(' > ', $parents);
        }else{
            $show_data['parents'] = L('top_user');
        }



        $show_data['user_cash'] = $data['user_cash'];
        $show_data['login_cnt'] = $data['login_cnt'];
        $show_data['status']    = $data['status'];
        if($data['reg_ip']){
            $show_data['register_info'] = $data['reg_ip'];
        }

        if($data['login_time']>0){
            $show_data['login_info'] = $data['login_ip'].' | '.date('Y-m-d H:i:s',$data['login_time']);
        }
        $this->_show($show_data);
    }

    public function add(){
        $data = $this->getPost();

        $rs = $this->filter($data,'', array('password'=>$data['username'], 'trader_password'=>$data['username']));
        if(false === $rs)
            $this->api_output($this->getError());

        if(!empty($data['trader_password'])){
            $add_data['trader_password'] = P_Crypt_Passwd::password($data['trader_password'], 8);
        }

        $add_data['parent_id']  = 1;

        $add_data['password']   = P_Crypt_Passwd::password($data['password']);
        $add_data['username']   = $data['username'];
        $add_data['nickname']   = $data['nickname'];
        $add_data['group_id']   = $data['group_id'];
        $add_data['status']     = C_Status::YES;

        $add_data['parents']    = 1;

        $User = Loader::model('web@User');
        $where['username'] = $data['username'];
        $where['nickname'] = $data['nickname'];
        $where['_logic'] = 'or';
        $cnt = $User->where($where)->count();

        if($cnt){
            $this->api_output('EXISTS', $cnt);
        }
        $rs = $User->add($add_data);
        if(!$rs)
            $this->api_output('FAIL');

        $cache_data = array(
            'uid'       => $rs,
            'username'  => $data['username'],
            'nickname'  => $data['nickname'],
            'user_cash' => 0,
            'group_id'  => $data['group_id'],
            'parents'   => $data['parents'],
            'status'    => C_Status::YES,
        );
        Lib_User::setCache($rs, $cache_data);


        $this->_admin_log($data['username']);
        $this->api_output('SUCC',array('is_reload'=>true));
    }



    public function edit(){
        if($this->getParam('do')){
            $data = $this->getPost();

            $rs = $this->filter($data,'', array('password'=>$data['username'], 'trader_password'=>$data['username']));
            if(false === $rs)
                $this->api_output($this->getError());

            if(!empty($data['password'])){
                $save_data['password'] = P_Crypt_Passwd::password($data['password']);
            }

            if(!empty($data['trader_password'])){
                $save_data['trader_password'] = P_Crypt_Passwd::password($data['trader_password'], 8);
            }



            $save_data['nickname']  = $data['nickname'];
            $save_data['group_id']  = $data['group_id'];

            $User = Loader::model('web@User');

            $user = $User->where(array('nickname'=>$data['nickname']))->find();

            if(empty($user)){
                $user = $User->where(array('uid'=>$data['uid']))->find();
            }else{
                if($user['uid'] != $data['uid']){
                    $this->api_output('EXISTS', $user);
                }
            }


            $rs = $User->where(array('uid'=>$data['uid']))->save($save_data);

            if(!$rs)
                $this->api_output('FAIL');

            $cache_data = array(
                'nickname'  => $data['nickname'],
                'group_id'  => $data['group_id'],
                'status'    => $user['status'],
            );
            Lib_User::setCache($user['uid'], $cache_data);

            $this->_admin_log($data['username']);
            $this->api_output('SUCC', array('is_reload'=>true));
        }else{
            $id = $this->getParam('id');
            if($id<=0){
                $this->api_output('PARAM');
            }
            $User = Loader::model('web@User');
            $where['uid'] = $id;
            $data = $User->where($where)->find();
            if($data){
                $data['password']           = '';
                $data['trader_password']    = '';

                $this->api_output('SUCC', array('action'=>$this->_action, 'info'=>$data));
            }

            $this->api_output('FAIL');
        }
    }

    public function editInfo(){
        if($this->getParam('do')){
            $data = $this->getPost();

            $rs = $this->filter($data);
            if(false === $rs)
                $this->api_output($this->getError());

            $UserInfo = Loader::model('web@User_Info');

            $add_data = array(
                'uid'   => $data['uid'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'qq'    => $data['qq'],
            );

            if(!empty($data['email'])){
                $info = $UserInfo->where(array('email'=>$data['email']))->find();
                if(!empty($info) && !empty($info['email']) && $info['uid'] != $data['uid']){
                    $this->api_output('EXISTS');
                }
            }


            $rs = $UserInfo->add($add_data, array(), true);
            if(!$rs)
                $this->api_output('FAIL');

            $cache_data = array(
                'email' => $data['email'],
                'phone' => $data['phone'],
                'qq'    => $data['qq'],
            );
            Lib_User::setCache($data['uid'], $cache_data);
            $this->_admin_log($add_data);
            $this->api_output('SUCC', array('is_reload'=>false));
        }else{
            $id = $this->getParam('id');
            if($id<=0){
                $this->api_output('PARAM');
            }
            $UserInfo = Loader::model('web@User_Info');
            $where['uid'] = $id;
            $data = $UserInfo->where($where)->find();
            if(false === $data){
                $this->api_output('FAIL', $data);
            }
            $data['uid'] = $id;
            $this->api_output('SUCC', array('action'=>$this->_action, 'info'=>$data));
        }
    }

    public function editBank(){
        if($this->getParam('do')){
            $data = $this->getPost();
            $rs = $this->filter($data);
            if(false === $rs)
                $this->api_output($this->getError());


            $UserBank = Loader::model('web@User_Bank');
            $bank = $UserBank->where(array('bank_type'=>$data['bank_type'],'bank_code'=>$data['bank_code']))->find();
            if(false === $bank){
                $this->api_output('FAIL');
            }

            if(!empty($bank)){
                if($bank['uid'] != $data['uid']){
                    $this->api_output('EXISTS');
                }
                $rs = $UserBank->where(array('id'=>$bank['id']))->save(array('bank_user' => $data['bank_user']));
            }else{
                $add_data = array(
                    'uid'       => $data['uid'],
                    'bank_type' => $data['bank_type'],
                    'bank_user' => $data['bank_user'],
                    'bank_code' => $data['bank_code'],
                );
                $rs = $UserBank->add($add_data);
            }

            if(!$rs)
                $this->api_output('FAIL');

            $cache_data = array(
                'bind_pay_cnt' => 1,
            );
            Lib_User::setCache($data['uid'], $cache_data);
            $this->_admin_log($data);
            $this->api_output('SUCC', array('is_reload'=>false));
        }else{
            $id = $this->getParam('id');
            if($id<=0){
                $this->api_output('PARAM');
            }
            $UserBank = Loader::model('web@User_Bank');
            $where['uid'] = $id;
            $data = $UserBank->where($where)->findAll();
            if(false === $data){
                $this->api_output('FAIL', $data);
            }

            $banks = Loader::loadConfig('pay', 'banks');
            foreach($data as $k=>$v){
                $data[$k]['bank_type'] = $banks[$v['bank_type']]['name'];
            }

            $this->api_output('SUCC', array('action'=>$this->_action, 'info'=>array('uid'=>$id, 'list'=>$data)));
        }
    }

    public function editConfig(){
        if($this->getParam('do')){
            $data = $this->getPost();
            if(!isset($data['uid']) && $data['uid']<=0){
                $this->api_output('PARAM');
            }

            $add_data = array(
                'uid'       => $data['uid'],
                'pay_sub'   => $data['pay_sub'],
                'look_cash' => $data['look_cash'],
            );
            $UserConfig= Loader::model('web@User_Config');
            $rs = $UserConfig->add($add_data, array(), true);
            if(!$rs)
                $this->api_output('FAIL');
            $this->_admin_log($add_data);
            $this->api_output('SUCC',array('is_reload'=>false));
        }else{
            $id = $this->getParam('id');
            if($id<=0){
                $this->api_output('PARAM');
            }
            $UserConfig= Loader::model('web@User_Config');
            $where['uid'] = $id;
            $data = $UserConfig->where($where)->find();
            if(false === $data){
                $this->api_output('FAIL', $data);
            }
            $data['uid'] = $id;
            $this->api_output('SUCC', array('action'=>$this->_action, 'info'=>$data));
        }
    }

    public function enable(){
        $this->_status(C_Status::YES);
    }

    public function delete(){
        $this->_status(C_Status::DELETE);
    }

    public function disable(){
        $this->_status(C_Status::NO);
    }

    private function _status($status){
        $ids    = $this->getPost('ids');

        if(empty($ids) || !is_array($ids)){
            $this->api_output('PARAM');
        }

        $uids = '';
        foreach($ids as $uid){
            $rs = Lib_User::status($uid, $status);
            if(false == $rs){
                continue;
            }
            $uids[] = $uid;
        }

        if(empty($uids)){
            $this->api_output('FAIL');
        }

        $this->_admin_log($uids);
        $this->api_output('SUCC', null, implode(',', $uids));
    }
}