<?php
/**
 * @fileoverview:   queue配置
 * @author:         Uqidi
 * @date:           2015-11-17
 * @copyright:      Uqidi
 **/

if (RUN_T == RUN_T_ONLINE) {
    return array(
        'default'   => array(
            array('host'=>'127.0.0.1' , 'port'=>'6379'),
        ),
        'order'      => array(
            array('host'=>'127.0.0.1' , 'port'=>'6379'),
        ),
    );
}else{
    return array(
        'default'   => array(
            array('host'=>'127.0.0.1' , 'port'=>'6379'),
        ),
        'order'      => array(
            array('host'=>'127.0.0.1' , 'port'=>'6379'),
        ),
    );
}