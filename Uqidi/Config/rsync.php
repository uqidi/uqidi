<?php
/**
 * @fileoverview:   rsync服务器
 * @author:         Uqidi
 * @date:           2015-11-17
 * @copyright:      Uqidi
 **/
if (RUN_T == RUN_T_ONLINE) {
    return array(
        'data'  => array(
            array(
                'password_file' => '/etc/rsyncd.secrets',
                'user'          => 'www',
                'host'          => '127.0.0.1',
                'module'        => 'web_data',
            ),
        )
    );
}else{
    return array(
        'data'  => array(
            array(
                'password_file' => '/etc/rsyncd.secrets',
                'user'          => 'www',
                'host'          => '127.0.0.1',
                'module'        => 'web_data',
            ),
        )
    );
}
