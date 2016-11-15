<?php
/**
 * @fileoverview:   T_WebSocket
 * @author:         Uqidi
 * @date:           2015-11-21
 * @copyright:      Uqidi
 */
class T_WebSocket {
    public static function send($host, $type, $action, $data){
        list($host, $port) = explode(':', $host, 2);
        for($i=0; $i<3; $i++){
            $ws = Lib_wsclient::instance($host, $port);
            if(false !== $ws){
                break;
            }
            T_Logger::monitorLog(__CLASS__, __FUNCTION__.':init:'.json_decode($ws->get_error()), T_Logger::LOG_LEVEL_ERROR);
        }

        if(false === $ws){
            return false;
        }

        $send_data['type'] = $type;
        $send_data['action'] = $action;
        $send_data['lolink_key'] = Config_msg::LIVE_LGLINK_KEY;
        $send_data = array_merge($send_data, $data);
        for($i=0; $i<3; $i++){
            $rs = $ws->send(json_encode($send_data));
            if($rs !== false){
                T_Logger::debugLog(__CLASS__, __FUNCTION__.':send:success'.var_export($send_data, true));
                break;
            }
            T_Logger::monitorLog(__CLASS__, __FUNCTION__.':send:'.json_decode($ws->get_error()), T_Logger::LOG_LEVEL_ERROR);
        }
        $rs = $ws->close();
        if($rs === false){
            T_Logger::monitorLog(__CLASS__, __FUNCTION__.':close'.json_decode($ws->get_error()), T_Logger::LOG_LEVEL_ERROR);
            return false;
        }
        return true;
    }
} 