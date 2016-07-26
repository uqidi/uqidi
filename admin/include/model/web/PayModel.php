<?php
class PayModel extends  K_Model{
    protected function _before_write(&$data){
        parent::_before_write($data);
        if(isset($data['ext']) && is_array($data['ext'])){
            $data['ext'] = json_encode($data['ext']);
        }
    }

    protected function _after_select(&$resultSet,$options){
        $data = reset($resultSet);
        if(isset($data['ext']) ){
            foreach($resultSet as $k=>$result){
                if(isset($result['ext']) && !empty($result['ext'])){
                    $resultSet[$k]['ext'] = json_decode($result['ext'], true);
                }
            }
        }
    }

    protected function _after_find(&$result,$options){
        if(isset($result['ext']) && !empty($result['ext'])){
            $result['ext'] = json_decode($result['ext'], true);
        }
    }
}
