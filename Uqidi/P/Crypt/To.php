<?php
class P_Crypt_To{
    private static $_Inst = null;
	private $key = NULL;
    private $iv = NULL;
    private $iv_size = NULL;

    static public function instance(){
        if(!self::$_Inst)
            self::$_Inst = new self();
        return self::$_Inst;
    }

	public function init($key=''){
		$this->key = ($key != "") ? $key : "";

		$this->algorithm = MCRYPT_DES;
		$this->mode = MCRYPT_MODE_ECB;

		$this->iv_size = mcrypt_get_iv_size($this->algorithm, $this->mode);
		$this->iv = mcrypt_create_iv($this->iv_size, MCRYPT_RAND);

		return true;
	}

	public function encrypt($data){
		$size = mcrypt_get_block_size($this->algorithm, $this->mode);
		$data = $this->pkcs5_pad($data, $size);
		return base64_encode(mcrypt_encrypt($this->algorithm, $this->key, $data, $this->mode, $this->iv));
	}

    public function decrypt($data){
		return $this->pkcs5_unpad(rtrim(mcrypt_decrypt($this->algorithm, $this->key, base64_decode($data), $this->mode, $this->iv)));
	}

    public function pkcs5_pad($text, $blocksize){
		$pad = $blocksize - (strlen($text) % $blocksize);
		return $text . str_repeat(chr($pad), $pad);
	}

    public function pkcs5_unpad($text){
		$pad = ord($text{strlen($text)-1});
		if ($pad > strlen($text)) return false;
		if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) 
			return false;
 		return substr($text, 0, -1 * $pad);
	}
}
