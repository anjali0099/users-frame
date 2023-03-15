<?php
class Encrypt extends Library{
	private $returnstr;
	private $model;
	private $settings;
	function __construct(){
		parent::__construct();
		$this->model=new Model('encryption_settings');
		$d=$this->model->where('id',1)->get_single();
		$this->settings=$d['Data'];

		if(empty($this->settings)){
			$data['id']='1';
			$data['password']=unique_id('E',7);
			$data['salt']=unique_id('S',4);
			$data['iv']=unique_id('I2Y$-',2);
			$data['iteration']=1000;
			$data['key_length']=32;
			$this->model->insert($data);

			$this->settings=$data;
		}
	}
	function PasswordEncrypt($string,$salt=''){
		$salt = ($salt=='')?unique_id('S','4'):$salt;

		$options = [
			'cost' => 10,
			'salt'=>$salt
		];
		$encString=password_hash($string, PASSWORD_BCRYPT, $options);
		$this->returnstr['Salt']=$salt;
		$this->returnstr['EncryptedString']=$encString;
		return $this->returnstr;
	}


	function Encrypt($plaintext){
		$ciphertext_b64 = "";
		extract($this->settings);
		$prepared_key = openssl_pbkdf2($password, $salt, $key_length, $iteration, "sha256");
		$ciphertext_b64 = base64_encode(openssl_encrypt($plaintext,"AES-256-CBC", $prepared_key,OPENSSL_RAW_DATA, $iv));
		return $ciphertext_b64;


	}

	function Decrypt($ciphertext_b64){
		extract($this->settings);
		$prepared_key = openssl_pbkdf2($password, $salt, $key_length, $iteration, "sha256");
		$plaintext = openssl_decrypt(base64_decode($ciphertext_b64),"AES-256-CBC", $prepared_key,OPENSSL_RAW_DATA, $iv);
		return $plaintext;
	}
}
