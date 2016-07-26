<?php
/**
 * @fileoverview:   标准渲染器
 * @author:         Uqidi
 * @date:           2015-11-22
 * @copyright:      Uqidi
 */

abstract class V_Base{
    protected $_vars = array();             /* 模板变量 */
    protected $_templateSuffix = '.html';   /* 模板扩展名 */
    protected $_tpl_style = '';
    protected $_template_dir = '';

    /**
     * 渲染方法
     * @param  string $template 模板名字
     * @return string 渲染模板结果
     */
    abstract function render($template='');
    abstract function display($template='');

    public function setTplStype($tpl_style){
        $this->_tpl_style = !empty($tpl_style) ? $tpl_style : C('tpl_style');
    }

    public function getTplStype(){
        return !empty($this->_tpl_style) ? $this->_tpl_style : C('tpl_style');
    }

    protected function _templatePath($template){
        return $template.$this->_templateSuffix;
    }

    /**
     * 加载变量
     * @param string $var 变量名
     * @param mixed $value
     */
    public function assign($var, $value){
        $this->__set($var, $value);
    }

    /**
     * 获得模板变量
     * @param string $var 变量名称
     * @return mixed
     */
    public function getVar($var=''){
        if(isset($var))
            return $this->__get($var);
        return $this->_vars;
    }

    /**
     * 删除模板变量
     * @param string $var 变量名称
     */
    public function clearVars($var){
        if (isset($this->_vars[$var])) {
            $this->__unset($var);
        }
    }

    /**
     * 设置模板变量
     * @see self::assign()
     * @param string $var
     * @param mixed $value
     *
     */
    public function __set($var, $value){
        $this->_vars[$var] = $value;
    }

    /**
     * 获得模板变量
     * @param string $var 变量名
     * @return mixed
     */
    public function __get($var){
        if (isset($this->_vars[$var])) {
            return $this->_vars[$var];
        } else {
            return null;
        }
    }

    /**
     * 清除模板变量
     * @param string $var
     */
    public function __unset($var){
        unset($this->_vars[$var]);
    }


}

