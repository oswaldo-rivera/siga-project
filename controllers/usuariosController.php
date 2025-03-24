<?php
class usuariosController extends Controller{

	private $_usuarios;
	private $_lectivo;
	private $_periodos;

	public function __construct() {
		parent::__construct();
		$this->_usuarios = $this->loadModel('usuarios');
		$this->_lectivo = $this->loadModel('lectivo');
		$this->_periodos = $this->loadModel('periodos');
	}

	public function index(){

	}

  public function crearcuenta(){
		$this->_view->sectionTitle = 'Nueva Cuenta';
		$this->_view->setJs(array('usuarios'));

    // validación y registro de formulario enviado
		if($this->getInt('enviardata')==1) {
			$this->_view->dni = $this->getInt('us_dni');
      $this->_view->email = $this->getPostParam('us_email');
			$data = $_POST;

      // GUARDAR DATOS Y CREAR USUARIO (no activo)
			$code = $this->_usuarios->saveUsuario(
				$this->getInt('us_dni'),
        $this->getPostParam('us_email'),
				$this->getPostParam('us_pass'),
				$this->getPostParam('us_nombre'),
				$this->getPostParam('us_apellido'),
				$this->getPostParam('us_rol'),
			);

      $dni = $this->getInt('us_dni');
      $usermail = $this->getPostParam('us_email');
			$nombre = $this->getPostParam('us_nombre');
			$apellido = $this->getPostParam('us_apellido');
      $fullname = ucwords(strtolower($nombre . ' ' . $apellido));
      $asunto  = 'Skole - Confirmación de cuenta de usuario';

      $mensaje = '<html>
									<head>
										<title>Skole - Confirmacion de cuenta de usuario</title>
                    <style>h3 {text-align: center;} small { font-size: 0.8em;}</style>
									</head>
									<body>
										<p>Estimado/a: ' . $fullname . '</p>
										<p>Para continuar con el registro, necesitamos asegurarnos de que tengas acceso a tu e-mail.</p>
										<p>Por esto enviamos el siguiente código de confirmación</p>
                    <h3>' . $code . '</h3>
										<p>El código de confirmación debe ser ingresado dentro de las próximas 24 hs. Si vence este plazo, el código caduca.</p>
										<p>Una vez confirmado, podrás ingresar al sistema con tu E-mail o con tu DNI.</p>
										<p>Muchas gracias.</p>
										<small>Este mensaje se genera automáticamente, por favor no respondas al remitente. 
                    Una vez usado el Código, podés borrar este E-mail.</small>
									</body>
									</html>';

      $result = $this->enviarEmail($fullname,$usermail,$asunto,$mensaje);
				if( $result == 1 ){
					$this->redireccionar('usuarios/confirmar/'.$dni);
				  exit;
				} else {
					$this->_view->setAlert('Error: No se pudo enviar el E-mail','danger');
					$this->redireccionar();
				  exit;
				}

		}

    $this->_view->renderizar('crearcuenta');
  }

  public function confirmar($dni){
		$this->_view->sectionTitle = 'Confirmar cuenta';
		$this->_view->setJs(array('confirmar'));

    // validación y registro de formulario enviado
		if( $this->getInt('codenviado')==1 ) {	
			$codigo = $this->getPostParam('user_code');
			// todo bien hasta aca
			$row = $this->_usuarios->getCodigo($dni,$codigo);
			if (!$row) {
				$this->_view->setAlert('Error: El código ingresado es incorrecto','danger');
			} else {
				$date = date("Y-m-d h:i:s");
				$vence = $row['cod_vence'];
				$date_time = strtotime($date);
				$vence_time = strtotime($vence);
				if ($date_time > $vence_time){
					$this->_view->setAlert('El código ingresado ya venció,<br>porque pasaron más de 24 Hs.<br>desde que lo enviamos a tu E-mail','danger');
					$this->_view->cod_vencido = true;
				} else {
					// ACTIVAR CUENTA
					$this->_usuarios->setStatusUsuario($dni,1);

					// INICIAR SESION
					$roles = [1=>'preceptor',2=>'docente',3=>'director',4=>'admin'];
					$user = $this->_usuarios->getUsuarioByDni($dni);
					Session::set('autenticado', true);
					Session::set('usuario_dni', $dni);
					Session::set('usuario', $user['usu_nombre'] . " " . $user['usu_apellido']);
					Session::set('iniciales', $this->_view->userIniciales($user['usu_nombre'],$user['usu_apellido']));
					Session::set('level', $user['usu_role']);
					Session::set('numlevel', array_search($user['usu_role'],$roles));
					Session::set('tiempo', time());
					
					$this->_view->setAlert('Tu cuenta fue confirmada correctamente <br><b>¡Bienvenido/a!</b><br>Para comenzar, hacé clic en el botón <b>( + )</b><br> y registrá las escuelas en las que trabajas.','info');
					$this->redireccionar('escuelas');
					exit;
				}
			}
    }

    $this->_view->renderizar('confirmar');
  }

	public function enviarcodigo() {
		$this->_view->sectionTitle = 'Restablecer contraseña';

		if( $this->getInt('enviaralemail')==1 ) {
			$email_provisto = $this->getPostParam('us_email');
			$row = $this->_usuarios->getUsuarioByEmail($email_provisto);
			if (!$row) {
				$this->_view->setAlert('Error: El E-mail ingresado no está registrado en el sistema para ningún usuario','danger');
				$this->_view->email = $email_provisto;
				$this->redireccionar('usuarios/enviarcodigo');
				exit;
			} else {
				$dni = $row['usu_dni'];
				$code = $this->_usuarios->updateCodigo($dni);
				$nombre = $row['usu_nombre'];
				$apellido = $row['usu_apellido'];
					$usermail = $row['usu_email'];
					$fullname = ucwords(strtolower($nombre . ' ' . $apellido));
					$asunto = "Skole - Restablecimiento de contraseña";
					$mensaje = '<html>
									<head>
										<title>' . $asunto . '</title>
                    <style>h3 {text-align: center;} small { font-size: 0.8em;}</style>
									</head>
									<body>
										<p>Estimado/a: ' . $fullname . '</p>
										<p>Hemos generado un nuevo código para reestablecer tu contraseña.</p>
										<h3>' . $code . '</h3>
										<p>Este código debe ser ingresado dentro de las próximas 24 hs. Después de este plazo el código caduca.</p>
										<p>Una vez ingresado, podrás crear una nueva contraseña para tu cuenta.</p>
										<p>Si no fuiste vos quien solicitó reestablecer la contraseña, hace caso omiso a este email. No es necesario realizar ninguna acción adicional.</p>
										<p>Muchas gracias.</p>
										<small>Este mensaje se genera automáticamente, por favor no respondas al remitente. 
                    Una vez usado el Código, podés borrar este E-mail.</small>
									</body>
									</html>';
          $result = $this->enviarEmail($fullname,$usermail,$asunto,$mensaje);
				if( $result == 1 ){
					$this->_view->dni = $dni;
					$this->redireccionar('usuarios/recibircodigo/'.$dni);
					exit;
				} else {
					$this->_view->setAlert('Error: No se pudo enviar el E-mail','danger');
					$this->redireccionar('usuarios/enviarcodigo');
					exit;
				}
			}
		}

		$this->_view->renderizar('enviarcodigo');
	}


  public function recibircodigo($dni){
		$this->_view->sectionTitle = 'Restablecer contraseña';
		$this->_view->setJs(array('confirmar'));

    // validación y registro de formulario enviado
		if( $this->getInt('cod_enviado')==1 ) {	
			$codigo = $this->getPostParam('user_code');
			// todo bien hasta aca
			$row = $this->_usuarios->getCodigo($dni,$codigo);
			if (!$row) {
				$this->_view->setAlert('Error: El código ingresado es incorrecto','danger');
			} else {
				$date = date("Y-m-d h:i:s");
				$vence = $row['cod_vence'];
				$date_time = strtotime($date);
				$vence_time = strtotime($vence);
				if ($date_time > $vence_time){
					$this->_view->setAlert('El código ingresado ya venció,<br>porque pasaron más de 24 Hs.<br>desde que lo enviamos a tu E-mail','danger');
					$this->_view->cod_vencido = true;
				} else {
					// CREA NUEVA CONTRASEÑA
					$this->redireccionar('usuarios/nuevacontrasena/'.$dni);
					exit;
				}
			}
    }

    $this->_view->renderizar('recibircodigo');
  }

  public function nuevacontrasena($dni){
		$this->_view->sectionTitle = 'Restablecer contraseña';
		$this->_view->setJs(array('contrasena'));

    $this->_view->dni = $dni;

    // proceso para actualizar contraseña
    if( $this->getInt('enviarcontrasena')==1 ) {
      $result = $this->_usuarios->updatePassword(
				$this->getInt('us_dni'),
				$this->getPostParam('us_pass'),
			);
      if($result) {
        $this->_view->setAlert('Tu contraseña fue restablecida correctamente <br>Ahora podés ingresar con tus nuevas credenciales.','info');
				$this->redireccionar('login');
      }
    }
		// redirecionar hacia login

    $this->_view->renderizar('nuevacontrasena');
  }

	public function enviarEmail($d_name,$d_email,$subjet,$message) {
    $enviado = 0;
		$this->getMailer();
		$email = new PHPMailer\PHPMailer\PHPMailer();
		$email->IsSMTP();
		$email->SMTPAuth = true;
		$email->SMTPSecure = 'tsl';
		$email->Host = "smtp.gmail.com";
		$email->Port = 587;
		$email->Username = "skoletek@gmail.com";
		$email->Password = "ulrugullgrjkrimc "; // application password generated on gmail security
		$email->setFrom('skoletek@gmail.com', 'Sistema Skole');
		$email->addAddress($d_email, $d_name);
	  $email->isHTML(true);
	  $email->Subject  = $subjet;
		$email->CharSet = 'UTF-8';
	  $email->MsgHTML($message);
		$email->AltBody = 'Su servidor de correo no soporta HTML';

    try {
      $email->send();
      return 1;
    } catch (Exception $e) {
      return 0;
    }
	}

  public function usuarioExiste() {
		echo $this->_usuarios->usuarioExiste($this->getInt('dni'));
	}

  public function emailExiste() {
    $email = $this->getPostParam('email');
    if($this->validarEmail($email)) {
      echo $this->_usuarios->emailExiste($email); // 0 = nuevo  1 = existe
    } else {
      echo '2'; // 2 = error
    }
	}

}
?>
