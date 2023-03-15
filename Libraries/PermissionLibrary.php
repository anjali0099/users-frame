<?php
class Permission extends Library{
  
	function __construct(){
		parent::__construct();

	}

  public static function GetPermissionDetail($id){
    $Model= new Model('permissions',1);
    $result=$Model->where('id',$id)->get_single();
    return $result['Data'];
  }

  public static function CheckPermission($class,$permission_name){
	 
	$permission = false;
	$auth=Session::get('Auth');
	if($auth['User']['flag'] == 1)
	{
		
		$permission = true;
	}
	else{
		 
		$model = new Model('user_roles',1);
	
		$roles = $model->where('unique_user_id', $auth['User']['unique_user_id'])->get_all();
		foreach($roles['Data'] as $r)
		{
			 
			$sql= "SELECT r.role_name, p.`permission`, c.`group_name` 
					FROM `role_permissions` rp 
					JOIN  controller_permissions cp 
						ON cp.id = rp.`controller_permission_id` 
					JOIN roles r 
						ON r.id = rp.`role_id` 
					LEFT JOIN permissions p 
						ON cp.`permission_id` = p.id
					LEFT JOIN controllers c
						ON cp.`controller_id` = c.id
				
				WHERE r.id = ".$r['role_id']
				
				. " And c.group_name = '".$class."'"
				. " And p.permission = '".$permission_name."'"
				
				;
				// echo($class);
				// echo($permission_name);
			$dataset=$model->raw_sql($sql)->execute();
			// print_r($dataset);exit;
			
			
			 
			if($dataset['Status'] =='OK' && !empty($dataset['Data'])){
				$permission = true;
			}
		}
	}

    return $permission;

  }

}
