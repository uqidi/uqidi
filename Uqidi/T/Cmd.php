<?php
/**
 * @fileoverview:   CMD
 * @author:         Uqidi
 * @date:           2015-11-15
 * @copyright:      Uqidi.com
 */
class T_Cmd {
    /**
     * 执行命令行
     * @authord Uqidi
     * @param $cmd
     * @return string
     */
    public static function runCmd($cmd){
        $fp = popen($cmd , 'r');
        $rs = '';
        while (!feof($fp)){
            $rs .= fgets($fp);
        }
        fclose($fp);
        return $rs;
    }

    /**
     * 获得进程数
     * @author Uqidi
     * @param $ps_cmd
     * @param int $cmd_type
     * @return int
     */
    public static function getPsCnt($ps_cmd, $cmd_type=7){
        $cmd_array = array(
            1   => 'grep -v grep',
            2   => 'grep -v vi',
            4   => "grep -v '/bin/sh'",
        );
        foreach($cmd_array as $k=>$v){
            ($cmd_type&$k) ? $cmds[] = $v : '';
        }
        $cmds = implode(' | ', $cmds);
        $cmd = "ps auwwx | grep '$ps_cmd' | $cmds | wc -l";
        return intval(self::runCmd($cmd));
    }

    /**
     * 杀掉进程
     * @param $ps_cmd
     * @return string
     */
    public static function kill_ps($ps_cmd){
        $cmd = 'ps auwwx | grep "'.$ps_cmd.'"| grep -v grep | grep -v \'/bin/sh\' | awk  \'{print $2}\' | xargs kill -9';
        return self::runCmd($cmd);
    }
}