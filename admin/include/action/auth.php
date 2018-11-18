<?php
class Action_Auth extends Action{
    protected $_admin = null;
    protected $_privs = null;

    public function __construct(){
        parent::__construct();
        if(isset($this->_uActions) && in_array($this->_action, $this->_uActions))
            return true;
        $route_type = $this->getParam('rt');
        switch($route_type){
            case 'api':
                $this->_api_init();
                break;
            default:
                $this->_page_init();
        }
        return true;
    }


    public function setDisplayData(){
        $this->assign('LANG',       L());
        parent::setDisplayData();
    }

    protected function _page_init(){
        $login = $this->check_login();
        if(!$login){
            redirect(U('login', 'Login', 'admin'));
        }

        $permission = $this->check_permission();

        if(!$permission){
            $this->jump(L('_ERR_NOAUTH_'), 0);
        }
        $this->assign('admin', $this->_admin);
        $this->assign('menus', $this->get_menu($this->_admin['roleid']));
        $rcode_config = Loader::loadConfig('rcode');
        $this->setScopeVar('$status', $rcode_config);
    }

    protected function _api_init(){
        $login = $this->check_login();
        if(!$login){
            $this->api_output('NOLOGIN');
        }

        $permission = $this->check_permission();

        if(!$permission){
            $this->api_output('NOAUTH');
        }
    }

    protected function check_login(){
        $sdata = P_Session::getInstance()->get();
        if(!isset($sdata['adminid'])){
            return false;
        }
        $this->_admin = $sdata;
        return true;
    }

    protected function check_permission(){
        if(false !== strpos($this->_action, 'public_')){
            return true;
        }
        $Role = Loader::model('admin@Role');
        $where['id'] = $this->_admin['roleid'];
        $role = $Role->where($where)->find();
        $this->_privs = $role['priv'];
        if('Main' !== $this->_controller){
            if('all' === $this->_privs)
                return true;
            if(empty($this->_privs)){
                return false;
            }
            $this->_privs = explode(',',$this->_privs);
            $menuid = $this->getQuery('menuid');
            if(empty($menuid)){
                $Menu = Loader::model('admin@Menu');
                $menuid = $Menu->where(
                    array(
                        'module' =>$this->_module,
                        'controller'    => $this->_controller,
                        'action'        => $this->_action)
                )->getField('id');
            }
            return in_array($menuid, $this->_privs);
        }

        'all' !== $this->_privs ? $this->_privs = explode(',',$this->_privs) : '';
        return true;
    }

    protected function get_menu(){
        $list = $this->menu_list();

        $menuid = $this->getQuery('menuid');
        $menus = '';
        foreach($list as $v){
            if($this->_privs !== 'all' && !in_array($v['id'], $this->_privs)){
                continue;
            }
            if($menuid == $v['id']){
                $menus .= '<li class="active">';
                $this->assign('cur_menu', $v);
            }else{
                if(isset($v['sub']) && isset($v['sub'][$menuid])){
                    $menus .= '<li class="active opened">';
                    $this->assign('top_menu', $v);
                }else{
                    $menus .= '<li>';
                }
            }

            $menus .= '<a href="'.U($v['action'], $v['controller'], $v['module'], array('menuid'=>$v['id'])).'">';
            if(!empty($v['icon']))
                $menus .= '<i class="'.$v['icon'].'"></i>';
            $menus .= '<span class="title">'.$v['name'].'</span>';
            $menus .= '</a>';
            if(isset($v['sub']) && is_array($v['sub'])){
                $menus .= '<ul>';
                foreach($v['sub'] as $sv){
                    if($this->_privs !== 'all' && !in_array($sv['id'], $this->_privs)){
                        continue;
                    }
                    if($menuid == $sv['id']){
                        $menus .= '<li class="active">';
                        $this->assign('cur_menu', $sv);
                    }else{
                        $menus .= '<li>';
                    }

                    $menus .= '<a href="'.U($sv['action'], $sv['controller'], $sv['module'], array('menuid'=>$sv['id'])).'">';
                    if(!empty($v['icon']))
                        $menus .= '<i class="'.$sv['icon'].'"></i>';
                    $menus .= '<span class="title">'.$sv['name'].'</span>';
                    $menus .= '</a>';
                    $menus .= '</li>';
                }
                $menus .= '</ul>';
            }
            $menus .= '</li>';
        }
        return $menus;
    }

    protected function menu_list($is_reload=false){
        if(!$is_reload){
            $list = T_FileData::get('Menu_list');
            if($list){
                return $list;
            }
        }

        $where = array('pid'=>0, 'display'=>1);
        $Menu = Loader::model('admin@Menu');
        $list = $Menu->where($where)->rkey('id')->order('listorder asc')->findAll();

        foreach($list as $k=>$v){
            $where = array('pid'=>$v['id'], 'display'=>1);

            $subs = $Menu->where($where)->rkey('id')->order('listorder asc')->findAll();
            if(!empty($subs)){
                $list[$k]['sub'] = $subs;

                foreach($subs as $sk=>$sv){
                    $where = array('pid'=>$sv['id'], 'display'=>1);
                    $actions = $Menu->where($where)->rkey('id')->order('listorder asc')->findAll();
                    if(!empty($actions))
                        $list[$k]['sub'][$sk]['sub'] = $actions;
                }
            }
        }

        T_FileData::set('Menu_list', $list);
        return $list;
    }

    protected function _admin_log($info=array()){
        if(is_array($info))
            $info = implode(' | ', $info);
        $data = array(
            'adminid'       => $this->_admin['adminid'],
            'username'      => $this->_admin['username'],
            'module'        => $this->_module,
            'controller'    => $this->_controller,
            'action'        => $this->_action,
            'log_info'      => $info,
            'cip'           => T_Ip::get_real_ip(),
            'create_time'   => date('Y-m-d H:i:s', NOW_TIME),
        );

        $Log = Loader::model('admin@Log');
        return $Log->add($data);
    }

    protected function _show($data){
        $show_data = array();
        foreach($data as $k=>$v){
            if($k == 'status')
                $show_data[L($k)] = L(C_Status::$status[$data['status']]);
            elseif(in_array($k, array('utime', 'ctime'))){
                $show_data[L($k)] = date('Y-m-d H:i:s', $v);
            }else{
                $show_data[L($k)] = $v;
            }
        }
        $this->api_output('SUCC', $show_data);
    }

    protected function _assign_field($name){
        $Form = Tool_Form::getInstance($this);
        $status_select = C_Status::$status;
        foreach($status_select as $k=>$v){
            $status_select[$k] = L($v);
        }
        $status_select = $Form->select($status_select, 'status', 'status');
        $this->assign('status_select', $status_select);
    }
    protected function _list($model_name, $tpl_name, $key='id', $field="*", $where=array()){
        $page = (int)$this->getQuery('page');
        $pre = (int)$this->getQuery('pre');
        $sort   = urldecode($this->getQuery('sort'));
        empty($page)    ? $page = 1 : '';
        empty($pre)     ? $pre = 20 : '';
        empty($sort)    ? $sort= $key.' desc' : '';

        $search = $this->getQuery('search');

        if($this->getParam('do')){
            $Model = Loader::model($model_name);
            $data = array(
                'page'  => $page,
                'pre'   => $pre,
            );

            if(is_array($search)){
                foreach($search as $k=>$v){
                    if(isset($this->_map) && isset($this->_map[$k]) && !empty($v)){
                        $search[$k] = $this->_search_map($v, $this->_map[$k]);
                    }else{
                        unset($search[$k]);
                    }
                }
            }

            if(isset($search['status']) && !empty($search['status'])){
                $search['status'] = $search['status'];
            }else{
                $search['status'] = array('lt', C_Status::DELETE);
            }
            $search = array_merge($search, $where);
            $data['total'] = (int)$Model->where($search)->count();

            if($data['total']>0){
                $data['list'] = $Model->field($field)->where($search)->page($page, $pre)->order($sort)->findAll();
                if(method_exists($this, '_list_show')){
                    $this->_list_show($data['list']);
                }else{
                    foreach($data['list'] as $k=>$v){
                        $data['list'][$k]['status'] = L(C_Status::$status[$v['status']]);
                    }
                }

                $data['total_page'] = ceil($data['total']/$pre);
            }else{
                $data['list'] = array();
                $data['total_page'] = 0;
            }

            $this->api_output('SUCC', $data);
        }else{
            $data = array(
                'page'  => $page,
                'pre'   => $pre,
                'sort'  => $sort,
            );
            empty($search) ? $data['search'] = $search : '';
            $this->_assign_field($model_name);
            $this->assign('params', $data);
            $this->display($tpl_name);
        }
    }

    protected function _search_map($data, $map){
        if(empty($map))
            return $data;
        if(isset($map[0]) && !empty($map[0])){
            if($map[0] == 'like'){
                if(isset($map[1])){
                    $where = str_replace('#', $data,$map[1]);
                }else{
                    $where = $data.'%';
                }
                return array('like', $where);
            }else{
                return array($map[0], $data);
            }
        }

        return $data;
    }
}