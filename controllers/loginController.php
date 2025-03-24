<?php
class loginController extends Controller{

	private $_login;
  private $_usuarios;

	public function __construct() {
		parent::__construct();
		$this->_login = $this->loadModel('login');
		$this->_usuarios = $this->loadModel('usuarios');
	}

	public function index(){
		if(Session::get('autenticado')){
			$this->redireccionar();
		}

		$this->_view->sectionTitle = 'Ingresar';
		// $this->_view->setJs(array('login'));

		if($this->getInt('enviar')==1){
			$this->_view->datos = $_POST;

			$row = $this->_login->getUsuario(
				$this->getPostParam('us_user'),
				$this->getPostParam('us_pass')
			);

			if (!$row) {
				$this->_view->setAlert('Usuario y/o Password incorrectos','danger');
				$this->_view->renderizar('index');
				exit;
			}
      
			if($row['usu_status'] != 1){
				$this->_view->dni = $row['usu_dni'];
				$this->redireccionar('usuarios/confirmar');
				exit;
			}
			
			Session::set('autenticado', true);
			Session::set('usuario_dni', $row['usu_dni']);
			Session::set('usuario', $row['usu_nombre'] . " " . $row['usu_apellido']);
			Session::set('iniciales', $this->_view->userIniciales($row['usu_nombre'],$row['usu_apellido']));
			Session::set('level', $row['usu_role']);
			Session::set('numlevel', Session::getLevel($row['usu_role']));
			Session::set('tiempo', time());

			$ip = $_SERVER['HTTP_X_FORWARDED_FOR']
    		?? $_SERVER['REMOTE_ADDR']
    		?? $_SERVER['HTTP_CLIENT_IP']
    		?? '';
			$this->_usuarios->safeConexion($row['usu_dni'], $ip);

			$this->redireccionar('escuelas');

		}

    $this->_view->renderizar('index');
  }

	public function cerrar(){
		Session::destroy();
		$this->redireccionar();
	}

}
?>
