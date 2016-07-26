<?php
/**
 * @fileoverview:   路由配置
 * @author:         Uqidi
 * @date:           2015-11-17
 * @copyright:      Uqidi
 **/

return array(
    'base'  => array(
        'domain'                    => SITE_HOST,
        'port'                      => '',
        C('router_var.module')      => 'admin',
        C('router_var.controller')  => 'Login',
        C('router_var.action')      => 'login'
    ),
    'router' => array(

    ),

);