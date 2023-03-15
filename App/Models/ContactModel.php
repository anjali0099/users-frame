<?php

class ContactModel extends Model
{
    private $contact;

    function __construct()
    {
        parent:: __construct('contact');
        $this->contact = new Model('contact');
    }

    function view_data()
    {
        $query = $this->contact->select('*')->get_all();
        return $query;
    }

    function delete_data($contact_id)
    {
        $query = $this->contact->where('contact_id',$contact_id)->delete_row();

        if($query['Status'] == 'OK')
        {
            $_SESSION['Success'] = "Contact Deleted Successfully";
        }
        else
        {
            $_SESSION['Error'] = "Error";
        }
    }

    function check_email_phone($phone,$email)
    {
        $query = $this->contact
            ->select('count(*) as count_email_phone')
            ->or_where('phone',$phone)
            ->or_where('email',$email)
            ->get_single();
        return $query;
    }

    function check_email_phone_edit($phone,$email,$editid)
    {
        $query = $this->contact
            ->select('count(*) as count_email_phone')
            ->or_where('phone',$phone)
            ->or_where('email',$email)
            ->where('contact_id',$editid,'!=')
            ->get_single();
        return $query;
    }



    


}
