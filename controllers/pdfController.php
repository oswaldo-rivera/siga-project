<?php

class pdfController extends Controller {
	private $_pdf;
  private $_informespdf;


	public function __construct(){
		parent::__construct();
		$this->_informespdf = $this->loadModel('informespdf');  // carga modelo PDF
		$this->getLibrary('tcpdf/tcpdf');
		$this->_pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
		// remove default header/footer
		$this->_pdf->setPrintHeader(false);
		$this->_pdf->setPrintFooter(false);
	}

	// metodo obligatorio, aunque no contenga nada
	public function index(){ }

	// cada metodo corresponde a un documento pdf
	// los parametros se envian por metodo get via url, separados por "/"

	public function print($t,$g,$e=0) {
    $perid = Session::get('perid');
		$lectivo = substr($perid,0,4); // primeros 4 dígitos
    $periodo = substr($perid,-1);  // último dígito
		$turno = $t;
		$grupo = $g;
		$estudiante = $e;
		$data_esc = $this->_informespdf->getEscuelaFPDF();
		$data_gru = $this->_informespdf->getGrupoFPDF($grupo);
		$data_est = $this->_informespdf->getEstudiantesFPDF($grupo,$estudiante);
		$data_reg = $this->_informespdf->getRegistrosFPDF($grupo,$estudiante);
		require_once ROOT . 'public' . DS . 'files' . DS . 'informes.php';
	}

}

?>
