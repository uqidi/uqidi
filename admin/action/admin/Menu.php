<?php
class MenuAction extends Action_Auth{
    protected function _assign_field($name){
        $Form = Tool_Form::getInstance($this);
        $menu_list = T_FileData::get('Menu_list');
        $form_select[0] = L('top');
        foreach($menu_list as $v){
            $form_select[$v['id']] = $v['name'];
            if(isset($v['sub'])){
                foreach($v['sub'] as $sv){
                    $form_select[$sv['id']] = '|—'.$sv['name'];
                }
            }
        }

        $form_select = $Form->select($form_select, 'pid', 'pid');
        $this->assign('form_select', $form_select);
    }
    public function index(){
        $list = T_FileData::get('Menu_list');
        $this->_assign_field('admin@Menu');
        $this->assign('list', $list);
        $this->display('admin/menu_list');
    }
    public function show(){
        $id = $this->getParam('id');
        $Menu = Loader::model('admin@Menu');
        $where['id'] = $id;
        $data = $Menu->where($where)->find();

        $data['display'] = (int)$data['display'];
        $data['display'] = L(C_Status::$status[$data['display']]);
        $data['icon'] = !empty($data['icon']) ? '<i class="'.$data['icon'].'"></i>' : '';
        $show_data = array();
        foreach($data as $k=>$v){
            $show_data[L($k)] = $v;
        }
        $this->api_output('SUCC', $show_data);
    }

    public function add(){
        $data = $this->getPost();
        $Menu = Loader::model('admin@Menu');
        $rs = $Menu->add($data);
        if(!$rs)
            $this->api_output('FAIL');
        $this->menu_list(true);
        $this->_admin_log($data);
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
            if($id<=0){
                $this->api_output('PARAM');
            }

            $Menu = Loader::model('admin@Menu');

            $rs = $Menu->where(array('id'=>$id))->save($data);

            if(!$rs)
                $this->api_output('FAIL');

            $this->menu_list(true);
            $this->_admin_log($data);
            $this->api_output('SUCC');
        }else{
            $id = $this->getParam('id');
            if(!$id){
                $this->api_output('PARAM');
            }
            $Menu = Loader::model('admin@Menu');
            $where['id'] = $id;
            $menu = $Menu->where($where)->find();
            if($menu)
                $this->api_output('SUCC', $menu);
            $this->api_output('FAIL');
        }

    }

    public function sort(){
        $list = $this->getPost('list');
        $Menu = Loader::model('admin@Menu');
        foreach($list as $mk=>$module){
            $Menu->where(array('id'=>$module['id']))->save(array('pid'=>0,'listorder'=>$mk));
            if(isset($module['children'])){
                foreach($module['children'] as $ck=>$controller){
                    $Menu->where(array('id'=>$controller['id']))->save(array('pid'=>$module['id'],'listorder'=>$ck));
                    if(isset($controller['children'])){
                        foreach($controller['children'] as $ak=>$action){
                            $Menu->where(array('id'=>$action['id']))->save(array('pid'=>$controller['id'],'listorder'=>$ak));
                        }

                    }
                }
            }
        }

        $this->menu_list(true);
        $this->_admin_log('sort');
        $this->api_output('SUCC');
    }

    public function delete(){
        $id = $this->getParam('id');

        if(!$id){
            $this->api_output('PARAM');
        }
        $Menu = Loader::model('admin@Menu');
        $where['id'] = $id;
        $rs = $Menu->where($where)->delete();
        if(!$rs)
            $this->api_output('FAIL');
        $this->menu_list(true);
        $this->_admin_log($id);
        $this->api_output('SUCC');
    }
}