<?php
class Core{
	public function model($file){
		if(file_exists('App/Models/'.$file.'Model.php')){
			require_once('App/Models/'.$file.'Model.php');
			$classname=$file.'Model';
			$model=new $classname();
			return $model;
		}
		else{
			echo 'Unable to Model File '.$file.'Model.php';
			exit;
		}

	}

	public function library($file){
        if(file_exists('Libraries/'.$file.'Library.php')){
            require_once('Libraries/'.$file.'Library.php');
        }
        else if(file_exists('System/Libraries/'.$file.'Library.php')){
            require_once('System/Libraries/'.$file.'Library.php');
        }
        else{
            echo 'Unable to Library File '.$file.'Library.php';
            exit;
        }

    }

	public function helper($file){
		if(file_exists('Helpers/'.$file.'Helper.php')){
			require_once('Helpers/'.$file.'Helper.php');
		}
		else if(file_exists('System/Helpers/'.$file.'Helper.php')){
			require_once('System/Helpers/'.$file.'Helper.php');
		}
		else{
			echo 'Unable to Helper File '.$file.'Helper.php';
			exit;
		}
	}
}
