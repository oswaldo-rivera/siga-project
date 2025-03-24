<?php

#[AllowDynamicProperties]
class View {
	private $_request;
	private $_js;
	private $_rutas;


	public function __construct(Request $peticion) {
		$this->_request = $peticion;
		$this->_js = array();
		$this->_rutas = array();

		$modulo = $this->_request->getModulo();
		$controlador = $this->_request->getControlador();
		$metodo = $this->_request->getMetodo();
		$this->_controller = $controlador;

		$this->_rutas['root'] = ROOT;
		$this->_rutas['ruta_base'] = BASE_URL;
		$this->_rutas['view'] = ROOT . 'views' . DS . $controlador . DS;
		$this->_rutas['js'] = BASE_URL . 'views' . DS . $controlador . DS . 'js' . DS;

	}

  /********************************************************
   * esta funcion construye la vista a partir de          *
   * partes estaticas que se encuentran en /views/layout/ *
   * mas la parte central que esta definida por el        *
   * nombre del controlador                               *
   ********************************************************/
	public function renderizar($vista) {

		$js = array();

		if (count($this->_js)) {
			$js = $this->_js;
		}

		$_layoutParams = array(
			'ruta_home' => $this->_rutas['ruta_base'],
			'ruta_css' => $this->_rutas['ruta_base'] . 'views/layout/css/',
			'ruta_img' => $this->_rutas['ruta_base'] . 'views/layout/pics/',
			'ruta_js' => $this->_rutas['ruta_base'] . 'views/layout/js/',
			'ruta_icon' => $this->_rutas['ruta_base'] . 'views/layout/icon/',
			'js' => $js
		);

		$rutaView = $this->_rutas['view'] . $vista . '.phtml';

        $viewZone = (is_readable($rutaView)) ? $rutaView  : $this->_rutas['root'] . 'views' . DS. 'error'  . DS . 'index.phtml';
            
		include_once $this->_rutas['root'] . 'views' . DS. 'layout' . DS . 'header.php';
		include_once $this->_rutas['root'] . 'views' . DS. 'layout' . DS . 'navbar.php';
		include_once $viewZone;
		include_once $this->_rutas['root'] . 'views' . DS. 'layout' . DS . 'footer.php';
	}

  /***************************************************
   * render de paginas externas al sistema, publicas *
   * que no requieren estar logueado para verlas     *
   * pero tampoco muestran el menu de la aplicacion  *
   ***************************************************/
	public function renderExterno($vista) {

		$js = array();

		if (count($this->_js)) {
			$js = $this->_js;
		}

		$_layoutParams = array(
			'ruta_home' => $this->_rutas['ruta_base'],
			'ruta_css' => $this->_rutas['ruta_base'] . 'views/layout/css/',
			'ruta_img' => $this->_rutas['ruta_base'] . 'views/layout/pics/',
			'ruta_js' => $this->_rutas['ruta_base'] . 'views/layout/js/',
			'ruta_icon' => $this->_rutas['ruta_base'] . 'views/layout/icon/',
			'js' => $js
		);

		$rutaView = $this->_rutas['view'] . $vista . '.phtml';

        $viewZone = (is_readable($rutaView)) ? $rutaView : $this->_rutas['root'] . 'views' . DS. 'error' . DS . 'index.phtml';

		include_once $this->_rutas['root'] . 'views' . DS. 'layout' . DS . 'header.php';
		include_once $viewZone;
		include_once $this->_rutas['root'] . 'views' . DS. 'layout' . DS . 'footer.php';

	}

	public function render_content($vista) {

		$rutaView = $this->_rutas['view'] . $vista . '.phtml';
		$rutaError = $this->_rutas['ruta_base'] . 'views/error/index.phtml';

		if(is_readable($rutaView)){
			include_once $rutaView;
		} else {
			include_once $rutaError;
		}
	}

  // cargar archivos js exclusivos de una vista
	public function setJs(array $js){
		if(is_array($js) && count($js)) {
			for($i = 0; $i < count($js); $i++) {
				$this->_js[] = $this->_rutas['js'] . $js[$i] . '.js';
			}
		} else{
			throw new Exception('error de js');
		}
	}
	
  // configurar alertas
  public function setAlert($msg ,$type = 'primary', $action = null){
		/**************************************************************************************
		 * Tipos primary, secondary, info, success, warning, danger (no requieren action)     *
		 * Tipo confirm (requiere action).                                                    *
		 * el argumento action es una funcion de js que se ejecuta cuando el usuario confirma *
		 **************************************************************************************/
		if($msg!='') {
			Session::set('alerta_msg', $msg);
			Session::set('alerta_type', $type);
			Session::set('alerta_action', $action);
		} 
  }

	public function unsetAlert() {
		unset($_SESSION["alerta_type"]);
		unset($_SESSION["alerta_msg"]);
		unset($_SESSION["alerta_action"]);
	}

    // funcion para tomar las iniciales del nombre del usuario
	public function userIniciales($nombres,$apellidos){
		$name = '';
        $n = explode(' ',$nombres);
        $a = explode(' ',$apellidos);
        foreach($n as $x){
            $name .=  $x[0];
        }
        foreach($a as $x){
            $name .=  $x[0];
        }
        return mb_strtoupper($name);
	}
}

?>
