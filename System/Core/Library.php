<?php
class Library {
	private $config='';
	public $load;
	function __construct(){
		global $config;
		$this->config = $config;
		$this->load = new Core();
	}
}