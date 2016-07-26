<?php
/**
 * @fileoverview:   配置
 * @author:         Uqidi
 * @date:           2015-11-17
 * @copyright:      Uqidi
 **/

return array(
    'db_name'       => 'web',
    'db_type'       => 'mysql',
    'queue_type'    => 'rds',
    'router_engine' => 'R_Stand',
    'router_var'    => array('module'=>'m','controller'=>'c','action'=>'a'),
    'viewer_engine' => 'V_Smarty',
    'tpl_success'  => 'public/success',
    'tpl_fail'     => 'public/error',
    'tpl_style'     => '',

    'lang'          => 'zh-cn',
    'lang_auto'     => false,
    'lang_var'      => 'lang',
    'lang_display'  => false,


    'cross'         => array('uqidi.com', 'uqidi.cn'),
    'log_attribute' => array('uid','id'),
    'cookie_prefix' => 'qd_',
    'cookie_expire' => '86400',
    'cookie_path'   => '/',
    'cookie_domain' => SITE_HOST,

);

