<?php
/**
 * @fileoverview:   框架级常量
 * @author:         Uqidi
 * @date:           2015-10-24
 * @copyright:      Uqidi.com
 */

/* 运行环境类型
 * online   生产
 * test     测试
 * dev      开发
*/

define('RUN_T_ONLINE',   'online');
define('RUN_T_TEST',     'test');
define('RUN_T_DEV',      'dev');

if(!defined('RUN_T'))
    define('RUN_T' , 'dev');

define('RUN_START_TIME',    1465142400);
define('APP_CONFIG_PATH',   APP_PATH.'config/');
define('APP_FILTER_PATH',   APP_PATH.'filter/');
define('APP_INCLUDE_PATH',  APP_PATH.'include/');
define('APP_LANG_PATH',     APP_PATH.'lang/');
define('APP_ACTION_PATH',   APP_PATH.'action/');
define('APP_VIEW_PATH',     APP_PATH.'view/');

