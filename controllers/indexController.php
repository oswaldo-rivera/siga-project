<?php
class indexController extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}

  public function index(){
		if(Session::get('autenticado')){
			$this->redireccionar('index/home');
		}

		$this->_view->sectionTitle = 'Inicio';
    
    $this->_view->renderizar('index');
	}
  
  public function home(){
    if(!Session::get('autenticado')){
      $this->redireccionar();
		}
    
		$this->_view->sectionTitle = 'Inicio';
    $this->_view->mActivo = "home";

		// Prueba de alertas
		// $this->_view->setAlert('Confirmar que quieres hacer eso que dijiste que querías hacer','confirm','confirmado');
		// $this->_view->setAlert('Algun mensaje','success');

    $this->_view->renderizar('home');
	}
  
  public function acercade(){
    
		$this->_view->sectionTitle = 'Acerca de Skole';
    $this->_view->mActivo = "home";

    $this->_view->renderizar('acercade');
	}
  
  public function primerospasos(){
    
		$this->_view->sectionTitle = 'Ventajas de Skole';
    $this->_view->mActivo = "home";

    $this->_view->renderizar('primerospasos');
	}
  
  
  public function colaborar(){
    
		$this->_view->sectionTitle = 'Colaborar con Skole';
    $this->_view->mActivo = "home";

    $this->_view->renderizar('colaborar');
	}
  
  public function contacto(){
    
		$this->_view->sectionTitle = 'Formulario de Contacto';
    $this->_view->mActivo = "home";

    if($this->getInt('enviar')==1) {
      $for = 'skoletek@gmail.com';
      $subject = 'Comentarios de SKOLE';
      $message = $this->getPostParam('c_texto');
      $from = $this->getPostParam('c_email');
      $header = 'From:' . $from . "\r\n" 
                . 'Reply-To: ' . $from . "\r\n" 
                . 'X-Mailer: PHP/' . phpversion();

      $enviado = mail($for, $subject, $message, $header);
        if($enviado) { 
          $this->_view->setAlert('Sus comentarios fueron enviados', 'success');
        } else {
          // $this->_view->setAlert($header . '-' . $message, 'success');
          $this->_view->setAlert('Error al enviar su comentario. <br>Por favor, intente nuevamente', 'danger');
        }
      

    }
    $this->_view->renderizar('contacto');
	}

}

?>
