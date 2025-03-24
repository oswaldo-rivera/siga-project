<?php
class Model {
	protected $_db;

  // crear instancia de Conexion a BD en el objeto "$_db"
	public function __construct(){
		$this->_db = new Database();
	}
}

?>
