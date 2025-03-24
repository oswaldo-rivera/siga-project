<?php
class errorController extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		// insertar JS propio de esta pagina
		// $this->_view->setJs(array('calculadora'));
		$this->_view->renderizar('index');
	}

}

?>
