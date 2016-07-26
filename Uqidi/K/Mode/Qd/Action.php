<?php
/**
 * @fileoverview:   Action
 * @author:         Uqidi
 * @date:           2015-12-04
 * @copyright:      Uqidi
 */

class Action{

    protected $_viewer  = null;
    protected $_request = null;
    protected $_error = array();

    protected $_action;
    protected $_controller;
    protected $_module;
    protected $_actionid;


    private $_title         = '';
    private $_data          = array();
    private $_keyword       = array();      /* 页面关键字 */
    private $_description   = array();      /* 页面关键字 */
    private $_nav           = array();      /* 导航信息数组 */
    private $_scope         = array(
        '$pName'    => 'UQIDI_ADMIN',
        '$pageId'   => '',
    );

    public function __construct($plugins=null){
        $this->_action      = __ACTION_NAME__;
        $this->_controller  = __CONTROLLER_NAME__;
        $this->_module      = __MODULE_NAME__;
        $this->_actionid    = $this->_module.'-'.$this->_controller.'-'.$this->_action;
        $this->setPageId($this->_actionid);
    }

    /**
     * 设置data
     * @param $name
     * @param string $value
     * @return bool
     */
    public function setData($name, $value=''){
        if(!is_array($name)){
            $this->_data[$name] = $value;
            return true;
        }
        foreach($name as $k=>$v){
            $this->_data[$k] = $v;
        }
        return true;
    }

    /**
     * 获取data
     * @param string $name
     * @return array
     */
    public function getData($name=''){
        return empty($name) ? $this->_data : $this->_data[$name];
    }

    /**
     * 设置页面ID
     * @param string $pageid
     * @return void
     */
    public function setPageId($pageid){
        $this->_scope['$pageId'] = $pageid;
        return ;
    }

    /**
     * 设置页面SCOPE变量
     *
     * @param unknown_type $key
     * @param unknown_type $value
     */
    public function setScopeVar($key , $value){
        $this->_scope[$key] = $value;
        return ;
    }

    /**
     * 获得页面SCOPE变量
     * @return array
     */
    public function getScope(){
        return $this->_scope;
    }

    /**
     * 设置page_title
     * @param $title
     */
    public function setPageTitle($title){
        $this->_title = $title.' - '.L('WEBSITE');
        return ;
    }

    /**
     * 设置page_title
     * @return string
     */
    public function getPageTitle(){
        return empty($this->_title) ? L('WEBSITE') : $this->_title;
    }

    /**
     * 增加页面 导航配置
     * @internal param key $string
     * @internal param $value
     */
    public function addPageNav($name, $value=''){
        if(!is_array($name)){
            $this->_nav[$name] = $value;
            return true;
        }
        foreach($name as $k=>$v){
            $this->_nav[$k] = $v;
        }
        return true;
    }

    /**
     * 增加页面 导航配置
     * @internal param key $string
     * @internal param $value
     */
    public function getPageNav(){
        return $this->_nav;
    }

    /**
     * 设置页面 SSO描述
     * @param string $string
     * @return bool
     */
    public function addPageDescription($string){
        if(!is_array($string)){
            $this->_description[] = $string;
            return true;
        }
        $this->_description = array_merge($this->_description, $string);
        return true;
    }

    /**
     * 增加页面 SSO关键字
     * @param unknown_type $string
     * @return bool
     */
    public function addPageKeyword($string){
        if(!is_array($string)){
            $this->_keyword[] = $string;
            return true;
        }
        $this->_keyword = array_merge($this->_keyword, $string);
        return true;
    }

    /**
     * 获取pageDescription
     * @param string $glud
     * @return string
     */
    public function getPageDescription($glud=','){
        if(empty($this->_description))
            return '';
         return implode($glud , array_unique($this->_description));
    }

    /**
     * 获取pageDescription
     * @param string $glud
     * @return string
     */
    public function getPageKeyword($glud=','){
        if(empty($this->_keyword))
            return '';
        return implode($glud , array_unique($this->_keyword));
    }

    /**
     * 获取显示器对象
     * @return K_View|null
     */
    public function getViewer(){
        if (is_null($this->_viewer)) {
            $this->_viewer = View::getInstance();
        }
        return $this->_viewer;
    }

    /**
     * 获取请求对象
     * @return null|object
     */
    public function getRequest(){
        if(is_null($this->_request)) {
            $this->_request = Request::getInstance();
        }
        return $this->_request;
    }

    /**
     * 显示器变量
     * @param $name
     * @param $val
     */
    public function assign($name, $val){
        $this->getViewer()->assign($name, $val);
    }

    /**
     * 显示模版
     * @param string $template
     * @param string $tpl_style
     */
    public function display($template='', $tpl_style=''){
        if(isset($tpl_style))
            $this->getViewer()->setTplStype($tpl_style);

        $this->setDisplayData();
        return $this->getViewer()->display($template);
    }

    public function setDisplayData(){
        $this->assign('RIA_URL',    C('ria_url'));
        $this->assign('HOST_URL',   SITE_URL);

        $head['title']          = $this->getPageTitle();
        $head['description']    = $this->getPageDescription();
        $head['keyword']        = $this->getPageKeyword();

        $this->setScopeVar('$riaUrl',       C('ria_url'));
        $this->setScopeVar('$hostUrl',      SITE_URL);
        if(C('lang_display')){
            $this->assign('LANG', L());
        }

        $this->assign('__URI__',    __URI__);
        $this->assign('scopes',     $this->getScope());
        $this->assign('head',       $head);
        $this->assign('data',       $this->getData());
    }

    /**
     * 代理视图对象的渲染方法
     *
     * @param string $template
     * @param string $tpl_style
     * @return string
     */
    public function render($template='', $tpl_style=''){
        if(isset($tpl_style))
            $this->getViewer()->setTplStype($tpl_style);

        $this->setDisplayData();
        return $this->getViewer()->render($template);
    }

    /**
     * 页面输出
     * @param string $template
     * @param array $data
     * @param string $tpl_style
     */
    public function page_output($template='', $data=array(), $tpl_style=''){
        $this->setData($data);
        $this->display($template, $tpl_style);
    }

    /**
     * 接口输出
     * @param $err
     * @param null $data
     * @param string $msg
     */
    public function api_output($err , $data = null , $msg = ''){
        if(!is_array($err)){
            $err = strtoupper($err);
            $rcode_config = Loader::loadConfig('rcode');
            if(!isset($rcode_config[$err])){
                $err = 'HACK';
            }

            $code = $rcode_config[$err];


            $out_data = array(
                'status'    => array(
                    'code'  => $code,
                    'msg'   => $msg,
                ),
            );
            if(empty($msg))
                $out_data['status']['msg'] = L('_ERR_'.$err.'_');
        }else{
            $out_data = array(
                'status'=>$err,
            );
            $code = $err['code'];
        }


        if($data || is_array($data))
            $out_data['data'] = $data;


        Timer::end('total');
        T_Logger::setLoginfo(C('log_attribute'));
        T_logger::pushLog('code',   $code);
        T_logger::pushLog('msg',    $out_data['status']['msg']);
        T_Logger::requestLog();
        T_Output::output($out_data , $this->getParam('format') , $this->getParam('varname') , $this->getParam('jsonp'));
    }

    /**
     * 获取POST数据
     * @param string $key
     * @return mixed
     */
    public function getPost($key=''){
        if($key){
            $res = $this->getRequest()->getPost($key);
        }else{
            $res = $this->getRequest()->getPost();
        }
        return $res;
    }

    /**
     * 获取get数据
     * @param string $key
     * @return mixed
     */
    public function getQuery($key=''){
        if($key){
            $res = $this->getRequest()->getQuery($key);
        }else{
            $res = $this->getRequest()->getQuery();
        }
        return $res;
    }

    /**
     * 获取request数据
     * @param string $key
     * @return mixed
     */
    public function getParam($key=''){
        if($key){
            $res = $this->getRequest()->getParam($key);
        }else{
            $res = $this->getRequest()->getParam();
        }
        return $res;
    }

    /**
     * 获取request数据
     * @param string $key
     * @return mixed
     */
    public function getFiles($key=''){
        if($key){
            $res = $this->getRequest()->getFiles($key);
        }else{
            $res = $this->getRequest()->getFiles();
        }
        return $res;
    }

    /**
     * 页面跳转
     * @param $message
     * @param int $status
     * @param int $time
     */
    public function jump($message='', $status=1, $time=0){
        $this->assign('msgTitle', $status==1 ? L('_ERR_SUCC_') : L('_ERR_FAIL_'));
        if($this->getQuery('closeWin'))
            $this->assign('jumpUrl','javascript:window.close();');
        $this->assign('status',  $status);
        $this->assign('message', $message);

        if($status==1) {
            !$time ? $time=3 : '';
            if(!$this->getQuery('waitSecond'))
                $this->assign('waitSecond', $time);
            if(!$this->getQuery('jumpUrl'))
                $this->assign("jumpUrl", isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : '');
            $this->display(C('tpl_success'));
        }else{
            !$time ? $time=1 : '';
            if(!$this->getQuery('waitSecond'))
                $this->assign('waitSecond', $time);

            if(!$this->getQuery('jumpUrl'))
                $this->assign('jumpUrl', "javascript:history.back(-1);");
            $this->display(C('tpl_fail'));
        }
        Timer::end('total');
        T_Logger::setLoginfo(C('log_attribute'));
        T_Logger::requestLog();
        exit;
    }

    public function filter(&$res, $key='', $data=array()){
        $options = Loader::loadFilter(__MODULE_NAME__.'_'.__CONTROLLER_NAME__, __ACTION_NAME__);
        if(empty($options)){
            return true;
        }
        if(!is_array($res)){
            if(empty($key))
                return false;

            if(!isset($options[$key])){
                return true;
            }
            $option = $options[$key];
            $option['data'] = $data;
            $rs = T_Filter::filter($res, $option);
            if(false === $rs){
                $this->setError('PARAM', isset($v['msg'])? $v['msg'] : '');
                return true;
            }
            return true;
        }



        foreach($options as $k=>$v){
            !isset($res[$k]) ? $res[$k] = '' : '';
            if(isset($data[$k])){
                $v['data'] = $data[$k];
            }
            $rs = T_Filter::filter($res[$k], $v);
            if(false === $rs){
                $this->setError('PARAM', isset($v['msg'])? $v['msg'] : '');
                return false;
            }
        }
        return true;
    }

    protected function setError($code, $msg=''){
        $rcode_config = Loader::loadConfig('rcode');
        $this->_error['code'] = $rcode_config[$code];
        $this->_error['msg']  = !empty($msg) ? $msg : L('_ERR_'.$code.'_');
    }

    protected function getError(){
        return $this->_error;
    }

}