<?php

class Bootstrap {

    // objeto "Request" definido en la clase del mismo nombre
	public static function run(Request $peticion) {
		$controller = $peticion->getControlador() . 'Controller';
		$metodo = $peticion->getMetodo();
		$args = $peticion->getArgs();

        // ruta del controlador
		$rutaControlador = ROOT . 'controllers' . DS . $controller . '.php';

        // si existe el archivo del controlador...	
        if(is_readable($rutaControlador)){

            // el archivo es requerido
			require_once $rutaControlador;
			$controller = new $controller;

            // si no se declara un metodo, se usa "index" por defecto
			if(is_callable(array($controller,$metodo))){
				$metodo = $peticion->getMetodo();
			} else {
				$metodo = 'index';
			}

            // si hay argumentos, los captura para luego usarlos
			if(isset($args)){
				call_user_func_array(array($controller, $metodo), $args);
			} else {
				call_user_func(array($controller, $metodo));
			}
		} else {
			throw new Exception('Error: Ruta no encontrada');
		}
	}
}

?>
