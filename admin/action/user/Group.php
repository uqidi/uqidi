<?php
class GroupAction extends Action_Auth{
    protected function _assign_field($name){
        $Form = Tool_Form::getInstance($this);
        $status_select = C_Status::$status;
        foreach($status_select as $k=>$v){
            $status_select[$k] = L($v);
        }
        $status_select      = $Form->select($status_select, 'status', 'status');
        $group_grades       = Loader::loadConfig('user', 'group_grades');
        $group_grade_select = $Form->select($group_grades, 'group_grade', 'group_grade');

        $pay_list = T_FileData::get('Pay_list');
        $pay_select[] = L('select_account');
        foreach($pay_list as $k=>$v){
            $pay_select[$k] = $v['account_name'].' | '.$v['account_id'];
        }

        $fetch_account_select   = $Form->select($pay_select, 'fetch_account', 'fetch_account');
        $pay_account_select     = $Form->select($pay_select, 'pay_account', 'pay_account');

        $this->assign('status_select',          $status_select);
        $this->assign('group_grade_select',     $group_grade_select);
        $this->assign('fetch_account_select',          $fetch_account_select);
        $this->assign('pay_account_select',             $pay_account_select);
    }
    public function index(){
        $page = (int)$this->getQuery('page');
        $pre = (int)$this->getQuery('pre');
        $sort   = urldecode($this->getQuery('sort'));
        empty($page)    ? $page = 1 : '';
        empty($pre)     ? $pre = 20 : '';
        empty($sort)    ? $sort= 'id desc' : '';

        $search = $this->getQuery('search');
        if($this->getParam('do')){
            $UserGroup = Loader::model('web@User_Group');
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

            if(isset($search['group_name']) && !empty($search['group_name'])){
                $map['group_name'] = array('like', $search['group_name'].'%');
            }

            $data['total'] = (int)$UserGroup->where($map)->count();

            if($data['total']>0){
                $data['list'] = $UserGroup->where($map)->page($page, $pre)->order($sort)->findAll();
                $group_grades = Loader::loadConfig('user', 'group_grades');
                $pay_list = T_FileData::get('Pay_list');
                foreach($data['list'] as $k=>$v){
                    $data['list'][$k]['status'] = L(C_Status::$status[$v['status']]);
                    $data['list'][$k]['group_grade']  = $group_grades[$v['group_grade']];
                    $data['list'][$k]['fetch_account'] = $pay_list[$v['fetch_account']]['account_name'];
                    $data['list'][$k]['pay_account'] = $pay_list[$v['pay_account']]['account_name'];
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
            empty($search) ? $data['search'] = $search : '';
            $this->_assign_field('web@User_Group');
            $this->assign('params', $data);
            $this->display('user/group_list');
        }
    }

    public function show(){
        $id = $this->getParam('id');
        $UserGroup = Loader::model('web@User_Group');
        $where['id'] = $id;
        $data = $UserGroup->where($where)->find();
        if(empty($data))
            $this->api_output('SUCC', array());
        $group_grades = Loader::loadConfig('user', 'group_grades');
        $data['group_grade'] = $group_grades[$data['group_grade']];
        if($data['fetch_account']>0){
            $fetch_account = T_FileData::get('Pay_'.$data['fetch_account']);
            $data['fetch_account'] = $fetch_account['account_name'];
        }
        if($data['pay_account']>0){
            $pay_account = T_FileData::get('Pay_'.$data['pay_account']);
            $data['pay_account'] = $pay_account['account_name'];
        }
        $this->_show($data);
    }

    public function add(){
        $data = $this->getPost();
        $rs = $this->filter($data);
        if(false === $rs){
            $this->api_output($this->getError());
        }

        $User = Loader::model('web@User_Group');

        $rs = $User->add($data);
        if(!$rs)
            $this->api_output('FAIL');

        $this->_admin_log($data['group_name']);
        $this->api_output('SUCC',array('is_reload'=>true));
    }



    public function edit(){
        if($this->getParam('do')){
            $data = $this->getPost();
            $rs = $this->filter($data);
            if(false === $rs){
                $this->api_output($this->getError());
            }

            $UserGroup = Loader::model('web@User_Group');

            $rs = $UserGroup->where(array('id'=>$data['id']))->save($data);

            if(!$rs)
                $this->api_output('FAIL');
            $this->_admin_log($data['group_name']);
            $this->api_output('SUCC', array('is_reload'=>true));
        }else{
            $id = $this->getParam('id');
            if($id<=0){
                $this->api_output('PARAM');
            }
            $UserGroup = Loader::model('web@User_Group');
            $where['id'] = $id;
            $data = $UserGroup->where($where)->find();
            if($data){
                $this->api_output('SUCC', array('action'=>$this->_action, 'info'=>$data));
            }

            $this->api_output('FAIL');
        }
    }


    public function publish(){
        $UserGroup = Loader::model('web@User_Group');
        $list = $UserGroup->rkey('id')->FindAll();
        foreach($list as $k=>$v){
            $file = 'Group_'.$k;
            T_FileData::set($file, $v);
            T_Rsync::send_php($file);
            if($v['status'] != C_Status::YES){
                unset($list[$k]);
            }
        }

        $file = 'Group_list';
        $rs = T_FileData::set($file, $list);
        T_Rsync::send_php($file);
        if(!$rs)
            $this->api_output('FAIL');
        $this->api_output('SUCC');
    }

}