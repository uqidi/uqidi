<?php
/**
 * @fileoverview:   Crypt_Id
 * @author:         Uqidi
 * @date:           2015-11-15
 * @copyright:      uqidi.com
 */
class P_Crypt_Id{
    const USER_ID_PREFIX = 'U';
    /**
     * ID加密
     * @author Uqidi
     * @param $id
     * @param string $prefix
     * @return string
     */
    public static function IEncode($id, $prefix=self::USER_ID_PREFIX){
        if(empty($id)) return '';
        if(!self::IIsEncode($id, $prefix))
            $encodeId = self::_IWrap((base64_encode($id<<2)), $prefix);
        else
            $encodeId = $id;
        return $encodeId;
    }

    /**
     * ID解密
     * @author Uqidi
     * @param $encodeId
     * @param $prefix
     * @return int|string
     */
    public static function IDecode($encodeId, $prefix){
        if(empty($encodeId)) return '';
        if(self::IIsEncode($encodeId, $prefix)) $encodeId = (base64_decode(self::_IDewrap($encodeId, $prefix)))>>2;
        return $encodeId;
    }

    /**
     * 是否可以加解密
     * @author Uqidi
     * @param $id
     * @param $prefix
     * @return bool
     */
    public static function IIsEncode($id, $prefix){
        return self::_IIsWrapped($id, $prefix) ? true : false;
    }

    /**
     * 加密执行体
     * @author Uqidi
     * @param $encodeId
     * @param $prefix
     * @return string
     */
    private function _IWrap($encodeId, $prefix){
        return $prefix.$encodeId;
    }

    /**
     * 解密执行体
     * @author Uqidi
     * @param $encodeId
     * @param $prefix
     * @return string
     */
    private function _IDewrap($encodeId, $prefix){
        return self::_IIsWrapped($encodeId, $prefix) ? substr($encodeId,strlen($prefix)) : $encodeId;
    }

    /**
     * 是否可以加解密执行体
     * @author Uqidi
     * @param $id
     * @param $prefix
     * @return bool
     */
    private function _IIsWrapped($id, $prefix){
        return (stripos($id,$prefix)===0) ? true : false;
    }
}