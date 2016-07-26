<?php
class T_Crypt{
	static public function decrypto($encrypt, $is_base64=false){
   		$encrypt = utf8_decode($encrypt);
        if($is_base64)
		    $encrypt =  base64_decode($encrypt);
	   	$len = strlen($encrypt);
	   	$key = substr($encrypt, 0, 4).substr($encrypt, $len-4,4);
	   	$encrypt = substr($encrypt, 6, $len-10);
	   	$oCrypto = P_Crypt_To::instance();
	   	$oCrypto->init($key);
	   	$decrpyt = $oCrypto->decrypt($encrypt);
	   
	   	return $decrpyt;
	}
	
	static public function encrypto($data, $is_base64=false){
		if(!isset($data[0]))
			return '';
		$key = self::get_key(8);
        $oCrypto = P_Crypt_To::instance();
		$oCrypto->init($key);
		$encrypt = $oCrypto->encrypt($data);
		$encrypt = substr($key, 0, 6).$encrypt.substr($key, 4, 4);
        if($is_base64)
		    $encrypt = base64_encode($encrypt);
		$encrypt = utf8_encode($encrypt);
		return $encrypt;
	}

	static public function get_key($len){
        $chars = '12345678abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		return T_String::random($len, $chars);
	}

}
