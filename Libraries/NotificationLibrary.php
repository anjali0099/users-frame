<?php
class Notification extends Library{

	public $notification_model;
	private  $auth;
	public function __construct($connid=1)
	{
		parent::__construct();
		$this->notification_model=new Model('notifications' ,$connid);
		$this->auth=Session::get('Auth');
	}

	public function AddNotification($data){
		//$data['module_id']=get_module_id();
		//$data['notification_date']=date('Y-m-d H:i:s');
		$rs=$this->notification_model->insert($data);
		if($rs=='OK'){
			return 'notification added';
		}
		else{
			return $rs['Data'];
		}
	}

	public function AdminNotification($msg,$url){
		$company=new Model('companies',1);
		$admin=$company->where('flag',1)->get_single();
		//print_r($admin);
		$data['target']=$admin['Data']['unique_company_identifier'];
		$data['is_seen']=0;
		$data['notification_date']=date('Y-m-d H:i:s');
		// $data['module_id']=get_module_id();
		$data['msg']=$msg;
		$data['url']=$url;
		$this->AddNotification($data);
	}

	public function PushNotification($msg,$url,$target){
		$data['target']=$target;
		$data['is_seen']=0;
		$data['notification_date']=date('Y-m-d H:i:s');
		$data['module_id']=get_module_id();
		$data['msg']=$msg;
		$data['url']=$url;
		$this->AddNotification($data);
	}

	public function  ReceiveNotification($id){
		$module=new Model('modules',1);


		$notif=$this->notification_model->where('id',$id)->get_single();
		if($notif['Status']=='OK' && !empty($notif['Data'])){
			$module=$module->where('id',$notif['Data']['module_id'])->get_single();

			if($module['Status']=='OK' && !empty($module['Data'])){

				$url='http://'.$module['Data']['url'].$notif['Data']['url'];

				$this->notification_model->where('id',$id)->update(array('is_seen'=>1));
				header("Location: $url");
				return true;
			}
			else{

				return false;
			}


		}
		else{
			return false;
		}

	}
	public function  GetNotification(){
		$moduleid=get_module_id();
		$rs = $this->notification_model
		->where('module_id',$moduleid)
		->in_where('target',array($this->auth['User']['unique_user_id'],$this->auth['Company']['unique_company_identifier']))
		->order_by('notification_date')
		->get_all();
		//print_r($rs);
		if($rs['Status']=='OK'){
			return $rs['Data'];
		}
		else{
			return 'No notifications';
		}

	}

	public function GetUnreadNotification(){
		$moduleid=get_module_id();
		$rs = $this->notification_model
		->where('module_id',$moduleid)
		->in_where('target',array($this->auth['User']['unique_user_id'],$this->auth['Company']['unique_company_identifier']))
		->order_by('notification_date')
		->where('is_seen',0)
		->limit(10)
		->get_all();
		//print_r($rs);
		if($rs['Status']=='OK'){
			return $this->RenderNotification( $rs['Data']);
		}
		else{
			return 'No notifications';
		}
	}

	public function GetUnreadNotificationCount(){
		$moduleid=get_module_id();
		$rs=$this->notification_model
		->where('is_seen',0)
		->where('module_id',$moduleid)->in_where('target',array($this->auth['User']['unique_user_id'],$this->auth['Company']['unique_company_identifier']))
		->select('count(*) as Total')
		->get_single();


		if($rs['Status']=='OK' && !empty($rs['Data'])){
			return $rs['Data']['Total'];
		}

	}

	public function GetNewUnreadNotification($date){
		$moduleid=get_module_id();
		$rs = $this->notification_model
		->where('module_id',$moduleid)
		->in_where('target',array($this->auth['User']['unique_user_id'],$this->auth['Company']['unique_company_identifier']))
		->order_by('notification_date')
		->where('notification_date',$date,'>=')
		->where('is_seen',0)
		->get_all();

		//print_r($rs);exit;

		if($rs['Status']=='OK'){
			return $this->RenderNotification( $rs['Data']);
		}
		else{
			return 'No notifications';
		}
	}


	function RenderNotification($data){
		return $this->set_template('Notification/List',array('Data'=>$data));
	}

	function set_template($viewname,$data= array()){
		//$viewpath=BASE_DIR.'/App/Views/'.ucfirst($viewname).'View.php';
		$viewpath=base_dir().'/App/Views/'.ucfirst($viewname).'View.php';
		extract($data);

		ob_start();
		include( $viewpath);
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}


}
