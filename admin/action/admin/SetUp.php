<?php
class SetUpAction extends Action_Auth{
    public function basic(){
        if(!$this->getParam('do')){
            return $this->_show_view($this->_action);
        }
        $data = $this->getPost('data');
        $rs = $this->_save_data($this->_action, $data);
        if(!$rs)
            $this->api_output('FAIL');
        $this->api_output('SUCC', $data);
    }
    public function website(){
        if(!$this->getParam('do')){
            return $this->_show_view($this->_action);
        }
        $data = $this->getPost('data');
        $rs = $this->_save_data($this->_action, $data);
        if(!$rs)
            $this->api_output('FAIL');
        $this->api_output('SUCC', $data);
    }
    public function mail(){
        if(!$this->getParam('do')){
            return $this->_show_view($this->_action);
        }
        $data = $this->getPost('data');
        $rs = $this->_save_data($this->_action, $data);
        if(!$rs)
            $this->api_output('FAIL');
        $this->api_output('SUCC', $data);
    }

    public function basic_publish(){
        $this->_publish(substr($this->_action, 0, strpos($this->_action, '_')));
    }

    public function website_publish(){
        $this->_publish(substr($this->_action, 0, strpos($this->_action, '_')));
    }

    public function mail_publish(){
        $this->_publish(substr($this->_action, 0, strpos($this->_action, '_')));
    }

    public function mailtpl_publish(){
        $code = $this->getParam('code');
        $Mailtpl = Loader::model('admin@MailTpl');
        $data = $Mailtpl->where(array('code'=>$code))->find();
        if(empty($data))
            $this->api_output('PARAM');

        $file = 'MailTpl_'.$code;
        $rs = T_FileData::set($file, $data);

        T_Rsync::send_php($file);
        if(!$rs)
            $this->api_output('FAIL');
        $this->_admin_log($code);
        $this->api_output('SUCC');
    }

    public function mail_test_send(){
        $data = $this->getPost('data');
        $mail_to = $this->getPost('mail_to');
        $Mail = P_Mail::instance($data);
        $rs = $Mail->send($mail_to, L('mail_test_send'), 'This is a test mail');
        if(false == $rs){
            $this->api_output('FAIL', null, $Mail->getError(true));
        }
        $this->api_output('SUCC');
    }
    public function mailtpl(){
        if(!$this->getParam('do')){
            $Mailtpl = Loader::model('admin@MailTpl');
            $list = $Mailtpl->findAll();
            $this->assign('list', $list);
            return $this->display('setup/mailtpl');
        }else{
            $code = $this->getPost('code');
            if(empty($code)){
                $this->api_output('PARAM');
            }
            $data = $this->getPost('data');
            if(!isset($data[$code]) || empty($data[$code])){
                $this->api_output('PARAM');
            }
            $data = $data[$code];
            $Mailtpl = Loader::model('admin@MailTpl');
            $where = array(
                'code'  => $code,
            );
            $mailtpl = $Mailtpl->where($where)->find();
            if(false === $mailtpl){
                $this->api_output('FAIL');
            }
            if(empty($mailtpl)){
                $data['code'] = $code;
                $rs = $Mailtpl->add($data);
            }else{
                $rs = $Mailtpl->where($where)->save($data);
            }
            if(false == $rs)
                return false;

            $this->_admin_log($code);

            $this->api_output('SUCC');
        }

    }

    private function _show_view($code){
        $Config = Loader::model('admin@Config');
        $data = $Config->where(array('code'=>$code))->getField('data');
        $data = empty($data) ? array() : json_decode($data, true);
        $this->assign('info', $data);
        $this->assign('code', $code);
        return $this->display('setup/'.$code);
    }

    private function _save_data($code, $data){
        $data = json_encode($data);
        $Config = Loader::model('admin@Config');
        $add_data['code'] = $code;
        $add_data['data'] = $data;
        $rs = $Config->add($add_data, array(), true);
        if(false == $rs)
            return false;
        $this->_admin_log($code);
        return true;
    }

    private function _publish($code){
        $Config = Loader::model('admin@Config');
        $data = $Config->where(array('code'=>$code))->getField('data');
        $data = empty($data) ? array() : json_decode($data, true);
        $file = 'Setup_'.$code;
        $rs = T_FileData::set($file, $data);

        T_Rsync::send_php($file);
        if(!$rs)
            $this->api_output('FAIL');
        $this->_admin_log($code);
        $this->api_output('SUCC', $data);
    }
}