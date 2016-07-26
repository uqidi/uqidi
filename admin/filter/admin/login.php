<?php
return array(
    'do_login'     => array(
        'username' => array(
            'type'      => T_Filter::TYPE_CALL,
            'must'      => true,
            'fun'       => 'T_Check::username',
            'msg'       => L('username').L('_ERR_PARAM_'),
        ),
        'password' => array(
            'type'      => T_Filter::TYPE_CALL,
            'must'      => true,
            'fun'       => 'T_Check::password',
            'msg'       => L('password').L('_ERR_PARAM_'),
        ),
    ),
);

