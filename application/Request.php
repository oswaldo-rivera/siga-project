<?php
class Request {

  /****************************************************************************************
   * El usuario hace una peticion por metodo get (via url)                                *
   * Por ejemplo: www.siga.com.ar/docentes/sistemas/1                                     *
   * el archivo .htaccess la traduce a www.siga.com.ar?url:docentes/sistemas/1            *
   * la variable url es leida y se obtienen partes separadas por /                        *
   * la primera parte se asume como nombre del controlador, tambien nombre de la vista    *
   * la segunda parte se asume como nombre del metodo (la funcion dentro del controlador) *
   * las siguientes partes (si las hay) se asumen como argumentos para el metodo          *
   ****************************************************************************************/

	private $_controlador;
	private $_metodo;
	private $_argumentos;

	public function __construct() {

		if (isset($_GET['url'])) {
			$url = filter_input(INPUT_GET,'url',FILTER_SANITIZE_URL);
			$url = explode('/', $url);
			$url = array_filter($url);

			$this->_controlador = strtolower(array_shift($url));
				if(!$this->_controlador) {
				$this->_controlador = 'index';
		    }

			$met = array_shift($url);
			$this->_metodo = ($met != null) ? strtolower($met) : null;
			$this->_argumentos = $url;
		}

		if(!$this->_controlador) {
			$this->_controlador = DEFAULT_CONTROLLER;
		}

		if(!$this->_metodo) {
			$this->_metodo = 'index';
		}

		if(!isset($this->_argumentos)) {
			$this->_argumentos = array();
		}
	}

	public function getControlador() {
		return $this->_controlador;
	}

	public function getMetodo() {
		return $this->_metodo;
	}

	public function getArgs() {
		return $this->_argumentos;
	}
}

?>
