<?php


class UserController extends Controller
{
    private $users;
    private $company;
    private $model_user;

    function __construct()
    {
        parent::__construct();
        $this->users = new Model('users');
        $this->company = new Model('company');
        $this->userlogs = new Model('userlogs');
        $this->model_user = $this->load->Model('Users');
        $this->model_log = $this->load->Model('Log');
    }

    public function index()
    {

        // $this->model_user->test_model();

        if (!isset( $_SESSION['Auth']['User']['email'] ) || empty( $_SESSION['Auth']['User']['email'] )) {
            redirect('Log');
        }

        // date_default_timezone_set("Asia/Kathmandu");
        // $time=600;
        // if (time()-$_SESSION['loggedin_time']>$time)
        // {
        //     $userId = $_SESSION['Auth']['User']['userid'];
        //     $postdata['logouttime'] = date("h:i:s");
        //     $postdata['date'] = date("Y-m-d");
        //     $postdata['userflag'] = '0';
        //     $current_date = date("Y-m-d");
      
        //     $check = $this->userlogs->select('login_out,count(logouttime) as logouttoday')->where('userId',$userId)->where('date',$current_date, '>=')->get_single();
        //     $logoutcheck = $check['Data']['logouttoday'];
        //     if($logoutcheck>0)
        //     {
        //       $logoutcheck = $check['Data']['login_out'];
        //       $array_data = json_decode($logoutcheck);
      
        //       $logout_data = explode("_",end($array_data));
        //       $newlogin = $logout_data[0];
        //       $logout_new = $newlogin."_|".'logout|'.date("h:i:s");
        //       $result_array = array_merge($array_data,array($logout_new));
      
        //       $final_data = json_encode($result_array);
      
        //       $postdata['login_out'] =  $final_data;
        //     }
        //     else
        //     {
        //       $logout =  '1_|logout|'.date("h:i:s");
        //       $logoutcheck = $check['Data']['login_out'];
        //       $array_data = json_decode($logoutcheck);
        //       $result_array = array_merge($array_data,array($logout));
      
        //       $postdata['login_out'] = json_encode($result_array);
        //     }
      
        //     $result = $this->userlogs->where('userId',$userId)->where('date',$postdata['date'])->update($postdata);
          
        //     $_SESSION['Error'] = "Session Expired";
        //     session_destroy();
        //     session_unset();            
        //     redirect('Log');   
        // }
        // else
        // {
        //     $_SESSION['loggedin_time']=time();
        // }

        if(isset( $_SESSION['Auth']['User']['email'] ) || !empty( $_SESSION['Auth']['User']['email']))
        {
            $email = $_SESSION['Auth']['User']['email'];
            $firstname = $_SESSION['Auth']['User']['name'];
            $userId = $_SESSION['Auth']['User']['userid'];
            $file = "Assets/document_$firstname.txt";
            if(!file_exists($file))
            {
                $contents = array();
                $contents["name"] = $firstname;
                $contents["email"] = $email;
               
                $filecontent = json_encode( $contents);
                $postdata['userinfo'] = $filecontent;
                $userinfo = $this->userlogs->where('userId',$userId)->update($postdata);
                file_put_contents($file,$filecontent);
            }
            else
            {
                $contents = array();
                $contents["name"] = $firstname;
                $contents["email"] = $email;
                $current_date = date("Y-m-d");
               
                $filecontent = json_encode( $contents);
                $postdata['userinfo'] = $filecontent;
                $userinfo = $this->userlogs->where('userId',$userId)->where('date',$current_date, '>=')->update($postdata);
                file_put_contents($file,$filecontent);

            }

        }
        $user = $this->model_user->viewindex_data();
        
        $param['user'] = $user['Data'];

        $this->render('User/Index', $param);
    }

    
    //create
    public function create()
    {
        // dd($_POST);
        if(empty($_POST))
        {
            redirect ('users');
            $_SESSION['Success'] = "Empty post";
        }
        else
        {
            $postdata = array();
            $postcompany = array();

            $postdata['firstname'] = $_POST['firstname'];
            $postdata['lastname'] = $_POST['lastname'];
            $postdata['email'] = $_POST['email'];
            $postdata['address'] = $_POST['address'];
            $password = $_POST['password'];  
            $postdata['password'] = password_hash($password, PASSWORD_DEFAULT);
            // dd($postdata);

            $postcompany['companyname'] = $_POST['companyname'];
            $postcompany['companyaddress'] = $_POST['companyaddress'];
             
            $count_email = $this->model_user->count_email_create($_POST['email']);

            if($count_email['Data']['count_email']>0){
                //email exist
                $_SESSION['Error'] = "Email Exist, Try new email address..";
            }else{
                //insert
                $result = $this->users->insert($postdata);
                $postcompany['userId'] = $result['Data'];

                $result1 = $this->company->insert($postcompany);
                $_SESSION['Success'] = "Success";
            }
        } 
    }

    //edit
    public function edit()
    {
        if(empty($_POST))
        {
            redirect('users');
        }
        else
        {
            $editid = $_POST['editid'];

            $postdata['firstname'] = $_POST['firstname'];
            $postdata['lastname'] = $_POST['lastname'];
            $postdata['email'] = $_POST['email'];
            $postdata['address'] = $_POST['address'];
            $password = $_POST['password'];  
            $postdata['password'] = password_hash($password, PASSWORD_DEFAULT);

            $postcompany['companyname'] = $_POST['companyname'];
            $postcompany['companyaddress'] = $_POST['companyaddress'];

            $count_email = $this->model_user->count_email_edit($_POST['email'],$editid);
            
            if($count_email['Data'])
            {
                //email exist
                $_SESSION['Error'] = "Email Exist, Try new email address..";
            }
            else
            {
                //update
                $result = $this->users->where('userId',$editid)->update($postdata);
                $postcompany['userId'] = $editid;
                $result1 = $this->company->where('userId',$editid)->update($postcompany);
                
                $_SESSION['Success'] = "Update Success";
            }
        }
    }

    //delete
    public function delete()
    {
        // dd($_POST);
        $id= $_POST['id'];
        $deletedata = $this->model_user->delete_data($id);
        // dd('here');
        
    }

    //search
    public function search()
    {
        $param['user'] = '';
        if(isset($_POST["query"]))
        {
            $search = $_POST["query"];
          
            $result = $this->model_user->search_data($search);
            $param['user'] = $result['Data'];
        }
        $this->render_partial('User/Index', $param);
    }

    public function checkbox_del(){
        if(isset($_POST['btndelete']))
        {

            if(isset($_POST['delete']))
            {
                foreach($_POST['delete'] as $deleteid)
                {
                    $checkbox_del = $this->model_user->checkbox_delete($deleteid);
                    // redirect("user");
                    
                }
            }
        }
    }

    //userlog
    public function user_log()
    {
        $userlogs = $this->model_user->userlog_data();
        $param['all_log'] = $userlogs['Data'];

        $this->render('User/Userlog', $param);
    }

    //view single log
    public function view_log()
    {
        $userId = $_GET['user_id'];
        
        $userlogs = $this->model_user->viewlog_data($userId);

        $param['single_log'] = $userlogs['Data'];
      
        $this->render('User/Userlog', $param);
    }

    //change password
    public function change_password()
    {
        $oldpassword = $_POST['oldpassword'];  
        $cpassword = $_POST['cpassword'];  
        $newpassword = $_POST['newpassword']; 
        $postdata['password'] = password_hash($newpassword, PASSWORD_DEFAULT);
        
        $user_id = $_SESSION['Auth']['User']['userid'];
        
        $changepass = $this->model_user->change_pass($user_id);
   
        if(password_verify($_POST['oldpassword'], $changepass['Data']['password']) && $newpassword == $cpassword)
        {
            //update
            $result = $this->users->where('userId',$user_id)->update($postdata);
            $_SESSION['Success'] = "Password changed successfully..";
         
        }
        else
        {
            //error
            $_SESSION['Error'] = "Error..";
        }
    }

    // change pass of individual users
    public function change_user_pass()
    {
        $oldpassword = $_POST['oldpassword'];  
        $cpassword = $_POST['cpassword'];  
        $newpassword = $_POST['newpassword']; 
        $postdata['password'] = password_hash($newpassword, PASSWORD_DEFAULT);
        
        $user_id = $_POST['userId'];
        $changepass = $this->model_user->changepass($user_id);
   
        if(password_verify($_POST['oldpassword'], $changepass['Data']['password']) && $newpassword == $cpassword)
        {
            //update
            $result = $this->users->where('userId',$user_id)->update($postdata);
            $_SESSION['Success'] = "Password changed successfully..";
        }
        else
        {
            //error
            $_SESSION['Error'] = "Error..";
        }
    }

    //userinfo
    public function user_info()
    {
        $user_id = $_SESSION['Auth']['User']['userid'];
        $current_date = date("Y-m-d");
        $userinfo = $this->model_user->userinfo($user_id,$current_date);

        $userinfo = json_decode($userinfo['Data']['userinfo'],true);
        $param['info'] = $userinfo;

        $this->render('User/Userinfo', $param);
    }

    //totallogin
    public function total_login()
    {
        date_default_timezone_set("Asia/Kathmandu");
        $userId = $_POST['userId'];
        $date = $_POST['date'];
        $totallogin = $this->model_user->total_login($userId,$date);
       
        $log = $totallogin['Data']['login_out'];

        $array_data = json_decode($log);

        foreach($array_data as $key => $explodedata)
        {
            $data_explode = explode('_',$explodedata);
            $explode_data[$data_explode[0]][] = $data_explode[1];
        }

        $a = 0;
        $log_value = array();
        foreach($explode_data as $key => $log_data)
        {
            if(count($log_data)>=2)
            {
                $log_start_data = explode("|",$log_data[0]);
                $log_end_data = explode("|",$log_data[1]);

                $log_value[$a]['logintime'] = $log_start_data[2];
                $log_value[$a]['logouttime'] = $log_end_data[2];
            }
            else
            {
                $log_start_data = explode("|",$log_data[0]);

                $log_value[$a]['logintime'] = $log_start_data[2];
                $log_value[$a]['logouttime'] = 'Not logged out yet';
            }
            $a++;
        }
        
        $param['array'] = $log_value;
        $this->render_partial('User/UserlogModal', $param);
    }

    //download csv file
    function export_csv()
    {
        $filename = 'userdata_'.date('Ymd') .'.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv;");
        
        $param['user'] = $this->model_user->get_users_data();
        $user = $param['user'];

        $file = fopen('php://output','w');
        $header = array("Firstname","Lastname","Company Name","Company Address");
        fputcsv($file,$header);

        if(!empty($user)) 
        {
            foreach($user as $key => $value) 
            {
                fputcsv($file,$value);
            }
        }
        fclose($file);
        exit;
    }

    
    function export_xls()
    {
        $param['user'] = $this->model_user->get_users_data();
        $user = $param['user'];

        if(isset($user))
        {
            $filename = 'userdata_'.date('Ymd') . ".xls";
            header("Content-Type: application/vnd.ms-excel'");
            header("Content-Disposition: attachment; filename=$filename");
            
            $showcoloumn = false;
            if(!empty($user))
            {
                foreach($user as $key => $value)
                {
                    if(!$showcoloumn)
                    {
                        echo implode("\t", array_keys($value)) . "\n";
                        $showcoloumn = true;
                    }
                    echo implode("\t", array_values($value)) . "\n";
                }
            }
            exit;
        }
    }
}