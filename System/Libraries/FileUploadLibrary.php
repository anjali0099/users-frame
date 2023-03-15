<?php
class FileUpload extends Library{
	private $status;
	public $allow_type;
	public $max_size;
	public function __construct()
	{
		parent::__construct();
	}

	public function Upload($fieldname,$allow_type='',$max_size='',$targetDir = "Uploads/")
	{

		// create directory if not exists in upload/ directory
		if(!is_dir($targetDir)){
			mkdir($targetDir, 0775);
		}
		//
		$uploadok=1;
		if($allow_type!=''){
			if(strpos($allow_type,',')){
				$allow=explode(',',$allow_type);
				if(in_array(pathinfo($_FILES[$fieldname]["name"],PATHINFO_EXTENSION), $allow))
				{
					$this->resultset['Status']='Error' ;
					$this->resultset['Data']='Invalid File Type file type should be of '.$allow_type;
					$uploadok=0;
				}
			}
			else{
				if(pathinfo($_FILES[$fieldname]["name"],PATHINFO_EXTENSION) != $allow_type)
				{
					$this->resultset['Status']='Error' ;
					$this->resultset['Data']='Invalid File Type file type should be of '.$allow_type;
					$uploadok=0;
				}
			}

		}
		if($max_size!=''){
			if(($_FILES[$fieldname]['size']> $max_size)){
				$this->resultset['Status']='Error' ;
				$this->resultset['Data']='File size too large should be less than '.($max_size).'KB';
				$uploadok=0;
			}
		}

		if($uploadok==1){
			$newfilename=time().rand(1,999).'.'.pathinfo($_FILES[$fieldname]["name"],PATHINFO_EXTENSION);
			if(move_uploaded_file($_FILES[$fieldname]["tmp_name"], $targetDir.$newfilename)){
				$this->resultset['Status']='OK' ;
				$this->resultset['Data']= $targetDir.$newfilename;
			}
			else
			{
				$this->resultset['Status']='Error' ;
				$this->resultset['Data']= 'File Couldn\'t be Uploaded';
			}
		}
		return $this->resultset;
	}

}
