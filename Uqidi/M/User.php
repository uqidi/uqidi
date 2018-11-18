<?php
class M_User extends  K_Model{
    protected function _before_write(&$data){
        if(isset($data['parents']) && is_array($data['parents'])){
            $data['parents'] = implode(',', $data['parents']);
        }
    }

    protected function _after_select(&$resultSet,$options){
        $data = reset($resultSet);
        if(isset($data['parents']) ){
            foreach($resultSet as $k=>$result){
                if(isset($result['parents']) && !empty($result['parents'])){
                    $resultSet[$k]['parents'] = explode(',', $result['parents']);
                }
            }
        }
    }

    protected function _after_find(&$result,$options){
        if(isset($result['parents']) && !empty($result['parents'])){
            $result['parents'] = explode(',', $result['parents']);
        }
    }
}

