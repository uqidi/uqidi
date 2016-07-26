<?php
class LotteryAction extends Action_Auth{
    protected $_map = array(
        'ltype_name'  => array('like', '%#%'),
        'gtype_name'  => array('like', '%#%'),
    );

    public function index(){
        $sort   = urldecode($this->getQuery('sort'));
        empty($sort)    ? $sort= 'sort_order asc' : '';

        $search = $this->getQuery('search');

        $ltypes = T_FileData::get('Game_ltype');
        $gtypes = T_FileData::get('Game_gtype');

        if($this->getParam('do')){
            $Model = Loader::model('web@Lottery_Game');

            if(isset($search['status']) && !empty($search['status'])){
                $search['status'] = $search['status'];
            }else{
                $search['status'] = array('lt', C_Status::DELETE);
            }

            $data['list'] = $Model->where($search)->order($sort)->findAll();
            foreach($data['list'] as $k=>$v){
                $data['list'][$k]['status'] = L(C_Status::$status[$v['status']]);
                $data['list'][$k]['ltype_name'] = $ltypes[$v['ltype_id']]['ltype_name'];
                $data['list'][$k]['code_len'] = $ltypes[$v['ltype_id']]['code_len'];
                if(empty($v['bonus'])){
                    $data['list'][$k]['bonus'] = $gtypes[$v['gtype_id']]['bonus'];
                }
                $data['list'][$k]['code_pos'] = C_Game::code_pos_convert($v['code_pos'], $ltypes[$v['ltype_id']]['code_len'] );
            }

            $this->api_output('SUCC', $data);
        }else{
            $this->_assign_field('web@Lottery_Game');

            $Form = Tool_Form::getInstance($this);


            $ltype_select = array();
            foreach($ltypes as $k=>$v){
                $ltype_select[$v['id']] = $v['ltype_name'];
                $ltypes[$k]['type_lo'] = L('type_lo_'.C_Game::$type_los[$v['type_lo']]['key']);
                $ltypes[$k]['code_pos_checkbox'] = $Form->checkbox(C_Game::code_pos_list($v['code_len']), 'code_pos', 'code_pos');
            }


            if(empty($search['ltype'])){
                $ltype =  reset($ltypes);
                $data['search']['ltype_id'] = $ltype['id'];
            }else{
                $ltype = $ltypes[$search['ltype']];
            }

            $ltype_select = $Form->select($ltype_select, 'ltype_id', 'ltype_id');

            $gtype_select = array();
            foreach($gtypes as $v){
                $gtype_select[$v['id']] = $v['gtype_name'];
            }
            $gtype_select = $Form->select($gtype_select, 'gtype_id', 'gtype_id');

            $data['sort']       = $sort;
            $configs['ltype']   = $ltype;



            $this->assign('ltype_select',       $ltype_select);
            $this->assign('gtype_select',       $gtype_select);

            $this->assign('ltypes',         $ltypes);
            $this->assign('params',         $data);
            $this->assign('configs',        $configs);
            $this->display('lottery/list');
        }
    }

    public function add(){
        $data = $this->getPost();
        if(empty($data['ltype_id'])){
            $this->api_output('PARAM', null, L('ltype_name'));
        }

        if(empty($data['gtype_id'])){
            $this->api_output('PARAM', null, L('gtype_name'));
        }


        if(isset($data['code_pos'])){
            $data['code_pos'] = array_sum($data['code_pos']);
        }

        $Model = Loader::model('web@Lottery_Game');
        $rs = $Model->add($data);
        if(!$rs)
            $this->api_output('FAIL');
        $this->_admin_log($data);
        $this->api_output('SUCC');
    }

    public function show(){
        $id = $this->getParam('id');
        if($id<1){
            $this->api_output('PARAM');
        }
        $Model = Loader::model('web@Lottery_Game');
        $where['id'] = $id;
        $data = $Model->where($where)->find();
        if(empty($data)){
            $this->api_output('FAIL');
        }
        $ltypes = T_FileData::get('Game_ltype');
        $gtypes = T_FileData::get('Game_gtype');
        $data['ltype_name'] = $ltypes[$data['ltype_id']]['ltype_name'];
        $data['gtype_name'] = $gtypes[$data['gtype_id']]['gtype_name'];

        if(empty($data['bonus'])){
            $data['bonus'] = $gtypes[$data['gtype_id']]['bonus'];
        }
        unset($data['ltype_id']);
        unset($data['gtype_id']);
        $this->_show($data);
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

            if(empty($data['gtype_id'])){
                $this->api_output('PARAM', null, L('gtype_name'));
            }

            $Model = Loader::model('web@Lottery_Game');

            if(isset($data['code_pos'])){
                $data['code_pos'] = array_sum($data['code_pos']);
            }

            $rs = $Model->where(array('id'=>$id))->save($data);

            if(!$rs)
                $this->api_output('FAIL');
            $this->_admin_log($data);
            $this->api_output('SUCC', $data);
        }else{
            $id = $this->getParam('id');
            if($id<1){
                $this->api_output('PARAM');
            }
            $Model = Loader::model('web@Lottery_Game');
            $where['id'] = $id;
            $data = $Model->where($where)->find();
            if($data){
                $ltypes = T_FileData::get('Game_ltype');
                $data['ltype_name'] = $ltypes[$data['ltype_id']]['ltype_name'];
                $this->api_output('SUCC', $data);
            }

            $this->api_output('FAIL');
        }
    }

    public function publish(){
        $Model = Loader::model('web@Lottery_Game');
        $list = $Model->rkey('id')->order('sort_order asc')->findAll();
        $file = 'Game_Type';
        $publish_data1 = $publish_data2 = array();
        $ltypes = T_FileData::get('Game_ltype');
        $gtypes = T_FileData::get('Game_gtype');
        foreach($list as $k=>$v){
            $v['ltype_code'] = $ltypes[$v['ltype_id']]['ltype_code'];
            $v['ltype_name'] = $ltypes[$v['ltype_id']]['ltype_name'];

            if(empty($v['bonus'])){
                $v['bonus'] = $gtypes[$v['gtype_id']]['bonus'];
            }

            $v['bonus_step'] = $gtypes[$v['gtype_id']]['bonus_step'];

            if(strpos($v['bonus'], ',')){
                $v['bonus']         = explode(',', $v['bonus']);
                $v['bonus_step']    = explode(',', $v['bonus_step']);
            }

            $v['gtype_code'] = $gtypes[$v['gtype_id']]['gtype_code'];
            $v['gtype_name'] = $gtypes[$v['gtype_id']]['gtype_name'];

            $publish_data1[$v['ltype_id']][$k] = $v;
            $key = $v['gtype_code'].'_'.$v['code_pos'];
            $publish_data2[$v['ltype_code']][$key] = $v;
        }

        foreach($publish_data1 as $k=>$v){
            $pfile = $file.'_'.$k;
            $rs = T_FileData::set($pfile, $v);
            T_Rsync::send_php($pfile);
            if(!$rs)
                $this->api_output('FAIL');
        }
        foreach($publish_data2 as $k=>$v){
            $pfile = $file.'_'.$k;
            $rs = T_FileData::set($pfile, $v);
            T_Rsync::send_php($pfile);
            if(!$rs)
                $this->api_output('FAIL');
        }

        $this->_admin_log($file);
        $this->api_output('SUCC');
    }

}