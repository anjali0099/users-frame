<?php
class Session extends Library{
	
	public function __construct($mode='')
	{
		parent::__construct();
	}
	
	public static function get($key){
		if(isset($_SESSION[$key]))
			return $_SESSION[$key];
		else
			return '';
	}
	
	public static function set($key,$value){
		$_SESSION[$key] = $value;
	}
	
	public static function remove($key){
		if(isset($_SESSION[$key])){
			unset($_SESSION[$key]);
		}
	}
	
	public static function end_session(){
		
		session_unset();
		session_destroy();
	}
	
	public static function check_session(){
		if(!isset($_SESSION) || empty($_SESSION)){
			redirect('Login');
		}
	}
}