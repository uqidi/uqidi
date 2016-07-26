<?php
/* 应用名字 */
define('APP_NAME', 'queue');

/* 运行模式 */
define('MODE_NAME', 'cli');

/* 应用路径 */
define("APP_PATH", dirname(dirname(__FILE__)).'/');

/* 框架路径 */
define('UQIDI_PATH', '/opt/org/uqidi/web/branches/Uqidi/v1/');

/* 网站的域名 */
define('SITE_HOST', 'queue.uqidi.com');

/* 网站的根路径 */
define('SITE_URL', "http://".SITE_HOST);

require(UQIDI_PATH."/Uqidi.php");
App::run();

