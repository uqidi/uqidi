<?php
/**
 * @fileoverview:   Http
 * rsync -avz --progress --password-file=rsyncd.passwd  dest_path   www@127.0.0.1::data
 * @author:         Uqidi
 * @date:           2016-11-21
 * @copyright:      uqidi.com
 */
class T_Rsync{
    private  static function _exec($path, $dir, $password_file, $user, $host, $module){
        $cmd = "cd $dir \n";
        $cmd .= 'rsync -Ravz --progress';
        $cmd .= ' --password-file='.$password_file;
        $cmd .= ' '.$path;
        $cmd .= ' '.$user.'@'.$host.'::'.$module;
        $rs = T_Cmd::runCmd($cmd);
        T_Logger::monitorLog(__CLASS__, $cmd.'::'.$rs, T_Logger::LOG_LEVEL_NOTICE);
        return $rs;
    }

    public static function send($path, $module='data'){
        $flag = true;
        $servers = Loader::loadConfig('rsync');
        $servers = $servers[$module];
        foreach($servers as $server){
            $rs = self::_exec($path, SYS_DATA_PATH, $server['password_file'], $server['user'], $server['host'], $server['module']);
            if(!$rs){
                $flag = false;
            }
        }
        return $flag;
    }

    public static function send_php($file, $module='data'){
        $explode = explode('_', $file);
        $file_name = array_pop($explode);
        $dir = '';
        if(!empty($explode)){
            $dir = implode('/', $explode).'/';
        }
        $file_name = $file_name.'.php';

        $file_path = $dir.$file_name;
        return self::send($file_path, $module);
    }
}