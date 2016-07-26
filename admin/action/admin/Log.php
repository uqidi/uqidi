<?php
class LogAction extends Action_Auth{
    public function index(){
        $page = (int)$this->getQuery('page');
        $pre = (int)$this->getQuery('pre');
        $sort   = urldecode($this->getQuery('sort'));
        empty($page)    ? $page = 1 : '';
        empty($pre)     ? $pre = 20 : '';
        empty($sort)    ? $sort= 'id desc' : '';



        $search = $this->getQuery('search');

        if($this->getParam('do')){
            $Log = Loader::model('admin@Log');
            $data = array(
                'page'  => $page,
                'pre'   => $pre,
            );

            $map = array();

            if(isset($search['username']) && !empty($search['username'])){
                $map['username'] = array('like', $search['username'].'%');
            }

            if(isset($search['module']) && !empty($search['module'])){
                $map['module'] = $search['module'];
            }

            if(isset($search['controller']) && !empty($search['controller'])){
                $map['controller'] = $search['controller'];
            }

            if(isset($search['action']) && !empty($search['action'])){
                $map['action'] = $search['action'];
            }

            if(isset($search['log_info']) && !empty($search['log_info'])){
                $map['log_info'] = array('like', '%'.$search['log_info'].'%');
            }

            $data['total'] = (int)$Log->where($map)->count();

            if($data['total']>0){
                $data['list'] = $Log->where($map)->page($page, $pre)->order($sort)->findAll();
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
            $this->assign('params', $data);
            $this->display('admin/log_list');
        }
    }
}