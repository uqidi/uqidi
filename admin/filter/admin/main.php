<?php
return array(
    'profile'     => array(
        'realname' => array(
            'type'      => T_Filter::TYPE_CALL,
            'must'      => true,
            'fun'       => 'T_Check::realname',
            'msg'       => L('realname').L('_ERR_PARAM_'),
        ),
        'password' => array(
            'type'      => T_Filter::TYPE_CALL,
            'must'      => false,
            'fun'       => 'T_Check::password',
            'msg'       => L('password').L('_ERR_PARAM_'),
        ),
        'email' => array(
            'type'      => T_Filter::TYPE_CALL,
            'must'      => true,
            'fun'       => 'T_Check::email',
            'msg'       => L('email').L('_ERR_PARAM_'),
        ),
    ),
);

