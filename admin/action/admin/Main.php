<?php
class MainAction extends Action_Auth{
    public function index(){
        $Admin = Loader::model('admin@Admin');
        $admin = $Admin->where(array('id'=>$this->_admin['adminid']))->find();

        $Role = Loader::model('admin@Role');
        $role = $Role->where(array('id'=>$admin['roleid']))->find();
        $admin['role_name'] = $role['role_name'];
        $this->assign('admin', $admin);
        $this->display('admin/main');
    }
    public function settings(){
        redirect(U('profile'));
    }
    public function profile(){
        if($this->getParam('do')){
            $data = $this->getPost();
            $rs = $this->filter($data);
            if(false === $rs)
                $this->api_output($this->getError());

            $result['is_logout'] = 0;
            if(isset($data['password'])){
                if(empty($data['password'])){
                    unset($data['password']);
                }else{
                    $data['password'] = P_Crypt_Passwd::password($data['password']);
                    $result['is_logout'] = 1;
                }
            }

            $Admin = Loader::model('admin@Admin');
            $rs = $Admin->where(array('id'=>$this->_admin['adminid']))->save($data);
            if(!$rs)
                $this->api_output('FAIL');
            $this->_admin_log($this->_admin['username']);
            $this->api_output('SUCC', $result);
        }else{
            $Admin = Loader::model('admin@Admin');
            $admin = $Admin->where(array('id'=>$this->_admin['adminid']))->find();

            $Role = Loader::model('admin@Role');
            $role = $Role->where(array('id'=>$admin['roleid']))->find();
            $admin['role_name'] = $role['role_name'];
            $this->assign('admin', $admin);
            $this->display('admin/profile');
        }
    }
}