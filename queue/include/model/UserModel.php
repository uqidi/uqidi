<?php
class UserModel extends M_User{
    public function get_user($uid){
        return $this->where(array('uid'=>$uid))->find();
    }
}

