<?php

class Bootstrap {

  // lee el objeto "peticion" de tipo "Request" definido en la clase del mismo nombre
	public static function run(Request $peticion) {
		$controller = $peticion->getControlador() . 'Controller';
		$metodo = $peticion->getMetodo();
		$args = $peticion->getArgs();

    // ruta del controlador
		$rutaControlador = ROOT . 'controllers' . DS . $controller . '.php';

    // si existe el archivo del controlador...	
    if(is_readable($rutaControlador)){
      // el archivo es requerido o cargado
			require_once $rutaControlador;
			// se crea una nueva instancia de controller
			$controller = new $controller;

      // si no se declara un metodo en la peticion, se usa "index" por defecto
			if(is_callable(array($controller,$metodo))){
				$metodo = $peticion->getMetodo();
			} else {
				$metodo = 'index';
			}

      // si hay argumentos en la peticion, los captura para luego usarlos
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
