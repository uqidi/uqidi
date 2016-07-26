<?php
class PayAction extends Action_Auth{
    protected function _assign_field($name){
        $Form = Tool_Form::getInstance($this);
        $status_select = C_Status::$status;
        foreach($status_select as $k=>$v){
            $status_select[$k] = L($v);
        }
        $status_select      = $Form->select($status_select, 'status', 'status');

        $payConfig = Loader::loadConfig('pay');
        foreach($payConfig['banks'] as $k=>$v){
            $show_banks[$k] = $v['name'].'('.$v['code'].')';
        }
        $account_types_select       = $Form->select($show_banks,            'account_type', 'account_type');
        $pay_types_select           = $Form->select($payConfig['pay_types'],'pay_type',     'pay_type');

        $this->assign('status_select',              $status_select);
        $this->assign('account_types_select',       $account_types_select);
        $this->assign('pay_types_select',           $pay_types_select);
    }
    public function index(){
        $page = (int)$this->getQuery('page');
        $pre = (int)$this->getQuery('pre');
        $sort   = urldecode($this->getQuery('sort'));
        empty($page)    ? $page = 1 : '';
        empty($pre)     ? $pre = 30 : '';
        empty($sort)    ? $sort= 'id desc' : '';

        $search = $this->getQuery('search');
        if($this->getParam('do')){
            $Pay = Loader::model('web@Pay');
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
            if(isset($search['account_name']) && !empty($search['account_name'])){
                $map['account_name'] = array('like', $search['account_name'].'%');
            }

            if(isset($search['pay_type']) && $search['pay_type']>=0){
                $map['pay_type'] = $search['pay_type'];
            }

            $data['total'] = (int)$Pay->where($map)->count();

            if($data['total']>0){
                $data['list'] = $Pay->where($map)->page($page, $pre)->order($sort)->findAll();
                $payConfig = Loader::loadConfig('pay');
                foreach($data['list'] as $k=>$v){
                    $data['list'][$k]['pay_type']       = $payConfig['pay_types'][$v['pay_type']];
                    $data['list'][$k]['account_type']   = $payConfig['banks'][$v['account_type']]['name'];
                    $data['list'][$k]['utime']          = date('Y-m-d H:i:s', $v['utime']);
                    $data['list'][$k]['status']         = L(C_Status::$status[$v['status']]);
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
            $this->_assign_field('web@Pay');
            $this->assign('params', $data);
            $this->display('user/pay_list');
        }
    }

    public function add(){
        $data = $this->getPost();
        $rs = $this->filter($data);
        if(false === $rs){
            $this->api_output($this->getError());
        }

        $Pay = Loader::model('web@Pay');
        $rs = $Pay->add($data);
        if(!$rs)
            $this->api_output('FAIL');
        $this->_admin_log($data);
        $this->api_output('SUCC', array('is_reload'=>true));
    }

    public function edit(){
        if($this->getParam('do')){
            $data = $this->getPost();
            $rs = $this->filter($data);
            if(false === $rs){
                $this->api_output($this->getError());
            }
            $id = $data['id'];
            unset($data['id']);

            $Pay = Loader::model('web@Pay');
            $rs = $Pay->where(array('id'=>$id))->save($data);

            if(!$rs)
                $this->api_output('FAIL');
            $this->_admin_log($data);
            $this->api_output('SUCC', array('is_reload'=>true));
        }else{
            $id = $this->getParam('id');
            if($id<=0){
                $this->api_output('PARAM');
            }

            $Pay = Loader::model('web@Pay');
            $where['id'] = $id;
            $data = $Pay->where($where)->find();
            if($data){
                $this->api_output('SUCC', array('action'=>$this->_action, 'info'=>$data));
            }

            $this->api_output('FAIL');
        }
    }

    public function publish(){
        $Pay = Loader::model('web@Pay');
        $list = $Pay->rkey('id')->FindAll();
        foreach($list as $k=>$v){
            $file = 'Pay_'.$k;
            T_FileData::set($file, $v);
            T_Rsync::send_php($file);
            if($v['status'] != C_Status::YES){
                unset($list[$k]);
            }
        }

        $file = 'Pay_list';
        $rs = T_FileData::set($file, $list);
        T_Rsync::send_php($file);
        if(!$rs)
            $this->api_output('FAIL');
        $this->api_output('SUCC');
    }

    public function show(){
        $id = $this->getParam('id');
        $Pay = Loader::model('web@Pay');
        $where['id'] = $id;
        $data = $Pay->where($where)->find();

        $pay = Loader::loadConfig('pay');
        $data['pay_type']       = $pay['pay_types'][$data['pay_type']];
        $data['account_type']   = isset($pay['banks'][$data['account_type']]) ? $pay['banks'][$data['account_type']]['name'] : '';
        $data['status']         = L(C_Status::$status[$data['status']]);
        $data['ctime']          = date('Y-m-d H:i:s', $data['ctime']);
        $data['utime']          = date('Y-m-d H:i:s', $data['utime']);
        if(empty($data['ext'])){
            unset($data['ext']);
        }
        $show_data = array();
        foreach($data as $k=>$v){
            $show_data[L($k)] = $v;
        }
        $this->api_output('SUCC', $show_data);
    }

    public function delete(){
        $id = $this->getParam('id');
        if($id<=0){
            $this->api_output('PARAM');
        }
        $UserBank = Loader::model('web@User_Bank');
        $where['id'] = $id;
        $bank = $UserBank->where($where)->find();
        if(empty($bank))
            $this->api_output('SUCC');
        
        $data = $UserBank->where($where)->delete();
        if(false === $data){
            $this->api_output('FAIL');
        }
        $this->_admin_log($bank);
        $this->api_output('SUCC');
    }
}