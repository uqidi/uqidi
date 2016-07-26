<?php
return array(
    'template_dir'          => APP_VIEW_PATH,
    'compile_dir'           => SYS_CACHE_PATH.'smarty/compile',
    'cache_dir'             => SYS_CACHE_PATH.'smarty/cache',
    'caching'               => 0,
    'cache_lifetime'        => 3600,
    'config_dir'            => APP_VIEW_PATH.'config',
    'delimiter'             => array('{%', '%}'),
);

