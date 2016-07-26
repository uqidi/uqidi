<?php
/**
 * @fileoverview:   定时器，用于计算一段代码的执行时间 时间单位是ms
 * @author:         Uqidi
 * @date:           2015-11-21
 * @copyright:      Uqidi
 */
class Timer{
    /**
     * 用于存在时间数据：开始时间和结束时间
     * @var array
     * {
     * 		start : 开始时间
     * 		end : 结束时间
     * }
     */
    private static $_arrData = array();
    /**
     * 执行的时间，us。
     * @var array
     * {
     * 		$strName => $intTimeUs
     * }
     */
    private static $_arrExecTime = array();

    private static $_intTimeS = null;

    /**
     * 计时开始。
     * @param string $strName
     */
    public static function start($strName){
        self::$_arrData[$strName]['end'] = self::$_arrData[$strName]['start'] = microtime(true);
    }
    /**
     * 计时结束
     * @param string $strName
     */
    public static function end($strName){
        self::$_arrData[$strName]['end'] = microtime(true);
    }

    /**
     * 获取执行时间，如果没有指定$strName，则返回所有的执行时间数据。
     * @param string $strName
     * @return array|int
     */
    public static function getTimes($strName=''){
        if (!empty($strName)) {
            return self::_calculateOne($strName);
        } else {
            return self::_calculateAll();
        }
    }


    /**
     * 获取当前时间
     * @return int|null
     */
    public static function getNowTime(){
        if (is_null(self::$_intTimeS)) {
            self::$_intTimeS = time();
        }
        return self::$_intTimeS;
    }

    /**
     * 计算时间
     * @param float $intStart
     * @param float $intEnd
     * @return float
     */
    public static function getUtime($intStart, $intEnd){
        return round(($intEnd-$intStart), 3)*1000;
    }

    /**
     * 统计数据转换称字符串
     * @param string $chr1
     * @param string $chr2
     * @return string
     */
    public static function toString($chr1=':', $chr2=' '){
        $arrRet = self::getTimes();
        if (empty($arrRet)) return '';
        $strRet = '';
        foreach ($arrRet as $strName => $strTimer) {
            $strRet .= sprintf('%s%s%s%s',$strName, $chr1, $strTimer, $chr2);
        }
        $strRet = rtrim($strRet, $chr2);
        return $strRet;
    }

    /**
     * 获取所有统计
     * @return array
     */
    protected static function _calculateAll(){
        if (empty(self::$_arrData)) return array();
        foreach(self::$_arrData as $strName => $arrTime) {
            if (!isset(self::$_arrExecTime[$strName])) {
                self::$_arrExecTime[$strName] = self::getUtime($arrTime['start'], $arrTime['end']);
            }
        }
        return self::$_arrExecTime;
    }

    /**
     * 计算单个统计
     * @param $strName
     * @return int
     */
    protected static function _calculateOne($strName){
        if (isset(self::$_arrExecTime[$strName])) {
            return self::$_arrExecTime[$strName];
        }
        if (isset(self::$_arrData[$strName])) {
            self::$_arrExecTime[$strName] = self::getUtime(self::$_arrData[$strName]['start'], self::$_arrData[$strName]['end']);
            return self::$_arrExecTime[$strName];
        }
        return 0;
    }

}
