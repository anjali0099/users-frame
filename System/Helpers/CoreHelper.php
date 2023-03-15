<?php
function helper(){
	print_r('helper Loader');

}
/*function geturi(){
	$uriblock=$_SERVER['REQUEST_URI'];
	$explodeduri=array_values(array_filter(explode('/',$uriblock)));
	//print_r($explodeduri);exit;
	return $explodeduri[0];
}*/
function base_url(){
	//$uri=geturi();
	global $config;
	if(!isset($config['BASE_URL'])){
		echo 'config file not set Exiting!!!!!!!';
	}
	return 'http://'.$config['BASE_URL'];
}
function base_dir(){
	/*$uri=geturi();

	return $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.$uri;*/
	global $config;
	if(!isset($config['BASE_DIR'])){
		echo 'config file not set Exiting!!!!!!!';
	}
	return $config['BASE_DIR'];
}

function dd($var='here'){
	var_dump($var);
	die();
}

function assets_path(){
	global $config;
	$uri='http://'.$config['BASE_URL'].'Assets/Templates/'.$config['ProjectTemplate'].'/';
	return $uri;

}

function assets_dir(){
	global $config;
	$uri=$config['BASE_DIR'].'Assets/Templates/'.$config['ProjectTemplate'].'/';
	return $uri;

}

function vendor_path(){
	global $config;
	$uri='http://'.$config['BASE_URL'].'Vendors/';
	return $uri;

}

function vendor_dir(){
	global $config;
	$uri=$config['BASE_DIR'].'Vendors/';
	return $uri;

}


function redirect($url){
	$uri='';
	if(strpos($url, "http://")) {
		$uri = $url;
	}else{
		$uri=base_url().$url;
	}

	header("Location: $uri");

}

function unique_id($prefix='Z',$length=4){
	$strpool='ABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890123456789';
	$uniq_parts=array();
	for($i=0; $i<$length ;$i++){
		$uniq_parts[$i]='';
		for($j = 0; $j < 5; $j++) {
				$uniq_parts[$i] .= $strpool[rand(0, strlen($strpool)-1)];
		}
	}
	$uid=$prefix.implode('-',$uniq_parts);
	return $uid;

}

function array_flatten($array) {
  if (!is_array($array)) {
    return FALSE;
  }
  $result = array();
  foreach ($array as $key => $value) {
    if (is_array($value)) {
      $result = array_merge($result, array_flatten($value));
    }
    else {
      $result[$key] = $value;
    }
  }
  return $result;
}

function execInBackground($cmd) {
	 if (substr(php_uname(), 0, 7) == "Windows"){
			 pclose(popen("start /B ". $cmd, "r"));
	 }
	 else {
			 exec($cmd . " > /dev/null &");
	 }
}


function convert_to_server_datetime($date, $timezone){
	$tz=date_default_timezone_get();

	$serverTimezone = new DateTimeZone($tz);
	$utz=$timezone;
	$userTimezone = new DateTimeZone($utz);
	$date = new DateTime($date, $userTimezone);
	$date->setTimezone($serverTimezone);

	return $date;
 }
