<?php
/**
 * @fileoverview:   Action
 * @author:         Uqidi
 * @date:           2015-12-04
 * @copyright:      Uqidi
 */

class Action{
    protected $_params = null;

    protected $_error = array();

    protected $_controller;
    protected $_module;

    public function __construct($plugins=null){
        $this->_controller  = __CONTROLLER_NAME__;
        $this->_module      = __MODULE_NAME__;

        $GLOBALS['CONTROLLER']  = $this->_controller;
        $GLOBALS['MODULE']      = $this->_module;
    }

    /**
     * 获取request数据
     * @param string $key
     * @return mixed
     */
    public function getParam($key=''){
        if(empty($this->_params)){
            $this->_params = $_SERVER['argv'];
        }
        if(empty($key)){
            return $this->_params;
        }

        if(isset($this->_params[$key])){
            return $this->_params[$key];
        }
        return '';
    }

    /**
     * 检查开始
     * @return bool
     */
    public function init(){
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
        T_Output::output($out_data);
    }
}