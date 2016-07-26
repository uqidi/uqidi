<?php
if(RUN_T == RUN_T_ONLINE){
    return array(
        'web' => array(
            'db_prefix'     => 'qd_',
            'master' => array(
                'host' => array(
                    0 => '127.0.0.1:3306'
                ),
                'user'  => 'uqidi',
                'pw'    => 'uqidi',
                'name'  => 'uqidi_web',
            ),
            'slave' => array(
                'host' => array(
                    0 => '127.0.0.1:3306'
                ),
                'user'  => 'uqidi',
                'pw'    => 'uqidi',
                'name'  => 'uqidi_web',
            ),
        ),
        'admin' => array(
            'db_prefix'     => 'qd_',
            'master' => array(
                'host' => array(
                    0 => '127.0.0.1:3306'
                ),
                'user'  => 'uqidi',
                'pw'    => 'uqidi',
                'name'  => 'uqidi_admin',
            ),
            'slave' => array(
                'host' => array(
                    0 => '127.0.0.1:3306'
                ),
                'user'  => 'uqidi',
                'pw'    => 'uqidi',
                'name'  => 'uqidi_admin',
            )
        ),
        'game' => array(
            'db_prefix'     => 'qd_',
            'master' => array(
                'host' => array(
                    0 => '127.0.0.1:3306'
                ),
                'user'  => 'uqidi',
                'pw'    => 'uqidi',
                'name'  => 'uqidi_game',
            ),
            'slave' => array(
                'host' => array(
                    0 => '127.0.0.1:3306'
                ),
                'user'  => 'uqidi',
                'pw'    => 'uqidi',
                'name'  => 'uqidi_game',
            )
        ),
        'order' => array(
            'db_prefix'     => 'qd_',
            'master' => array(
                'host' => array(
                    0 => '127.0.0.1:3306'
                ),
                'user'  => 'uqidi',
                'pw'    => 'uqidi',
                'name'  => 'uqidi_order',
            ),
            'slave' => array(
                'host' => array(
                    0 => '127.0.0.1:3306'
                ),
                'user'  => 'uqidi',
                'pw'    => 'uqidi',
                'name'  => 'uqidi_order',
            )
        ),
        'log' => array(
            'db_prefix'     => 'qd_',
            'master' => array(
                'host' => array(
                    0 => '127.0.0.1:3306'
                ),
                'user'  => 'uqidi',
                'pw'    => 'uqidi',
                'name'  => 'uqidi_log',
            ),
            'slave' => array(
                'host' => array(
                    0 => '127.0.0.1:3306'
                ),
                'user'  => 'uqidi',
                'pw'    => 'uqidi',
                'name'  => 'uqidi_log',
            )
        ),
    );
}else{
    return array(
        'web' => array(
            'db_prefix'     => 'qd_',
            'master' => array(
                'host' => array(
                    0 => '127.0.0.1:3306'
                ),
                'user'  => 'uqidi',
                'pw'    => 'uqidi',
                'name'  => 'uqidi_web',
            ),
            'slave' => array(
                'host' => array(
                    0 => '127.0.0.1:3306'
                ),
                'user'  => 'uqidi',
                'pw'    => 'uqidi',
                'name'  => 'uqidi_web',
            ),
        ),
        'admin' => array(
            'db_prefix'     => 'qd_',
            'master' => array(
                'host' => array(
                    0 => '127.0.0.1:3306'
                ),
                'user'  => 'uqidi',
                'pw'    => 'uqidi',
                'name'  => 'uqidi_admin',
            ),
            'slave' => array(
                'host' => array(
                    0 => '127.0.0.1:3306'
                ),
                'user'  => 'uqidi',
                'pw'    => 'uqidi',
                'name'  => 'uqidi_admin',
            )
        ),
        'game' => array(
            'db_prefix'     => 'qd_',
            'master' => array(
                'host' => array(
                    0 => '127.0.0.1:3306'
                ),
                'user'  => 'uqidi',
                'pw'    => 'uqidi',
                'name'  => 'uqidi_game',
            ),
            'slave' => array(
                'host' => array(
                    0 => '127.0.0.1:3306'
                ),
                'user'  => 'uqidi',
                'pw'    => 'uqidi',
                'name'  => 'uqidi_game',
            )
        ),
        'order' => array(
            'db_prefix'     => 'qd_',
            'master' => array(
                'host' => array(
                    0 => '127.0.0.1:3306'
                ),
                'user'  => 'uqidi',
                'pw'    => 'uqidi',
                'name'  => 'uqidi_order',
            ),
            'slave' => array(
                'host' => array(
                    0 => '127.0.0.1:3306'
                ),
                'user'  => 'uqidi',
                'pw'    => 'uqidi',
                'name'  => 'uqidi_order',
            )
        ),
        'log' => array(
            'db_prefix'     => 'qd_',
            'master' => array(
                'host' => array(
                    0 => '127.0.0.1:3306'
                ),
                'user'  => 'uqidi',
                'pw'    => 'uqidi',
                'name'  => 'uqidi_log',
            ),
            'slave' => array(
                'host' => array(
                    0 => '127.0.0.1:3306'
                ),
                'user'  => 'uqidi',
                'pw'    => 'uqidi',
                'name'  => 'uqidi_log',
            )
        ),
    );
}


