<?php
class CSRF extends Library{
	function __construct(){
		parent::__construct();
	}
	
    public static function CreateToken()
    {
        //Generate token
        $token = md5(time()); 
        
        //Save in Session
        $_SESSION["token"] = $token;

        //Creating hidden field
        echo "<input type='hidden' name ='token' value='$token'/>";
    }

    public static function ValidateToken($token)
    {
        $validate = false;

        if ( $token == $_SESSION['token'] ){
            $validate = true;          
        }

        return $validate;
    }

    //     if(empty($token)){
    //         // $param['msg']='CSRF token is missing';
    //         // Session::set('Error',$param['msg']);
    //         // $this->render_partial('Login/Index');

    //     }
    //     elseif ( $token != $_SESSION['token'] ){
    //         // $param['msg']='Incorrect CSRF Token';
    //         // Session::set('Error',$param['msg']);
    //         // $this->render_partial('Login/Index');
           
    //     }else{
    //         $validate = true;

    //     }
    //     return $validate;
    // }


}