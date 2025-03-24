<?php

// activacion TEMPORAL de vista de errores
    ini_set('display_errors',1);
    error_reporting(E_ALL ^ E_NOTICE);

// definicion de directorios
    define('DS', DIRECTORY_SEPARATOR);
    define('ROOT', realpath(dirname(__FILE__)) . DS);
    define('APP_PATH', ROOT . 'application' . DS);

// inicio de carga de archivos base de la aplicacion
// se ejectan en el orden necesario
    try 
    {
	    require_once APP_PATH . 'Config.php';
	    require_once APP_PATH . 'Request.php';
	    require_once APP_PATH . 'Bootstrap.php';
	    require_once APP_PATH . 'Controller.php';
	    require_once APP_PATH . 'Model.php';
	    require_once APP_PATH . 'View.php';
	    require_once APP_PATH . 'Database.php';
	    require_once APP_PATH . 'Session.php';
	    require_once APP_PATH . 'Hash.php';

        // metodo "init" de la clase "Session" 
	    Session::init();

        // metodo "run" de clase "Bootstrap"
	    Bootstrap::run(new Request);

    } 
    catch (Exception $e) 
    {
	    echo $e->getMessage();
    }
?>
