<?php

class ModelDecorator 
{
    public $page = 1;
    public $totalRows;
    public $model;
    public $pageSize = 5;
    public $offset;
    public $showTotalRecords;
    public $totalRecords;

    function __construct($model, $showTotalRecords = false, $pageSize)
    {

        $this->model = $model;
        $this->showTotalRecords = $showTotalRecords;

        if (isset($_GET['page']))
            $this->page = $_GET['page'];
        
        if (isset($pageSize))
            $this->pageSize = $pageSize;

        if ($this->showTotalRecords) {
                $this->showTotalRecords = $this->model->select('count(*) as total_records')->get_single();
                $this->totalRecords = $this->showTotalRecords['Data']['total_records'];
   
           }
        
     
        if($this->pageSize=='All')
            $this->pageSize =  $this->totalRecords;

       
            $this->offset = $this->page === 1 ? 0 : ($this->page - 1) * $this->pageSize ;

        $this->model->limit($this->pageSize, $this->offset);
    }

    public function __call($name, $arguments) {
        $data = call_user_func_array(
            array($this->model, $name),
            $arguments
        );
        
        return $data;
    }

    public function createLinks(){
        
        
        $limit = $this->pageSize;
        $totalRecords =  $this->totalRecords;
        $number_of_page = ceil($totalRecords/$limit);
        $page= $this->page;
        $link ='';
        
      
        if (($totalRecords > $limit) && ($page<=$number_of_page) ) {
            $link .="<div class='pagination-wrapper'> <ul class='pagination'>";

            if($page>1)
                $link .= "<li><button  class='btn btn-info btn-sm'  onclick=\"location.href='?page=" . ($page-1) . "'\">Prev </button></li>";
            else
                $link .= "<li><button  disabled class='btn btn-info btn-sm'>Prev </button></li>";

            if ($page > 1 ) {
                for ($x = 1; $x < $page; $x ++ ) {
                    $link .= "<li><a href=\"?page=" . $x . "\">" . $x . " </a></li>";
                
                 }   
            }

            for ($x = $page; $x <= $number_of_page; $x ++ ) {
                    if ($page == $x ) {
                        $link .= "<li class='active'><a href=\"?page=" . $x . "\">" . $x . " </a></li>";
                    } else {
                        $link .= "<li><a href=\"?page=" . $x . "\">" . $x . " </a></li>";
                    }
                
            }

            if(!($page==$number_of_page))
                $link .= "<li><button  class='btn btn-info btn-sm'  onclick=\"location.href='?page=" . ($page+1) . "'\">Next </button></li>";
            else
                $link .= "<li><button  disabled class='btn btn-info btn-sm'>Next </button></li>";

            $link .= "</ul></div> ";
            echo $link;
        }
       
    }

    public function noRecords()
    {
        $records='';
        $totalRecords = $this->showTotalRecords['Data']['total_records'];
        if($totalRecords==0)
        {
            $records=" <tr><td style='color: red;'>No records found!</td></tr>";
        }

        echo $records;

    }
}