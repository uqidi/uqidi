<?php
if(RUN_T == RUN_T_ONLINE){
    return array(
        'admin' => array(
            'db_prefix'     => 'qd_',
            'master' => array(
                'host' => array(
                    0 => '127.0.0.1:3306'
                ),
                'user'  => 'root',
                'pw'    => 'qidiyao',
                'name'  => 'qidiyao_admin',
            ),
            'slave' => array(
                'host' => array(
                    0 => '127.0.0.1:3306'
                ),
                'user'  => 'root',
                'pw'    => 'qidiyao',
                'name'  => 'qidiyao_admin',
            )
        ),
    );
}else{
    return array(
        'admin' => array(
            'db_prefix'     => 'qd_',
            'master' => array(
                'host' => array(
                    0 => '127.0.0.1:3306'
                ),
                'user'  => 'root',
                'pw'    => 'qidiyao',
                'name'  => 'qidiyao_admin',
            ),
            'slave' => array(
                'host' => array(
                    0 => '127.0.0.1:3306'
                ),
                'user'  => 'root',
                'pw'    => 'qidiyao',
                'name'  => 'qidiyao_admin',
            )
        ),
    );
}


