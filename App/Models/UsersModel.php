<?php

class UsersModel extends Model
{
    private $users;
    private $company;
    private $userlogs;
    function __construct()
    {
        parent::__construct('users');
        $this->users = new Model('users');
        $this->company = new Model('company');
        $this->userlogs = new Model('userlogs');
    }

    function getUsersList()
    {
        $data = $this->select('')->get_all();
        return $data;
    }

    function addUser($data)
    {
        $this->insert($data,'');
        return;

    }

    function updateUser($data)
    {
        $id=$data['id'];
        $this->where('id',$id)->update($data);
        return;

    }

    function deleteUser($id)
    {
        $this->where('id',$id)->delete_row();
        return;
    }


    function viewindex_data()
    {
        $sql = $this->users
            ->select('*')
            ->join_table('company','company.userId','userId')
            ->get_all();
        return $sql;

    }

    function count_email_create($email)
    {
        
        $count_email = $this->users
            ->select('count(*) as count_email')
            ->where('email',$email)
            ->get_single();
        return $count_email;
    }
    
    function count_email_edit($email,$editid)
    {
        $count_email = $this->users
            ->select('*')
            ->where('email',$email)
            ->where('userId',$editid,'!=')
            ->get_single();
        return $count_email;
        
    }

    function delete_data($id)
    {
        // dd('here');
        $user = $this->users
            ->where('userId', $id)
            ->delete_row();
        $company = $this->company
            ->where('userId', $id)
            ->delete_row();
        
        // dd($company);
        if($user['Status'] == 'OK' || $company['Status'] == 'OK')
        {
            $_SESSION['Success'] = 'Deleted Successfully';
        }
        else
        {
            $_SESSION['Error'] = 'Error';
        }
        
    }

    function userlog_data()
    {
        $user_logs = $this->users
            ->select('users.firstname,userlogs.logintime,userlogs.logouttime,userlogs.date,userlogs.totallogin')
            ->join_table('userlogs','userlogs.userId','userId')
            ->get_all();
        // dd($user_logs);
        // $userlogs = "SELECT users.firstname,userlogs.logintime,userlogs.logouttime,userlogs.date,userlogs.totallogin
        // FROM users
        // JOIN userlogs
        // ON users.userId = userlogs.userId";
        // $userlogs = $this->userlogs->raw_sql($userlogs)->execute();
        return $user_logs;
    }

    function viewlog_data($userId)
    {
        $userlogs = $this->userlogs
            ->select('users.firstname,userlogs.logintime,userlogs.logouttime,userlogs.date,userlogs.userId,userlogs.totallogin')
            ->join_table('users','users.userId','userId')
            ->where('userlogs.userId',$userId)
            ->get_all();

        return $userlogs;
    }

    function change_pass($user_id)
    {
        $changepass = $this->users
            ->select('password')
            ->where('userId',$user_id )
            ->get_single();
        return $changepass;
    }

    function userinfo($user_id,$current_date)
    {
        $userinfo = $this->userlogs
            ->select('userinfo')
            ->where('userId',$user_id )
            ->where('date',$current_date,'>=')
            ->get_single();

        return $userinfo;
    }

    function total_login($userId,$date)
    {
        $totallogin = $this->userlogs
            ->select('login_out')
            ->where('userId',$userId)
            ->where('date',$date, '=')
            ->get_single();

        return $totallogin;
    }

    function get_users_data()
    {
        $sql = $this->users
            ->select('users.firstname, users.lastname, company.companyname, company.companyaddress')
            ->join_table('company','company.userId','userId')
            ->get_all();
        $user = $sql['Data'];
        
        return $user;
    }

    function search_data($search)
    {
        $query = $this->users->select('*')
            ->join_table('company','company.userId','userId')
            ->or_like_where('firstname' , '%'.$search.'%')
            ->or_like_where('companyname', '%'.$search.'%')
            ->or_like_where('companyaddress', '%'.$search.'%')
            ->get_all();
            // $query = "SELECT *
            // FROM users
            // JOIN company
            // ON users.userId = company.userId where firstname LIKE '%$search%'
            // OR lastname LIKE '%$search%'
            // OR companyname LIKE '%$search%'
            // OR companyaddress LIKE '%$search%'";
            // dd($query);

            // $result = $this->users->raw_sql($query)->execute();
        return $query;
    }

    function checkbox_delete($deleteid)
    {
        $user = $this->users->where('userId', $deleteid)->delete_row();
        $company = $this->company->where('userId', $deleteid)->delete_row();
        if($user['Status'] == 'OK' || $company['Status'] == 'OK')
        {
            $_SESSION['Success'] = 'Deleted Successfully';
            redirect("user");
        }
        else
        {
            $_SESSION['Error'] = 'Error';
            redirect("user");
        }
    }

}