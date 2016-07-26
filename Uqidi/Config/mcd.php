<?php
/**
 * @fileoverview:   memcached配置
 * @author:         Uqidi
 * @date:           2015-11-17
 * @copyright:      Uqidi
 **/
if (RUN_T == RUN_T_ONLINE) {
    return array(
        C_Mcd::SERVER_DEFAULT   => array(
            array('host'=>'127.0.0.1' , 'port'=>'11211'),
        ),
        C_Mcd::SERVER_USER      => array(
            array('host'=>'127.0.0.1' , 'port'=>'11211'),
        ),
    );
}else{
    return array(
        C_Mcd::SERVER_DEFAULT   => array(
            array('host'=>'127.0.0.1' , 'port'=>'11211'),
        ),
        C_Mcd::SERVER_USER      => array(
            array('host'=>'127.0.0.1' , 'port'=>'11211'),
        ),
    );
}
