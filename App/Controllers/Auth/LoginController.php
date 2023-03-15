<?php

class LoginController extends Controller
{
    //private $user_model;
    private $users;
    private $pagination;
    private $newModel;
    private $paginationFlag;
    private $pageSize=5;


    function __construct()
    {
        parent::__construct();

        

        if((isset($_POST['pageSize'])) && ($_POST['pageSize']!='')) {
            $this->pageSize=$_POST['pageSize'];
            $_SESSION['ok'] = $_POST['pageSize'];
        }

     
        if(isset($_SESSION['ok'])){
            $this->pageSize=$_SESSION['ok'];
            
        }
    //    dd($this->load->Model('users'));
        $this->users = new ModelDecorator($this->load->Model('users'), true, $this->pageSize);
        $this->users2 = $this->load->Model('users');
        $this->paginationFlag = true;

       
        
    }

    // /api/users?pageSize=10&currentPage=1
    public function index()
    {
        ($this->paginationFlag) ? ($data = $this->users->getUsersList()) : ($data = $this->users2->getUsersList());
        $param['users'] = $data['Data'];
        $param['paginate'] =$this->users;
        $param['filterPage'] = $this->pageSize;


        $this->render('Auth/Login', $param);
    }

    public function addEditUserForm()
    {

        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $data = $this->users2->select('')->where('id', $id)->get_all();
            $param['edit'] = $data['Data'];;

            $this->render('Auth/Form', $param);
        } else
            $this->render('Auth/Form');


    }

    public function addUpdateUser()
    {
        if (isset($_POST['id'])) {
            $this->users->updateUser($_POST);
        } else {
            $this->users->addUser($_POST);
        }

        redirect('');
    }


    public function delete()
    {

        $id = $_GET['id'];
        $this->users->deleteUser($id);

        redirect('');
    }

}