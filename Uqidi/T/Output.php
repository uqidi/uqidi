<?php
/**
 * @fileoverview:   Output
 * @author:         Uqidi
 * @date:           2015-11-21
 * @copyright:      Uqidi
 */
class T_Output{


    /**
     * 返回错误
     * @param $status
     * @param $reason
     * @return array
     */
    static public function return_error($status, $reason=''){
        return array('status'=>$status, 'reason'=>$reason);
    }
    /**
     * 错误输出
     * @param $code
     * @param string $msg
     */
    static public function error($code, $msg=''){
        $errors = array(
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            500 => 'Internal Server Error',
            503 => 'Internal Server Error',
        );
        $error = isset($errors[$code]) ? $code.' '.$errors[$code] : $code;
        header("http/1.1 $error");
        header("Status: $error");
        echo $msg;
        exit;
    }

    /**
     * 数据输出
     * @param $data
     * @param string $format
     * @param string $varname
     * @param string $jsonp
     * @param bool $is_exit
     */
    static public function output($data , $format = '' , $varname = '' , $jsonp = '', $is_exit=true){
        $format = strtoupper($format);

        if(empty($format) && isset($_REQUEST['callback'][0])){
            $format = 'JSONP';
            $jsonp = trim($_REQUEST['callback']);
        }

        if (!in_array($format, array('JSON', 'JSONP', 'XML', 'PHP'))){
            $format = 'JSON';
        }

        if ('JSON' == $format || 'JSONP' == $format){
            if (isset($_GET['domain']) && $_GET['domain'] == 1){
                echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . "\n";
                echo '<script type="text/javascript">document.domain=".youku.com";</script>' . "\n";
            }

            if ('' != $varname){
                $varname = str_replace(array("<",">"),"",$varname);
                echo $varname . '=' . json_encode($data);
            }elseif(preg_match('/^[0-9a-zA-Z-_]{1,}$/' , $jsonp)){
                echo $jsonp . '(' . json_encode($data) . ')';
            }else{
                T_Header::set_type();
                echo json_encode($data);
            }
        }elseif ('XML' == $format){
            echo self::_xml_encode($data) ;
        }elseif('PHP' == $format){
            echo serialize($data);
        }
        if($is_exit)
            exit;
    }

    static private function _xml_encode($data, $encoding = 'utf-8', $root = "root"){
        $xml = "<?xml version=\"1.0\" encoding=\"" . $encoding . "\"?>\n";
        $xml .= "<{$root}>\n";
        $xml .= self::_data_to_xml($data);
        $xml .= "</{$root}>";
        return $xml;
    }

    static private function _data_to_xml($data){
        if (is_object($data)){
            $data = get_object_vars($data);
        }
        $xml = '';
        foreach($data as $key => $val){
            is_numeric($key) && $key = "item id=\"$key\"";
            $xml .= "<$key>";
            if (is_array($val) || is_object($val)){
                $xml .= "\n" . self::_data_to_xml($val);
            }else{
                $xml .= in_array($key, array('category', 'title', 'memo', 'tag')) ? "<![CDATA[" . $val . "]]>" : $val;
            }
            list($key,) = explode(' ', $key);
            $xml .= "</$key>\n";
        }
        return $xml;
    }
}