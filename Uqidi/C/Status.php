<?php
/**
 * @fileoverview:   状态
 * @author:         Uqidi
 * @date:           2015-10-24
 * @copyright:      Uqidi.com
 */

abstract class C_Status{
    const YES           = 1;  /* 正常 */
    const NO            = 2;  /* 已禁止 */
    const DELETE        = 99; /* 已删除 */

    public static $status = array(
        self::YES       => 'yes',
        self::NO        => 'no',
        self::DELETE    => 'delete',
    );
}

