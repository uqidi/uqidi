<?php
/**
 * @fileoverview:   框架级日志配置
 * @author:         Uqidi
 * @date:           2015-10-24
 * @copyright:      Uqidi.com
 */

define('SYS_LOG_PATH',      '/data1/www/logs/'.APP_NAME.'/');       /* 日志路径 */
define('SYS_CACHE_PATH',    '/data1/www/cache/'.APP_NAME.'/');      /* 缓存路径 */
define('SYS_DATA_PATH',     '/data1/www/data/'.APP_NAME.'/');       /* 数据路径 */


define('SYS_LOG_DEBUG_PATH',    SYS_LOG_PATH.'debug');      /* 调试LOG */
define('SYS_LOG_MONITOR_PATH',  SYS_LOG_PATH.'monitor');    /* 监控LOG */
define('SYS_LOG_DATA_PATH',     SYS_LOG_PATH.'data');       /* 数据统计LOG */
define('SYS_LOG_ACTION_PATH',   SYS_LOG_PATH.'action');     /* 行为统计LOG */

define('SYS_LOG_SEPARATE' ,     "\t");  /* LOG字段分隔符 */

define("SYSDEF_LOG_ID", time().rand(10000,20000));   /* 日志ID */