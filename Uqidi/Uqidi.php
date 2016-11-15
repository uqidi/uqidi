<?php
/**
 * @fileoverview:   框架入口
 * @author:         Uqidi
 * @date:           2015-10-24
 * @copyright:      Uqidi.com
 */

/* php基础设置 */
date_default_timezone_set('Asia/Shanghai');
ini_set("magic_quotes_runtime", 0);

/* 版本判断 */
if(version_compare(PHP_VERSION,'5.0.0','<'))
    die('require PHP > 5.0 !');

define('NOW_TIME', isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time());


/* 系统调试开关 */
if(!defined('DEBUG'))
    define('DEBUG' , true);

require UQIDI_PATH.'C/Constant.php';
require UQIDI_PATH.'C/Logger.php';
require UQIDI_PATH.'F/system.php';
require UQIDI_PATH.'K/Core/Loader.php';
require UQIDI_PATH.'T/Timer.php';
Loader::loadApp();
//111
