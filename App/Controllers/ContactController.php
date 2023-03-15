<?php


class ContactController extends Controller
{
    private $contact;
    private $model_contact;

    function __construct()
    {
        parent::__construct();
        $this->contact = new Model('contact');
        $this->model_contact = $this->load->Model('Contact');
    }

    public function index()
    {
        if (!isset( $_SESSION['Auth']['User']['email'] ) || empty( $_SESSION['Auth']['User']['email'] )) {
            redirect('Log');
        }

        $get_data = $this->model_contact->view_data();
        $param['contact'] = $get_data['Data'];

        $this->render('Contact/Contact', $param);
    }

    public function create()
    {
        // dd('asd');
        if(empty($_POST))
        {
            redirect ('contact');
        }
        else
        {
            $postdata = array();

            $postdata['phone'] = $_POST['phone'];
            $postdata['email'] = $_POST['email'];
            $postdata['address'] = $_POST['address'];

            $check_email_phone = $this->model_contact->check_email_phone($postdata['phone'],$postdata['email']);

            if($check_email_phone['Data']['count_email_phone'] > 0)
            {
                // exists
                $_SESSION['Error'] = "Email or Phone Number Exist, Try new...";
            }
            else
            {
                //add
                $result = $this->contact->insert($postdata);
                
                $_SESSION['Success'] = "Contact Added Successfully";
            }
        }
    }

    public function edit()
    {
        if(empty($_POST))
        {
            redirect ('contact');
        }
        else
        {
            $editid = $_POST['editid'];

            $postdata['phone'] = $_POST['phone'];
            $postdata['email'] = $_POST['email'];
            $postdata['address'] = $_POST['address'];

            $check_email_phone_edit = $this->model_contact->check_email_phone_edit($postdata['phone'],$postdata['email'],$editid);

            if($check_email_phone_edit['Data']['count_email_phone'] > 0)
            {
                // exists
                $_SESSION['Error'] = "Email or Phone Number Exist, Try new...";
            }
            else
            {
                //update
                $update = $this->contact->where('contact_id',$editid)->update($postdata);
                
                $_SESSION['Success'] = "Contact Updated Successfully";
            }
        }
    }

    public function delete()
    {
       $contact_id = $_POST['contact_id'];
       $delete_data = $this->model_contact->delete_data($contact_id);
      
    }

    public function contact_upload()
    {
        if (isset($_POST['importSubmit'])) 
        {
            $csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');
        
            if (!empty($_FILES['file']['name']) && in_array($_FILES['file']['type'], $csvMimes)) 
            {
                if (is_uploaded_file($_FILES['file']['tmp_name'])) 
                {
                    $csvFile = fopen($_FILES['file']['tmp_name'], 'r');
                    
                    fgetcsv($csvFile);
        
                    while (($line = fgetcsv($csvFile)) !== FALSE) 
                    {
                        
                        if(!empty($line) && is_array($line))
                        {
                            if(isset($line[0], $line[1],$line[2]))
                            {
                                $postdata['phone'] = $line[0];
                                $postdata['email'] = $line[1];
                                $postdata['address'] = $line[2];

                                $check_phone = preg_match('/^[0-9]{10}+$/', $postdata['phone']);
                                
                                if ($check_phone == true && (strlen($postdata['phone']) == 10) && (!filter_var($postdata['email'], FILTER_VALIDATE_EMAIL) === false))
                                {
                                    $count_email = $this->contact->select('count(*) as count')
                                    ->or_where('email',$postdata['email'])
                                    ->or_where('phone',$postdata['phone'])
                                    ->get_single();
                                    $email_count = $count_email['Data']['count'];

                                    if($email_count > 0)
                                    {
                                        $_SESSION['Error'] = "Email or Phone Number Already Exists";
                                        redirect('contact');
                                    }
                                    else
                                    {
                                        $insert = $this->contact->insert($postdata);
                                      
                                        if($insert['Status'] == 'OK')
                                        {
                                            $_SESSION['Success'] = "Contact Uploaded Successfully";
                                            redirect('contact');
                                        }
                                        else
                                        {
                                            $_SESSION['Error'] = "Error in uploading contacts";
                                            redirect('contact');
                                        }
                                    }
                                } 
                                else 
                                {
                                    $_SESSION['Error'] = "Email or Phone Number not valid";
                                    redirect('contact');
                                }
                            }
                            else
                            {
                                $_SESSION['Error'] = "Error";
                                redirect('contact');
                            }
                        }
                    }
                    fclose($csvFile);
                } 
                else 
                {
                    $_SESSION['Error'] = "Error";
                    redirect('contact');
                }
            } 
            else 
            {
                $_SESSION['Error'] = "Invalid file";
                redirect('contact');
            }
        }
    }
}