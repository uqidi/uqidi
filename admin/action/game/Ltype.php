<?php
class LtypeAction extends Action_Auth{
    protected $_map = array(
        'ltype_name'  => array('like', '%#%'),
    );


    public function index(){
        $this->_list('web@Lottery_Type', 'lottery/ltype_list');
    }

    protected function _assign_field($name){
        parent::_assign_field($name);
        $Form = Tool_Form::getInstance($this);
        $type_los = C_Game::$type_los;
        foreach($type_los as $k=>$v){
            $type_los[$k] = L('type_lo_'.$v['key']);
        }

        $type_lo_select     = $Form->select($type_los, 'type_lo', 'type_lo');

        $code_lens = array(
            3=>3,
            5=>5,
        );
        $code_len_select    = $Form->select($code_lens, 'code_len', 'code_len');

        $this->assign('type_lo_select',     $type_lo_select);
        $this->assign('code_len_select',    $code_len_select);
    }

    protected function _list_show(&$list){
        foreach($list as $k=>$v){
            $list[$k]['status']     = L(C_Status::$status[$v['status']]);
            $list[$k]['type_lo']    = L('type_lo_'.C_Game::$type_los[$v['type_lo']]['key']);
        }
    }

    public function show(){
        $id = $this->getParam('id');
        $Model = Loader::model('web@Lottery_Type');
        $where['id'] = $id;
        $data = $Model->where($where)->find();
        $data['type_lo'] = L('type_lo_'.C_Game::$type_los[$data['type_lo']]['key']);
        $this->_show($data);
    }

    public function add(){
        $data = $this->getPost();

        if(empty($data['ltype_name'])){
            $this->api_output('PARAM', null, L('ltype_name'));
        }

        if(empty($data['ltype_code'])){
            $this->api_output('PARAM', null, L('ltype_code'));
        }

        $Model = Loader::model('web@Lottery_Type');
        $rs = $Model->add($data);
        if(!$rs)
            $this->api_output('FAIL');
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
            if($id<1){
                $this->api_output('PARAM');
            }

            if(empty($data['ltype_name'])){
                $this->api_output('PARAM', null, L('ltype_name'));
            }
            if(empty($data['ltype_code'])){
                $this->api_output('PARAM', null, L('ltype_code'));
            }

            if(isset($data['type_lo']))
                unset($data['type_lo']);
            $Model = Loader::model('web@Lottery_Type');

            $rs = $Model->where(array('id'=>$id))->save($data);

            if(!$rs)
                $this->api_output('FAIL');
            $this->_admin_log($data);
            $this->api_output('SUCC');
        }else{
            $id = $this->getParam('id');
            if($id<1){
                $this->api_output('PARAM');
            }
            $Model = Loader::model('web@Lottery_Type');
            $where['id'] = $id;
            $data = $Model->where($where)->find();
            if($data){
                $this->api_output('SUCC', $data);
            }

            $this->api_output('FAIL');
        }
    }

    public function publish(){
        $Model = Loader::model('web@Lottery_Type');
        $data = $Model->field('id,ltype_name,type_lo,ltype_code,code_len,sort_order,status')->rkey('id')->order('sort_order asc')->findAll();
        $file = 'Game_ltype';
        $rs = T_FileData::set($file, $data);

        T_Rsync::send_php($file);
        if(!$rs)
            $this->api_output('FAIL');
        $this->_admin_log($file);
        $this->api_output('SUCC');
    }
}