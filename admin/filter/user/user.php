<?php
return array(
    'add'     => array(
        'username' => array(
            'type'      => T_Filter::TYPE_CALL,
            'must'      => true,
            'fun'       => 'T_Check::username',
            'msg'       => L('username').L('_ERR_PARAM_'),
        ),
        'group_id'      => array(
            'type'      => T_Filter::TYPE_INT,
            'must'      => true,
            'msg'       => L('group_name').L('_ERR_PARAM_'),
        ),
        'nickname' => array(
            'type'      => T_Filter::TYPE_CALL,
            'must'      => true,
            'fun'       => 'T_Check::realname',
            'msg'       => L('nickname').L('_ERR_PARAM_'),
        ),
        'password' => array(
            'type'      => T_Filter::TYPE_CALL,
            'must'      => true,
            'fun'       => 'T_Check::password',
            'msg'       => L('password').L('_ERR_PARAM_'),
        ),
        'trader_password' => array(
            'type'      => T_Filter::TYPE_CALL,
            'must'      => false,
            'fun'       => 'T_Check::trader_password',
            'msg'       => L('trader_password').L('_ERR_PARAM_'),
        ),
    ),
    'edit'     => array(
        'uid'      => array(
            'type'      => T_Filter::TYPE_INT,
            'must'      => true,
            'msg'       => L('uid').L('_ERR_PARAM_'),
        ),
        'group_id'      => array(
            'type'      => T_Filter::TYPE_INT,
            'must'      => true,
            'msg'       => L('group_name').L('_ERR_PARAM_'),
        ),
        'nickname' => array(
            'type'      => T_Filter::TYPE_CALL,
            'must'      => true,
            'fun'       => 'T_Check::realname',
            'msg'       => L('nickname').L('_ERR_PARAM_'),
        ),
        'password' => array(
            'type'      => T_Filter::TYPE_CALL,
            'must'      => false,
            'fun'       => 'T_Check::password',
            'msg'       => L('password').L('_ERR_PARAM_'),
        ),
        'trader_password' => array(
            'type'      => T_Filter::TYPE_CALL,
            'must'      => false,
            'fun'       => 'T_Check::trader_password',
            'msg'       => L('trader_password').L('_ERR_PARAM_'),
        ),
    ),
    'editInfo'  => array(
        'uid'      => array(
            'type'      => T_Filter::TYPE_INT,
            'must'      => true,
            'msg'       => L('uid').L('_ERR_PARAM_'),
        ),
        'email' => array(
            'type'      => T_Filter::TYPE_CALL,
            'must'      => false,
            'fun'       => 'T_Check::email',
            'msg'       => L('email').L('_ERR_PARAM_'),
        ),
        'qq' => array(
            'type'      => T_Filter::TYPE_DIGIT,
            'must'      => false,
            'msg'       => L('qq').L('_ERR_PARAM_'),
        ),
        'phone' => array(
            'type'      => T_Filter::TYPE_CALL,
            'must'      => false,
            'fun'       => 'T_Check::phone',
            'msg'       => L('phone').L('_ERR_PARAM_'),
        ),
    ),
    'editBank'  => array(
        'uid'      => array(
            'type'      => T_Filter::TYPE_INT,
            'must'      => true,
            'msg'       => L('uid').L('_ERR_PARAM_'),
        ),
        'bank_type' => array(
            'type'      => T_Filter::TYPE_INT,
            'must'      => true,
            'msg'       => L('bank_type').L('_ERR_PARAM_'),
        ),
        'bank_user' => array(
            'type'      => T_Filter::TYPE_CALL,
            'must'      => true,
            'fun'       => 'T_Check::realname',
            'msg'       => L('bank_user').L('_ERR_PARAM_'),
        ),
        'bank_code' => array(
            'type'      => T_Filter::TYPE_CALL,
            'must'      => true,
            'fun'       => 'T_Check::bank_code',
            'msg'       => L('bank_code').L('_ERR_PARAM_'),
        ),
    ),
);

