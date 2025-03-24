<?php
class loginModel extends Model {

	public function __construct() {
		parent::__construct();
	}

	public function getUsuario($user, $pass) {
    $where = "usu_email = '$user'";
    if(is_numeric($user)){
      $where = "usu_dni = $user";
    } 
		$sql = "SELECT * FROM rc_usuarios WHERE $where 
            AND usu_pass = '" . Hash::getHash('md5',$pass, HASH_KEY) . "'";
		$datos  = $this->_db->query($sql);
		return $datos->fetch();
	}
}

?>
