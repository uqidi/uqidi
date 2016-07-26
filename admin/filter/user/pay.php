<?php
return array(
    'add'     => array(
        'pay_type' => array(
            'type'      => T_Filter::TYPE_INT,
            'must'      => true,
            'msg'       => L('pay_type'),
        ),
        'account_type' => array(
            'type'      => T_Filter::TYPE_INT,
            'must'      => true,
            'msg'       => L('account_type'),
        ),
        'account_name' => array(
            'must'      => true,
            'msg'       => L('account_name'),
        ),
        'account_id' => array(
            'must'      => true,
            'msg'       => L('account_id'),
        ),
    ),
    'edit'     => array(
        'id' => array(
            'type'      => T_Filter::TYPE_INT,
            'must'      => true,
            'msg'       => L('id'),
        ),
        'pay_type' => array(
            'type'      => T_Filter::TYPE_INT,
            'must'      => true,
            'msg'       => L('pay_type'),
        ),
        'account_type' => array(
            'type'      => T_Filter::TYPE_INT,
            'must'      => true,
            'msg'       => L('account_type'),
        ),
        'account_name' => array(
            'must'      => true,
            'msg'       => L('account_name'),
        ),
        'account_id' => array(
            'must'      => true,
            'msg'       => L('account_id'),
        ),
    ),
);

