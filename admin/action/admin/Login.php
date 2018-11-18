<?php
class LoginAction extends Action{
    public function login(){
        $Session = P_Session::getInstance();
        $data = $Session->get();
        if(isset($data['adminid']) && $data['adminid']>0){
            redirect(U('index', 'Main'));
        }

        if(isset($_COOKIE['remember']) && isset($_COOKIE['remember'][0])){
            $remember = json_decode(base64_decode(urldecode($_COOKIE['remember'])), true);
            if(isset($remember['adminid']) && $remember['expires']>NOW_TIME){
                $Admin = Loader::model('admin@Admin');
                $admin = $Admin->where(array('id'=>$remember['adminid']))->find();
                if(!empty($admin) && C_Status::YES == $admin['status'] && $remember['password'] === $admin['password']){
                    $data = array(
                        'adminid'   => $admin['id'],
                        'roleid'    => $admin['roleid'],
                        'username'  => $admin['username'],
                    );
                    $Session = P_Session::getInstance();
                    $Session->set($data);
                    $Session->save();
                    $save_data = array(
                        'num'       => array('exp','`num`+1'),
                        'loginip'   => T_Ip::get_real_ip(),
                        'logintime' => date('Y-m-d H:i:s', NOW_TIME),
                    );
                    $Admin->where(array('id'=>$admin['id']))->save($save_data);
                    $this->_admin_log(array('login', 'auto'), $data);
                    redirect(U('index', 'Main'));
                }
            }

        }

        $rcode_config = Loader::loadConfig('rcode');
        $this->setScopeVar('$status', $rcode_config);
        $this->setPageTitle(L('login'));
        $this->display('admin/login');
    }

    public function do_login(){
        $post_data = $this->getPost();
        $rs = $this->filter($post_data);
        if(false === $rs)
            $this->api_output($this->getError());

        $user_name = $post_data['username'];
        $password  = $post_data['password'];

        $Admin = Loader::model('admin@Admin');
        $admin = $Admin->where(array('username'=>$user_name))->find();
        if(empty($admin))
            $this->api_output('NOEXISTS');

        if(C_Status::YES != $admin['status']){
            $this->api_output('DISABLED');
        }

        if(!P_Crypt_Passwd::check_password($password, $admin['password'])){
            $this->api_output('PASSWORD');
        }
        $data = array(
            'adminid'   => $admin['id'],
            'roleid'    => $admin['roleid'],
            'username'  => $admin['username'],
        );
        $Session = P_Session::getInstance();
        $Session->set($data);
        $Session->save();
        $save_data = array(
            'num'       => array('exp','`num`+1'),
            'loginip'   => T_Ip::get_real_ip(),
            'logintime' => date('Y-m-d H:i:s', NOW_TIME),
        );
        $Admin->where(array('id'=>$admin['id']))->save($save_data);
        $this->_admin_log('login', $data);

        if(isset($post_data['remember_me']) && $post_data['remember_me']>0){
            $expires = NOW_TIME+2592000;
            $cookie_data = array(
                'adminid'   => $data['adminid'],
                'password'  => $admin['password'],
                'expires'   => $expires
            );
            $cookie_data = json_encode($cookie_data);
            $cookie_data = urlencode(base64_encode($cookie_data));
            setcookie('remember', $cookie_data, $expires, '/');
        }

        $this->api_output('SUCC');
    }
    public function logout(){
        $Session = P_Session::getInstance();
        setcookie('remember', '', NOW_TIME-1, '/');
        $this->_admin_log('logout',$Session->get());
        $Session->destroy();
        redirect(U('login'));
    }

    protected function _admin_log($info=array(), $admin=array()){
        if(is_array($info))
            $info = implode(' | ', $info);
        if(!isset($admin['adminid']) || empty($admin['adminid']))
            return false;
        $data = array(
            'adminid'       => $admin['adminid'],
            'username'      => isset($admin['username']) ? $admin['username'] : '',
            'module'        => $this->_module,
            'controller'    => $this->_controller,
            'action'        => $this->_action,
            'log_info'      => $info,
            'cip'           => T_Ip::get_real_ip(),
            'create_time'      => date('Y-m-d H:i:s', NOW_TIME),
        );

        $Log = Loader::model('admin@Log');
        return $Log->add($data);
    }

}