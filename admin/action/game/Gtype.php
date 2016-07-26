<?php
class GtypeAction extends Action_Auth{
    protected $_map = array(
        'gtype_name'  => array('like', '%#%'),
    );

    public function index(){
        $this->_list('web@Game_Type', 'lottery/gtype_list');
    }

    public function show(){
        $id = $this->getParam('id');
        $Model = Loader::model('web@Game_Type');
        $where['id'] = $id;
        $data = $Model->where($where)->find();
        $this->_show($data);
    }

    public function add(){
        $data = $this->getPost();

        if(empty($data['gtype_name'])){
            $this->api_output('PARAM', null, L('gtype_name'));
        }
        if(empty($data['gtype_code'])){
            $this->api_output('PARAM', null, L('gtype_code'));
        }

        $Model = Loader::model('web@Game_Type');
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

            if(empty($data['gtype_name'])){
                $this->api_output('PARAM', null, L('gtype_name'));
            }
            if(empty($data['gtype_code'])){
                $this->api_output('PARAM', null, L('gtype_code'));
            }

            $Model = Loader::model('web@Game_Type');

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
            $Model = Loader::model('web@Game_Type');
            $where['id'] = $id;
            $data = $Model->where($where)->find();
            if($data){
                $this->api_output('SUCC', $data);
            }

            $this->api_output('FAIL');
        }
    }

    public function publish(){
        $Model = Loader::model('web@Game_Type');
        $data = $Model->rkey('id')->findAll();
        $file = 'Game_gtype';
        $rs = T_FileData::set($file, $data);

        T_Rsync::send_php($file);
        if(!$rs)
            $this->api_output('FAIL');
        $this->_admin_log($file);
        $this->api_output('SUCC');
    }

}