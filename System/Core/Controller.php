<?php

class Controller {

    private $template;
    public $config = '';
    public $load;

    function __construct() {
        global $config;
        $this->config = $config;
        $this->load = new Core();
    }

    function render($viewname, $data = array()) {
        //echo $this->getview($viewname , $data);
        $viewcontent = '';
        // $viewpath=base_dir().'/App/Views/'.ucfirst($viewname).'View.php';
        $viewpath = base_dir() . 'Assets/Templates/' . $this->config['ProjectTemplate'] . '/Views/' . ucfirst($viewname) . 'View.php';
        extract($data);

        ob_start();
        include( $viewpath);
        $viewcontent = ob_get_contents();
        ob_end_clean();


        $content_data = array(
            'view_content' => $viewcontent,
            'project_title' => $this->config['ProjectTitle']
        );
        extract($content_data);
        $templatepath = base_dir() . 'Assets/Templates/' . $this->config['ProjectTemplate'] . '/Layout.php';

        ob_start();

        include($templatepath);
        $content = ob_get_contents();
        ob_end_clean();

        echo $content;
    }

    function render_partial($viewname, $data = array()) {
        //$viewpath=BASE_DIR.'/App/Views/'.ucfirst($viewname).'View.php';
        // $viewpath=base_dir().'/App/Views/'.ucfirst($viewname).'View.php';
        $viewpath = base_dir() . 'Assets/Templates/' . $this->config['ProjectTemplate'] . '/Views/' . ucfirst($viewname) . 'View.php';
        extract($data);

        ob_start();
        include( $viewpath);
        $content = ob_get_contents();
        ob_end_clean();
        echo $content;
    }

}
