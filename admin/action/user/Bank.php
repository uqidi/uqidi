<?php
class BankAction extends Action_Auth{
    protected function _assign_field($name){
        $Form = Tool_Form::getInstance($this);
        $banks = Loader::loadConfig('pay', 'banks');

        $show_banks[0]   = L('bank_type');
        foreach($banks as $k=>$v){
            $show_banks[$k] = $v['name'].'('.$v['code'].')';
        }
        $banks_select = $Form->select($show_banks, 'form_bank_type', 'search[bank_type]');

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
            $UserBank = Loader::model('web@User_Bank');
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

            if(isset($search['bank_user']) && !empty($search['bank_user'])){
                $map['bank_user'] = array('like', $search['bank_user'].'%');
            }

            if(isset($search['bank_type']) && $search['bank_type']>=0){
                $map['bank_type'] = $search['bank_type'];
            }

            $data['total'] = (int)$UserBank->where($map)->count();

            if($data['total']>0){
                $data['list'] = $UserBank->where($map)->page($page, $pre)->order($sort)->findAll();
                foreach($data['list'] as $v){
                    $uids[] = $v['uid'];
                }
                array_unique($uids);
                $User = Loader::model('web@User');
                $users = $User->field('uid,username')->where(array('uid'=>array('in', $uids)))->rkey('uid')->findAll();
                $banks = Loader::loadConfig('pay', 'banks');
                foreach($data['list'] as $k=>$v){
                    $data['list'][$k]['username']   = $users[$v['uid']]['username'];
                    $data['list'][$k]['bank_type']  = $banks[$v['bank_type']]['name'];
                    $data['list'][$k]['utime']      = date('Y-m-d H:i:s', $v['utime']);
                    $data['list'][$k]['status']     = L(C_Status::$status[$v['status']]);
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
            $this->_assign_field('web@User_Bank');
            $this->assign('params', $data);
            $this->display('user/bank_list');
        }
    }

    public function show(){
        $id = $this->getParam('id');
        $UserBank = Loader::model('web@User_Bank');
        $where['id'] = $id;
        $data = $UserBank->where($where)->find();

        $User = Loader::model('web@User');
        $username = $User->where(array('uid'=>$data['uid']))->getField('username');
        $data['username'] = $username;
        unset($data['uid']);
        $banks = Loader::loadConfig('pay', 'banks');
        $data['bank_type']  = $banks[$data['bank_type']]['name'];
        $data['status']     = L(C_Status::$status[$data['status']]);
        $data['ctime']      = date('Y-m-d H:i:s', $data['ctime']);
        $data['utime']      = date('Y-m-d H:i:s', $data['utime']);
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
        Lib_User::setCache($bank['uid'], array('bind_pay_cnt'=>-1));
        $this->_admin_log($bank);
        $this->api_output('SUCC');
    }
}