<?php
class Lib_User{
    public static function getUserByUid($uid){
        $User = Loader::model('User');
        $user = $User->where(array('uid'=>$uid))->find();
        return $user;
    }
}

