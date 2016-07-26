<?php
/**
 * @fileoverview:   redis配置
 * @author:         Uqidi
 * @date:           2015-11-17
 * @copyright:      Uqidi
 **/

if (RUN_T == RUN_T_ONLINE) {
    return array(
        C_Rds::SERVER_DEFAULT   => array(
            array('host'=>'127.0.0.1' , 'port'=>'6379'),
        ),
        C_Rds::SERVER_USER      => array(
            array('host'=>'127.0.0.1' , 'port'=>'6379'),
        ),
    );
}else{
    return array(
        C_Rds::SERVER_DEFAULT   => array(
            array('host'=>'127.0.0.1' , 'port'=>'6379'),
        ),
        C_Rds::SERVER_USER      => array(
            array('host'=>'127.0.0.1' , 'port'=>'6379'),
        ),
    );
}
