<?php
/**
 * @fileoverview:   布局渲染引擎 - layout
 *      此引擎负责页面布局渲染，如果不需要布局模板则不需要调用此引擎
 *      此布局渲染需要和模板渲染引擎配合适用
 * @author:         Uqidi
 * @date:           2015-11-22
 * @copyright:      Uqidi
 */
require_once UQIDI_PATH.'P/Smarty/Smarty.class.php';
class V_Smarty extends V_Base{
    protected $smarty;

    public function __construct(){
        $this->smarty = new Smarty();
        $config = Loader::loadConfig('smarty');
        if(isset($config['cache_dir']))
            $this->smarty->setCacheDir($config['cache_dir']);
        if(isset($config['config_dir']))
            $this->smarty->setConfigDir($config['config_dir']);
        if(isset($config['template_dir'])){
            $this->smarty->setTemplateDir($config['template_dir']);
            $this->_template_dir = $config['template_dir'];
        }
        if(isset($config['compile_dir']))
            $this->smarty->setCompileDir($config['compile_dir']);
        if(isset($config['delimiter'])){
            $this->smarty->setLeftDelimiter($config['delimiter'][0]);
            $this->smarty->setRightDelimiter($config['delimiter'][1]);
        }
        if(isset($config['caching'])){
            $this->smarty->caching  = $config['caching'];
        }

        if(isset($config['cache_lifetime'])){
            $this->smarty->cache_lifetime  = $config['cache_lifetime'];
        }

        $this->smarty->addPluginsDir(UQIDI_PATH.'P/Smarty/plugin');
        if(isset($config['template_suffix'])){
            $this->_templateSuffix = $config['template_suffix'];
        }
    }

    public function setTplStype($tpl_style){
        parent::setTplStype($tpl_style);

        if(!empty($this->_tpl_style)){
            $template_dir = $this->_template_dir.$this->_tpl_style.'/';
        }else{
            $template_dir = $this->_template_dir;
        }
        $this->smarty->setTemplateDir($template_dir);
    }

    public function render($template=''){
        foreach($this->_vars as $key => $val){
            $this->smarty->assign($key, $val);
        }

        return $this->smarty->fetch($this->_templatePath($template));
    }

    /**
     * 解析模板 直接输出到浏览器
     * @param string $template
     * @return string
     */
    public function display($template=''){
        foreach($this->_vars as $key => $val){
            $this->smarty->assign($key, $val);
        }

        $this->smarty->display($this->_templatePath($template));
    }
}