<?php
class Tool_Form{
    private $_Action = null;
    private $_elements = array();
    private static $_inst = null;

    static public function getInstance($Action) {
        if(self::$_inst){
            return self::$_inst;
        }
        self::$_inst = new self($Action);
        return self::$_inst;
    }
    private function __construct($Action){
        $this->_Action = $Action;
    }
	public function select($list, $id='select_id',$name='select_name',$selected=''){
        $data['options'] = $list;
        $data['name'] = $name;
        $data['id']   = $id;
        $data['selected'] = $selected;
        if(!isset($this->_elements['select']) || !isset($this->_elements['select']['js_reload']) || !$this->_elements['select']['js_reload']){
            $this->_elements['select']['js_reload'] = true;
            $data['js_reload'] = true;
        }else{
            $data['js_reload'] = false;
        }

        $this->_Action->assign('form_select', $data);
        $str = $this->_Action->render('form/select');
        return str_replace(array("\r\n", "\r", "\n"), '', $str);
	}

    public function radio($list, $id='radio_id',$name='radio_name',$checked=''){
        $data['options'] = $list;
        $data['name'] = $name;
        $data['id']   = $id;
        $data['checked'] = $checked;
        $this->_Action->assign('form_radio', $data);
        $str = $this->_Action->render('form/radio');
        return str_replace(array("\r\n", "\r", "\n"), '', $str);
    }

    public function checkbox($list, $id='checkbox_id',$name='checkbox_name',$checked=array()){
        $data['options'] = $list;
        $data['name'] = $name;
        $data['id']   = $id;
        $data['checked'] = $checked;
        $this->_Action->assign('form_checkbox', $data);
        $str = $this->_Action->render('form/checkbox');
        return str_replace(array("\r\n", "\r", "\n"), '', $str);
    }
}