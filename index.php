<?php

session_start();
foreach (glob("Config/*.php") as $filename) {
    require_once($filename);
}



include('System/Autoload/Autoloader.php');

$autoload = new Autoloader();
$autoload->loader();


$url = isset($_SERVER['PATH_INFO']) ? explode('/', ltrim($_SERVER['PATH_INFO'], '/')) : '/';

if ($url == '/') {
    //require_once __DIR__ . '/App/Controllers/' . $config['HomePage'] . 'Controller.php';
    $filepath = index_controller_path($config['HomePage'] . 'Controller.php');
    //display proper error
    require_once $filepath;
    $controllerName = $config['HomePage'] . 'Controller';
    $indexController = New $controllerName();
    print $indexController->Index();
} else {

    $requestedController = $url[0];

    $requestedAction = isset($url[1]) ? $url[1] : '';

    $requestedParams = array_slice($url, 2);


    //$ctrlPath = __DIR__ . '/App/Controllers/' . ucfirst($requestedController) . 'Controller.php';
    $filepath = index_controller_path(ucfirst($requestedController) . 'Controller.php');



    if (file_exists($filepath)) {

        //require_once __DIR__ . '/App/Controllers/' . $requestedController . 'Controller.php';
        require_once $filepath;
        $controllerName = ucfirst($requestedController) . 'Controller';
        $controllerObj = new $controllerName();
        if ($requestedAction != '') {
            print $controllerObj->$requestedAction($requestedParams);
        } else {
            print $controllerObj->Index($requestedParams);
        }
    } else {

        header('HTTP/1.1 404 Not Found');
        die('404 - The file - ' . $requestedController . 'Controller.php' . ' - not found');
        //require the 404 controller and initiate it
        //Display its view
    }
}

function index_controller_path($filename) {
    $path = '';

    foreach (glob_recursive(__DIR__ . '/App/Controllers/*') as $fileloc) {

        $file_exploded = explode('/', $fileloc);
        if ($filename == $file_exploded[count($file_exploded) - 1]) {
            $path = $fileloc;
        }
    }

    return $path;
}

function glob_recursive($pattern, $flags = 0) {
    $files = glob($pattern, $flags);
    foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
        $files = array_merge($files, glob_recursive($dir . '/' . basename($pattern), $flags));
    }
    return $files;
}
