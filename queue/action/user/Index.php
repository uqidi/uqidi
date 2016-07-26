<?php
class IndexAction extends Action_Queue{
    protected function _init(){
        $this->setKey('order');
    }
    public function run_job($data){
        var_dump($this->getParam(), $data);
    }
}