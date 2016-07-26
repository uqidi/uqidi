<?php
/**
 * @fileoverview:   Wsclient
 * @author:         Uqidi
 * @date:           2015-11-15
 * @copyright:      Uqidi.com
 */
class P_Wsclient{
    private $_host    = '';     /* where is the websocket server */
    private $_port    = '';     /* where is the websocket server port */
    private $_local   = '';     /* url where this script run */
    private $_key = null;       /* current socket key */
    private $socket  = null;    /* current socket */

    static private $_insts = array();

    /* 错误参数 */
    private  $_error   = array(
        'code'  => 0,
        'msg'   => '',
    );

    static public function instance($host, $port, $local = ""){
        if(empty($local))
            $local = SITE_URL;

        $key= md5($host.':'.$port.':'.$local);
        if (!isset(self::$_insts[$key])) {
            $socket = new self($host, $port, $local);
            if(false === $socket)
                return false;
            $socket->set_key($key);
            self::$_insts[$key] = $socket;
        }
        return self::$_insts[$key];
    }

    public  function __construct($host, $port, $local = ""){
        $this->_host = $host;
        $this->_port = $port;
        $this->_local = $local ;
        return $this->connect();
    }

    public function get_error(){
        return $this->_error;
    }

    public function __destruct(){
        if(is_resource($this->socket)){
            $this->close();
        }
    }

    public function set_key($key){
        $this->_key = $key;
    }

    public function get_key(){
        return $this->_key;
    }

    public function connect(){
        $key = base64_encode($this->_generateRandomString(16, false, true));
        $head = "GET / HTTP/1.1"."\r\n".
            "Upgrade: WebSocket"."\r\n".
            "Connection: Upgrade"."\r\n".
            "Origin: $this->_local"."\r\n".
            "Host: $this->_host"."\r\n".
            "Sec-WebSocket-Key: ".$key."\r\n".
            "Sec-WebSocket-Version: 13"."\r\n".
            "\r\n";
        $this->socket = fsockopen($this->_host, $this->_port, $this->_error['code'], $this->_error['msg'], 10);
        if(!$this->socket){
            $this->_error = array(
                'code'  => -1,
                'msg'   => 'connect error',
            );
            return false;
        }
        socket_set_timeout($this->socket, 0, 20000);
        $rs = @fwrite($this->socket, $head);
        if(false === $rs){
            $this->_error = array(
                'code'  => -1,
                'msg'   => 'connect fwrite error',
            );
            return false;
        }
        $response = @fread($this->socket, 1500);
        if(false === $response){
            $this->_error = array(
                'code'  => -1,
                'msg'   => 'connect response error',
            );
            return false;
        }

        preg_match('#Sec-WebSocket-Accept:\s(.*)$#mU', $response, $matches);
        if ($matches) {
            $keyAccept = trim($matches[1]);
            $expectedResonse = base64_encode(pack('H*', sha1($key . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
            $rs = ($keyAccept === $expectedResonse) ? true : false;
        }
        if(false === $rs){
            $this->_error = array(
                'code'  => -1,
                'msg'   => 'connect Sec-WebSocket-Accept error',
            );
            return false;
        }
        return true;
    }

    public function send($data){
        $rs = fwrite($this->socket, $this->hybi10Encode($data));
        if(false === $rs){
            $this->_error = array(
                'code'  => -2,
                'msg'   => 'send fwrite error',
            );
            return false;
        };
        $rs = fread($this->socket, 1500);

        if(false === $rs){
            $this->_error = array(
                'code'  => -2,
                'msg'   => 'send fread error',
            );
            return false;
        }
        return true;

    }

    public function close(){
        $rs = fclose($this->socket);

        if(false === $rs){
            $this->_error = array(
                'code'  => -3,
                'msg'   => 'close fclose error',
            );
            return false;
        }
        unset(self::$_insts[$this->_key]);
        return $rs;
    }

    function hybi10Decode($data){
        $bytes = $data;
        $dataLength ='';
        $mask = '';
        $coded_data = '';
        $decodedData = '';
        $secondByte = sprintf('%08b', ord($bytes[1]));
        $masked = ($secondByte[0] == '1') ? true : false;
        $dataLength = ($masked === true) ? ord($bytes[1]) & 127 : ord($bytes[1]);

        if($masked === true){
            if($dataLength === 126){
                $mask = substr($bytes, 4, 4);
                $coded_data = substr($bytes, 8);
            }elseif($dataLength === 127){
                $mask = substr($bytes, 10, 4);
                $coded_data = substr($bytes, 14);
            }else{
                $mask = substr($bytes, 2, 4);
                $coded_data = substr($bytes, 6);
            }
            for($i = 0; $i < strlen($coded_data); $i++){
                $decodedData .= $coded_data[$i] ^ $mask[$i % 4];
            }
        }else{
            if($dataLength === 126){
                $decodedData = substr($bytes, 4);
            }elseif($dataLength === 127){
                $decodedData = substr($bytes, 10);
            }else{
                $decodedData = substr($bytes, 2);
            }
        }
        return $decodedData;
    }

    function hybi10Encode($payload, $type = 'text', $masked = true) {
        $frameHead = array();
        $frame = '';
        $payloadLength = strlen($payload);
        switch ($type) {
            case 'text':
                /* first byte indicates FIN, Text-Frame (10000001): */
                $frameHead[0] = 129;
                break;

            case 'close':
                /* first byte indicates FIN, Close Frame(10001000): */
                $frameHead[0] = 136;
                break;

            case 'ping':
                /* first byte indicates FIN, Ping frame (10001001): */
                $frameHead[0] = 137;
                break;

            case 'pong':
                /* first byte indicates FIN, Pong frame (10001010): */
                $frameHead[0] = 138;
                break;
        }

        /* set mask and payload length (using 1, 3 or 9 bytes) */
        if ($payloadLength > 65535) {
            $payloadLengthBin = str_split(sprintf('%064b', $payloadLength), 8);
            $frameHead[1] = ($masked === true) ? 255 : 127;
            for ($i = 0; $i < 8; $i++) {
                $frameHead[$i + 2] = bindec($payloadLengthBin[$i]);
            }

            /* most significant bit MUST be 0 (close connection if frame too big) */
            if ($frameHead[2] > 127) {
                $this->close(1004);
                return false;
            }
        } elseif ($payloadLength > 125) {
            $payloadLengthBin = str_split(sprintf('%016b', $payloadLength), 8);
            $frameHead[1] = ($masked === true) ? 254 : 126;
            $frameHead[2] = bindec($payloadLengthBin[0]);
            $frameHead[3] = bindec($payloadLengthBin[1]);
        } else {
            $frameHead[1] = ($masked === true) ? $payloadLength + 128 : $payloadLength;
        }

        /* convert frame-head to string: */
        foreach (array_keys($frameHead) as $i) {
            $frameHead[$i] = chr($frameHead[$i]);
        }

        /* generate a random mask: */
        if ($masked === true) {
            $mask = array();
            for ($i = 0; $i < 4; $i++) {
                $mask[$i] = chr(rand(0, 255));
            }

            $frameHead = array_merge($frameHead, $mask);
        }
        $frame = implode('', $frameHead);

        /* append payload to frame: */
        for ($i = 0; $i < $payloadLength; $i++) {
            $frame .= ($masked === true) ? $payload[$i] ^ $mask[$i % 4] : $payload[$i];
        }

        return $frame;
    }

    private function _generateRandomString($length = 10, $addSpaces = true, $addNumbers = true){
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!"§$%&/()=[]{}';
        $useChars = array();
        /* select some random chars */
        for($i = 0; $i < $length; $i++)
        {
            $useChars[] = $characters[mt_rand(0, strlen($characters)-1)];
        }

        /* add spaces and numbers */
        if($addSpaces === true)
        {
            array_push($useChars, ' ', ' ', ' ', ' ', ' ', ' ');
        }
        if($addNumbers === true)
        {
            array_push($useChars, rand(0,9), rand(0,9), rand(0,9));
        }
        shuffle($useChars);
        $randomString = trim(implode('', $useChars));
        $randomString = substr($randomString, 0, $length);
        return $randomString;
    }
}