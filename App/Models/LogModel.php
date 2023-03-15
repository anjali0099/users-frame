<?php

class LogModel extends Model
{
    private $users;
    private $company;
    private $userlogs;
    function __construct()
    {
        parent::__construct('log');
        $this->users = new Model('users');
        $this->company = new Model('company');
        $this->userlogs = new Model('userlogs');
    }

    function get_user($email)
    {
        $user = $this->users->select('*')->where('email',$email)->get_single();

        return $user;
    }

    function log_check($ID)
    {
        $logcheck = $this->userlogs
            ->select('count(*) as log_check')
            ->where('userId',$ID)
            ->where('userflag','1','>=')
            ->get_single();

        return $logcheck;
    }

    function check_login($ID,$current_date)
    {
        $check = $this->userlogs
            ->select('login_out,count(*) as login_today, totallogin')
            ->where('userId',$ID)
            ->where('date',$current_date, '>=')
            ->get_single();

        return $check;
    }


    function check_logout($userId,$current_date)
    {
        $check = $this->userlogs
            ->select('login_out,count(logouttime) as logouttoday')
            ->where('userId',$userId)
            ->where('date',$current_date, '>=')
            ->get_single();

        return $check;
    }


    function send_email($ID,$current_date)
    {
        $logintime_select = $this->userlogs
                            ->select('logintime')
                            ->where('userId',$ID)
                            ->where('date',$current_date, '>=')
                            ->get_single();
        return $logintime_select;
    }


}