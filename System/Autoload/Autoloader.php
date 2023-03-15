<?php

class Autoloader{

	function __construct(){


	}

	function classes(){
		global $classes;
		//$content = array();
		return $classes;
	}

	function loader(){
		foreach (glob("System/Core/*.php") as $filename)
		{
			require_once($filename);
		}
		/*foreach (glob("System/Helpers/*.php") as $filename)
		{
			require_once($filename);
		}*/
		require_once('System/Helpers/CoreHelper.php');
	
		$list=$this->classes();
		if(!empty($list))
		{
			foreach ($list as $filename)
			{
				$loaded=0;
				if(file_exists('System/Core/'.$filename.'.php')){
					require_once('System/Core/'.$filename.'.php');
					$loaded=1;
				}

				if(file_exists('System/Libraries/'.$filename.'Library.php')){
					require_once('System/Libraries/'.$filename.'Library.php');
					$loaded=1;
				}
				else if(file_exists('Libraries/'.$filename.'Library.php')){
					require_once('Libraries/'.$filename.'Library.php');
					$loaded=1;
				}

				if(file_exists('Helpers/'.$filename.'Helper.php')){
					require_once('Helpers/'.$filename.'Helper.php');
					$loaded=1;
				}

				if(file_exists('System/Helpers/'.$filename.'Helper.php')){
					require_once('System/Helpers/'.$filename.'Helper.php');
					$loaded=1;
				}

				if(file_exists('App/Models/'.$filename.'Model.php')){
					require_once('App/Models/'.$filename.'Model.php');
					$loaded=1;
				}

				if($loaded==0){
					echo 'unable to file '.$filename.' in models , helpers and libraries';
					exit;
				}

			}
		}
	}

}
