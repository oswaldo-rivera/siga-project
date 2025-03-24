<?php
#[AllowDynamicProperties]
abstract class Controller {
	protected $_view;
	protected $_request;

	public function __construct() {
		$this->_request = new Request();
		$this->_view = new View($this->_request);
	}

	abstract public function index();

	// carga al controlador actual el modelo que corresponda
	protected function loadModel($modelo) {
		$modelo = $modelo . 'Model';
		$rutaModelo = ROOT . 'models' . DS . $modelo . '.php';

		if(is_readable($rutaModelo)) {
			require_once $rutaModelo;
			$modelo = new $modelo;
			return $modelo;
		} else {
			throw new Exception('Error: no existe el modelo');
		}
	}

	// incluir librerias para su uso dentro del codigo
	protected function getLibrary($libreria){
		$rutaLibreria = ROOT . 'libs' . DS . $libreria .'.php';

		if(is_readable($rutaLibreria)){
			include_once $rutaLibreria;
		} else {
			throw new Exception('Error: La librería no esta disponible');
		}
	}
	
	// requerir librerias
	protected function requireLibrary($libreria){
		$rutaLibreria = ROOT . 'libs' . DS . $libreria .'.php';

		try {
			if (!file_exists($rutaLibreria ))
				throw new Exception ($rutaLibreria.' file not exist');
			else
				require_once($rutaLibreria ); 
		}
		catch(Exception $e) {    
			echo "Message : " . $e->getMessage();
			echo "Code : " . $e->getCode();
		}
	}

	// requerir especificamente phpMailer
	protected function getMailer(){
		$ruta = ROOT . 'libs' . DS . 'mailer' . DS;
		if(is_readable($ruta . 'PHPMailer.php')){
			require_once $ruta . 'Exception.php';
			require_once $ruta . 'PHPMailer.php';
			require_once $ruta . 'SMTP.php';
		} else {
			throw new Exception('PhpMailer no esta disponible');
		}
	}

	// Depurar y validar texto recibido por metodo POST
	protected function getTexto($clave) {
		if (isset($_POST[$clave]) && !empty($_POST[$clave])) {
			$_POST[$clave] = htmlspecialchars($_POST[$clave], ENT_QUOTES);
			return $_POST[$clave];
		}
		return '';
	}

    // Depurar y validar parametro generico recibido por metodo POST
	protected function getPostParam($clave) {
		if (isset($_POST[$clave]) && !empty($_POST[$clave])) {
			return $_POST[$clave];
		}
		return '';
	}

	// Depurar y validar numeros recibidos por metodo POST
	protected function getNumeric($clave){
		if (isset($_POST[$clave]) && !empty($_POST[$clave])) {
			$_POST[$clave] = filter_input(INPUT_POST, $clave, FILTER_VALIDATE_FLOAT);
			return $_POST[$clave];
		}
		return 0;
	}

	// Depurar y validar numeros enteros recibidos por metodo POST
	protected function getInt($clave){
		if (isset($_POST[$clave]) && !empty($_POST[$clave])) {
			$_POST[$clave] = filter_input(INPUT_POST, $clave, FILTER_VALIDATE_INT);
			return $_POST[$clave];
		}
		return 0;
	}

    // Depurar y validar sentencias SQL recibidas por metodo POST
	protected function getSql($clave){
		if (isset($_POST[$clave]) && !empty($_POST[$clave])) {
			$_POST[$clave] = strip_tags($_POST[$clave]);
			return trim($_POST[$clave]);
		}
	}

    // Depurar y validar alfanumericos recibidos por metodo POST
	protected function getAlphaNum($clave){
		if (isset($_POST[$clave]) && !empty($_POST[$clave])) {
			$_POST[$clave] = (string) preg_replace('/[^A-Z0-9_]/i','',$_POST[$clave]);
			return trim($_POST[$clave]);
		}
	}

	// redireccionar a otra ruta especificada
	protected function redireccionar($ruta = false){
		if($ruta) {
			header('location:' . BASE_URL . $ruta);
			exit;
		} else {
			header('location:' . BASE_URL);
			exit;
		}
	}

	// Depurar y validar cualquier entero
	protected function validarInt($int){
		$int = (int) $int;
		if (is_int($int)){
			return $int;
		} else {
			return 0;
		}
	}

	// Depurar y validar numeros de coma flotante y ajustar # decimales
	protected function validarDec($dec, $decimales = 2) {
		// falta por desarrollar esta funcion
		return $dec;
	}

    // Depurar y Validar Email
	protected function validarEmail($email) {
		if (!filter_var($email,FILTER_VALIDATE_EMAIL)) {
			return false;
		}
		return true;
	}

}

?>
