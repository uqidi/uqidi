<?php
class L_User{
    /**
     * 用户uid获取用户
     * @param $uid
     * @param string $field
     * @return mixed
     */
    public static function getUserByUid($uid, $field='*'){
        $User = Loader::model('User');
        $user = $User->field($field)->where(array('uid'=>$uid))->find();
        return $user;
    }

    /**
     * 帐号获取用户
     * @param $username
     * @param string $field
     * @return mixed
     */
    public static function getUserByUsername($username, $field='*'){
        $User = Loader::model('User');
        $user = $User->field($field)->where(array('username'=>$username))->find();
        return $user;
    }

    /**
     * 设置用户缓存
     * @param $uid
     * @param $data
     * @return mixed
     */
    public static function setCache($uid, $data){
        $UserCache = Loader::cache(C_Rds::SERVER_USER, K_Cache::CLASS_RDS, $uid);
        if(isset($data['parents']) && is_array($data['parents'])){
            $data['parents'] = implode(',', $data['parents']);
        }
        if(isset($data['games']) && is_array($data['games'])){
            $data['games'] = json_encode($data['games']);
        }
        return $UserCache->hMset(C_Rds::PRE_USER.$uid, $data);
    }

    /**
     * 获取用户缓存
     * @param $uid
     * @param array $data
     * @return mixed
     */
    public static function getCache($uid, $data=array()){
        $UserCache = Loader::cache(C_Rds::SERVER_USER, K_Cache::CLASS_RDS, $uid);
        $key = C_Rds::PRE_USER.$uid;
        if(empty($data)){
            $result = $UserCache->hGetAll($key);
            if(!empty($result) && isset($result['parents'])){
                $result['parents'] = empty($result['parents']) ? array() : explode(',', $result['parents']);
            }
            if(!empty($result) && isset($result['games'])){
                $result['games'] = empty($result['games']) ? array() : json_decode($result['games'], true);
            }
        }else if(is_array($data)){
            $result = $UserCache->hMGet($key, $data);
            if(!empty($result) && isset($result['parents'])){
                $result['parents'] = empty($result['parents']) ? array() : explode(',', $result['parents']);
            }
            if(!empty($result) && isset($result['games'])){
                $result['games'] = empty($result['games']) ? array() : json_decode($result['games'], true);
            }
        }else{
            $result = $UserCache->hGet($key, $data);

            if(!empty($result)){
                if($key == 'parents'){
                    $result = explode(',', $result);
                }elseif($key == 'games'){
                    $result = json_decode($result, true);
                }
            }
        }

        return $result;
    }

    /**
     * 删除用户缓存
     * @param $uid
     * @param array $data
     * @return mixed
     */
    public static function delCache($uid, $data=array()){
        $UserCache = Loader::cache(C_Rds::SERVER_USER, K_Cache::CLASS_RDS, $uid);
        $key = C_Rds::PRE_USER.$uid;

        if(empty($data)){
            return $UserCache->delete($key);
        }

        if(!is_array($data))
            return $UserCache->hDel($key, $data);

        array_unshift($data, $key);
        return call_user_func_array(array($UserCache, 'hDel'), $data);
    }

}

