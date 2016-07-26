<?php
class RoleAction extends Action_Auth{
    protected function _assign_field($name){
        $Form = Tool_Form::getInstance($this);
        $status_select = C_Status::$status;
        foreach($status_select as $k=>$v){
            $status_select[$k] = L($v);
        }
        $status_select = $Form->select($status_select, 'status', 'status');
        $this->assign('menu_list', $this->menu_list());
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
            $Role = Loader::model('admin@Role');
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

            if(isset($search['role_name']) && !empty($search['role_name'])){
                $map['role_name'] = array('like', $search['role_name'].'%');
            }

            $data['total'] = (int)$Role->where($map)->count();

            if($data['total']>0){
                $data['list'] = $Role->field('id,role_name,status')->where($map)->page($page, $pre)->order($sort)->findAll();
                foreach($data['list'] as $k=>$v){
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
            $this->_assign_field('admin@Role');
            $this->assign('params', $data);
            $this->display('admin/role_list');
        }
    }

    public function show(){
        $id = $this->getParam('id');
        $Role = Loader::model('admin@Role');
        $where['id'] = $id;
        $data = $Role->field('id,role_name,status')->where($where)->find();
        $data['status'] = (int)$data['status'];
        $data['status'] = L(C_Status::$status[$data['status']]);
        $show_data = array();
        foreach($data as $k=>$v){
            $show_data[L($k)] = $v;
        }
        $this->api_output('SUCC', $show_data);
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

            if(isset($data['selectall']) && $data['selectall']>0){
                $data['priv'] = 'all';
            }else{
                if(!isset($data['priv']))
                    $data['priv']= '';
                else
                    $data['priv'] = implode(',', $data['priv']);
            }

            $Role = Loader::model('admin@Role');

            $rs = $Role->where(array('id'=>$id))->save($data);

            if(!$rs)
                $this->api_output('FAIL');
            $this->_admin_log($data['role_name']);
            $this->api_output('SUCC');
        }else{
            $id = $this->getParam('id');
            if($id<1){
                $this->api_output('PARAM');
            }
            $Role = Loader::model('admin@Role');
            $where['id'] = $id;
            $role = $Role->where($where)->find();
            if($role){
                if($role['priv'] !== 'all'){
                    $role['priv'] = explode(',',$role['priv']);
                }
                $this->api_output('SUCC', $role);
            }

            $this->api_output('FAIL');
        }
    }

    public function add(){
        $data = $this->getPost();

        if(empty($data['role_name'])){
            $this->api_output('PARAM', null, L('role_name'));
        }

        if(isset($data['selectall']) && $data['selectall']>0){
            $data['priv'] = 'all';
        }else{
            if(!isset($data['priv']))
                $data['priv']= '';
            else
                $data['priv'] = implode(',', $data['priv']);
        }


        $Role = Loader::model('admin@Role');
        $rs = $Role->add($data);
        if(!$rs)
            $this->api_output('FAIL');
        $this->_admin_log($data['role_name']);
        $this->api_output('SUCC');
    }

    public function delete(){
        $id = $this->getParam('id');
        if($id<=1){
            $this->api_output('PARAM');
        }
        $Role = Loader::model('admin@Role');
        $data = array(
            'status'    => C_Status::DELETE,
            'id'        => $id,
        );
        $rs = $Role->where(array('id'=>$id))->save($data);
        if(!$rs)
            $this->api_output('FAIL');
        $this->_admin_log($data['id']);
        $this->api_output('SUCC');
    }
}