<?php
/**
 * @fileoverview:   设备配置
 * @author:         Uqidi
 * @date:           2015-11-17
 * @copyright:      Uqidi
 **/
abstract class C_Device{
    /* 设备类型 */
    const OTYPE_WINDOWS     = 1;    /* windows */
    const OTYPE_ANDROID     = 2;    /* 安卓 */
    const OTYPE_IOS         = 3;    /* IOS */

    static public $otypes = array(
        'windows'       => self::OTYPE_WINDOWS,
        'android'       => self::OTYPE_ANDROID,
        'ios'           => self::OTYPE_IOS,
    );

    /* APP类型 */
    const ATYPE_WEB     = 1;    /* web 默认 */
    const ATYPE_ADMIN   = 2;    /* admin 后台 */

    static public $atypes = array(
        'uqidi_web'         => self::ATYPE_WEB,
        'uqidi_admin'       => self::ATYPE_ADMIN,
    );
}













