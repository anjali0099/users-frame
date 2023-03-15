<?php
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\SMTP;
  use PHPMailer\PHPMailer\Exception;
  // require 'vendor/autoload.php';
  include('E:\xampp\vendor\autoload.php');

class LogController extends Controller
{
    private $users;

    function __construct()
    {
      parent::__construct();
      $this->users = new Model('users');
      $this->userlogs = new Model('userlogs');
      $this->model_log = $this->load->Model('Log');
    }

    public function index()
    {
      if (!isset( $_SESSION['Auth']['User']['email'] ) || empty( $_SESSION['Auth']['User']['email'] )) 
      {
        $this->render('Log/login');
      }
      else
      {
        redirect('User','refresh');
      }
    }


    //login
    public function login()
    {
      if(!isset($_POST['email'], $_POST['password']))
      {
        redirect('Log');
      }
      else
      {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $user = $this->model_log->get_user($email);
        if (isset($user['Data']) && password_verify($_POST['password'], $user['Data']['password']))
        {
          $_SESSION['Auth']['User']['userid']=  $user['Data']['userId'];
          $ID = $_SESSION['Auth']['User']['userid'];

          $logcheck = $this->model_log->log_check($ID);
          
          if($logcheck['Data']['log_check']>0)
          {
            redirect('Log/login');
            
            $_SESSION['Error'] = "User already logged in"; 
            
          }
          else
          {
            $current_date = date("Y-m-d");
            $check = $this->model_log->check_login($ID,$current_date);
            if($check['Data']['login_today']>0)
            {
              //update
              date_default_timezone_set("Asia/Kathmandu");
              $updatedata['logintime'] = date("h:i:s");
              $updatedata['userflag'] = '1';
              $updatedata['totallogin'] = $check['Data']['totallogin'] +1;

              $logincheck = $check['Data']['login_out'];
              $array_data = json_decode($logincheck);
              $login_data = explode("_",end($array_data));
              $newlogin = $login_data[0] + 1;
              $login_new = $newlogin."_|".'login|'.date("h:i:s");
              $result_array = array_merge($array_data,array($login_new));
          
              $final_data = json_encode($result_array);

              $updatedata['login_out'] =  $final_data;

              $update = $this->userlogs->where('userId',$ID)->where('date',$current_date, '>=')->update($updatedata);
            }
            else
            {
              //insert
              date_default_timezone_set("Asia/Kathmandu");
              $postdata['userId'] = $ID;
              $postdata['logintime'] = date("h:i:s");
              $postdata['date'] = date("Y-m-d");
              $postdata['userflag'] = '1';
              $postdata['totallogin'] = $check['Data']['totallogin'] +1;
             
              $logincheck = $check['Data']['login_out'];

              $login = '1_|login|'.date("h:i:s");

              $final_data = json_encode(array($login));
              $postdata['login_out'] = $final_data;

              $insert = $this->userlogs->insert($postdata);

            }
            
            redirect('User');
            date_default_timezone_set("Asia/Kathmandu");
            $_SESSION['Auth']['User']['email'] =  $user['Data']['email'];
            $_SESSION['Auth']['User']['name'] =  $user['Data']['firstname'];
            $email = $_SESSION['Auth']['User']['email'];
            
            $_SESSION['loggedin_time'] = time();
            $_SESSION['Success'] = "Success";
            
            $logintime_select = $this->model_log->send_email($ID,$current_date);

            $mail = new PHPMailer(true);
            $mailid = $email;
            $subject = "Your Logged in time";
            $text_message = 'You have logged in at:'.$logintime_select['Data']['logintime'];
    
            try
            {
                $mail->IsSMTP();
                $mail->isHTML(true);
                $mail->SMTPDebug = 0;
                $mail->SMTPAuth = true;
                $mail->SMTPSecure = "ssl";
                $mail->Host = "smtp.gmail.com";
                $mail->Port = '465';
                $mail->AddAddress($mailid);
                $mail->Username ="anjalifyp2022@gmail.com";
                $mail->Password ="anjali2022";
                $mail->SetFrom('anjalifyp2022@gmail.com','Anjali Shrestha');
                $mail->AddReplyTo("anjalifyp2022@gmail.com","Anjali Shrestha");
                $mail->Subject = $subject;
                $mail->Body = $text_message;
    
                if($mail->Send())
                {
                  $_SESSION['Success'] = "Success";
                }
    
            }
            catch(phpmailerException $ex)
            {
                $msg = "
                ".$ex->errorMessage()."
                ";
            }
            
          }
        }
        else
        {
          $_SESSION['Error'] = "Invalid email or password";
          redirect('log');        
        }
      }
    }


    //logout
    function logout() {
    
      date_default_timezone_set("Asia/Kathmandu");
      $current_date = date("Y-m-d");
      $userId = $_SESSION['Auth']['User']['userid'];
      $postdata['logouttime'] = date("h:i:s");
      $postdata['date'] = date("Y-m-d");
      $postdata['userflag'] = '0';

      $check = $this->model_log->check_logout($userId,$current_date);
      $logoutcheck = $check['Data']['logouttoday'];
      if($logoutcheck>0)
      {
        $logoutcheck = $check['Data']['login_out'];
        $array_data = json_decode($logoutcheck);

        $logout_data = explode("_",end($array_data));
        $newlogin = $logout_data[0];
        $logout_new = $newlogin."_|".'logout|'.date("h:i:s");
        $result_array = array_merge($array_data,array($logout_new));

        $final_data = json_encode($result_array);

        $postdata['login_out'] =  $final_data;
      }
      else
      {
        $logout =  '1_|logout|'.date("h:i:s");
        $logoutcheck = $check['Data']['login_out'];
        $array_data = json_decode($logoutcheck);
        $result_array = array_merge($array_data,array($logout));

        $postdata['login_out'] = json_encode($result_array);
      }

      $result = $this->userlogs->where('userId',$userId)->where('date',$postdata['date'])->update($postdata);
    
      // remove all session variables
      session_unset();
      // destroy the session
      session_destroy();

      redirect("Log");
    }


    //register
    public function register()
    {
      if(empty($_POST))
      {
        redirect ('users');
        $_SESSION['Success'] = "Empty post";
      }
      else
      {
        $postdata['firstname'] = $_POST['firstname'];
        $postdata['lastname'] = $_POST['lastname'];
        $postdata['email'] = $_POST['email'];
        $postdata['address'] = $_POST['address'];
        $password = $_POST['password'];  
        $postdata['password'] = password_hash($password, PASSWORD_DEFAULT);

        if($postdata['email'] != "") 
        {
            $users = $this->users->select('*')->get_all();
            foreach ($users['Data'] as $value) 
            {
              if ($postdata['email']==$value['email'])
              {
                $_SESSION['Error'] = "Email Exist, Try new email address..";
                break;
              }
              else
              {
                $result = $this->users->insert($postdata);
                $_SESSION['Success'] = "Success";
              }
            } 
        }
      }    
    }
}
