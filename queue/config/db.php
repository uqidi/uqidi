<?php
return array(
    'order' => array(
        'db_prefix'  =>'qd_',
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
);

