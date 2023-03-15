<?php
function get_api_key($url=''){
	/*$module = new Model('modules',1);
	$app_info=$module->where('url',$url)->select('api_key')->get_single();
	if(!empty($app_info['Data']))
	{
		return $app_info['Data']['api_key'];
	}
	else{
		return '';
	}*/
  return 'P38A07-3O8AL-MTHJ5';
}

function get_module_id($apikey=''){
  if($apikey==''){
    $apikey=get_api_key();
  }
	$module = new Model('modules');
	$app_info=$module->where('api_key',$apikey)->select('id')->get_single();
	if(!empty($app_info['Data']))
	{
		return $app_info['Data']['id'];
	}
	else{
		return '';
	}

}

function check_api_key($apikey){
	$module = new Model('modules');
	$app_info=$module->where('api_key',$apikey)->select('api_key')->get_single();
	if($app_info['Status']=='OK' && !empty($app_info['Data']))
	{
		return true;
	}
	else{
		return false;
	}
}


function get_control_panel_url(){
	$module = new Model('modules');
	$app_info=$module->where('id',1)->select('url')->get_single();
	if($app_info['Status']=='OK' && !empty($app_info['Data']))
	{
		return 'http://'.$app_info['Data']['url'];
	}
	else{
		return false;
	}
}
