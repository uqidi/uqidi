<?php
/**
 * @fileoverview:   支付配置
 * @author:         Uqidi
 * @date:           2015-11-17
 * @copyright:      Uqidi
 **/

return array(
    'banks' => array(
        10  => array(
            'id'    => 10,
            'name'  => '中国工商银行',
            'code'  => 'ICBC',
        ),
        20  => array(
            'id'    => 20,
            'name'  => '中国建设银行',
            'code'  => 'CCB',
        ),
        30  => array(
            'id'    => 30,
            'name'  => '招商银行',
            'code'  => 'CMB',
        ),
        40  => array(
            'id'    => 40,
            'name'  => '中国农业银行',
            'code'  => 'ABC',
        ),
    ),
    'pay_types'     => array(
        10  => L('pay_type_bank'),
    ),
);

