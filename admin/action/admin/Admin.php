<?php
class AdminAction extends Action_Auth{
    protected function _assign_field($name){
        $Form = Tool_Form::getInstance($this);
        $Role = Loader::model('admin@Role');
        $roles = $Role->where(array('status'=>C_Status::YES))->getField('id,role_name');
        $role_select = $Form->select($roles, 'roleid', 'roleid');
        $roles[0] = L('role');
        ksort($roles);
        $this->assign('role_select', $role_select);
        $role_search = $Form->select($roles, 'search_roleid', 'search[roleid]');
        $this->assign('role_search', $role_search);
        $status_select = C_Status::$status;
        foreach($status_select as $k=>$v){
            $status_select[$k] = L($v);
        }
        $status_select = $Form->select($status_select, 'status', 'status');
        $this->assign('status_select', $status_select);
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
            $Admin = Loader::model('admin@Admin');
            $data = array(
                'page'  => $page,
                'pre'   => $pre,
            );

            $map = array(
                'id'        => array('gt', 1),
            );

            if(isset($search['status']) && !empty($search['status'])){
                $map['status'] = $search['status'];
            }else{
                $map['status'] = array('lt', C_Status::DELETE);
            }

            if(isset($search['username']) && !empty($search['username'])){
                $map['username'] = array('like', $search['username'].'%');
            }

            if(isset($search['roleid']) && !empty($search['roleid'])){
                $map['roleid'] = $search['roleid'];
            }
            $data['total'] = (int)$Admin->where($map)->count();

            if($data['total']>0){
                $data['list'] = $Admin->where($map)->page($page, $pre)->order($sort)->findAll();
                foreach($data['list'] as $v){
                    $roleids[] = $v['roleid'];
                }

                $roleids = array_unique($roleids);

                $Role = Loader::model('admin@Role');
                $roles = $Role->field('id,role_name')->where(array('id'=>array('in', $roleids)))->rkey('id')->findAll();
                foreach($data['list'] as $k=>$v){
                    $data['list'][$k]['role']   = $roles[$v['roleid']]['role_name'];
                    $data['list'][$k]['status'] = L(C_Status::$status[$v['status']]);
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
            $this->_assign_field('admin@Admin');
            $this->assign('params', $data);
            $this->display('admin/admin_list');
        }
    }

    public function show(){
        $id = $this->getParam('id');
        $Menu = Loader::model('admin@Admin');
        $where['id'] = $id;
        $data = $Menu->where($where)->find();

        $data['status'] = (int)$data['status'];
        $data['status'] = L(C_Status::$status[$data['status']]);
        $Role = Loader::model('admin@Role');
        $data['roleid'] = $Role->where(array('id'=>$data['roleid']))->getField('role_name');
        $data['password'] = '******';
        $show_data = array();
        foreach($data as $k=>$v){
            $show_data[L($k)] = $v;
        }
        $this->api_output('SUCC', $show_data);
    }

    public function add(){
        $data = $this->getPost();

        if(empty($data['password'])){
            $this->api_output('PARAM', null, L('password'));
        }
        if(empty($data['username'])){
            $this->api_output('PARAM', null, L('username'));
        }

        $data['password'] = P_Crypt_Passwd::password($data['password']);


        $Admin = Loader::model('admin@Admin');
        $rs = $Admin->add($data);
        if(!$rs)
            $this->api_output('FAIL');
        $this->_admin_log($data['username']);
        $this->api_output('SUCC');
    }

    public function edit(){
        if($this->getParam('do')){
            $data = $this->getPost();
            $id = 0;
            if(isset($data['id'])){
                !empty($data['id']) ? $id = $data['id'] : '';
                unset($data['id']);
            }
            if($id<=1){
                $this->api_output('PARAM');
            }
            if(isset($data['username'])){
                $username = $data['username'];
                unset($data['username']);
            }

            if(empty($data['password'])){
                unset($data['password']);
            }else{
                $data['password'] = P_Crypt_Passwd::password($data['password']);
            }

            $Admin = Loader::model('admin@Admin');

            $rs = $Admin->where(array('id'=>$id))->save($data);

            if(!$rs)
                $this->api_output('FAIL');
            $this->_admin_log($username);
            $this->api_output('SUCC');
        }else{
            $id = $this->getParam('id');
            if($id<=1){
                $this->api_output('PARAM');
            }
            $Admin = Loader::model('admin@Admin');
            $where['id'] = $id;
            $admin = $Admin->where($where)->find();
            if($admin){
                $admin['password'] = '';
                $this->api_output('SUCC', $admin);
            }

            $this->api_output('FAIL');
        }
    }

    public function delete(){
        $id = $this->getParam('id');
        if($id<=1){
            $this->api_output('PARAM');
        }
        $Admin = Loader::model('admin@Admin');
        $data = array(
            'status'    => C_Status::DELETE,
            'id'        => $id,
        );
        $rs = $Admin->where(array('id'=>$id))->save($data);
        if(!$rs)
            $this->api_output('FAIL');
        $this->_admin_log($data['id']);
        $this->api_output('SUCC');
    }
}