<?php

class usuariosModel extends Model {
	public function __construct(){
		parent::__construct();
	}

	public function updatePassword($dni, $pass) {
		$sql = "UPDATE rc_usuarios SET usu_pass = :pass WHERE usu_dni = :dni";
		$result = $this->_db->prepare($sql)
		->execute(
			array(
				':pass' => Hash::getHash('md5',$pass,HASH_KEY),
				':dni' => $dni
			)
		);
		return $result;
	}

	public function updateAtributo($dni, $dato, $atributo) {
		$sql = "UPDATE rc_usuarios SET :atributo = ':dato' WHERE usu_dni = :dni";
		$result = $this->_db->prepare($sql)
		->execute(
			array(
				':atributo' => $atributo,
				':dato' => $dato,
				':dni' => $dni
			)
		);
		return $result;
	}

	public function getUsuarioByDni($dni) {
			$sql = "SELECT * FROM rc_usuarios WHERE usu_dni = $dni";
			$usuario = $this->_db->query($sql);
			$usuario->setFetchMode(PDO::FETCH_ASSOC);
			return $usuario->fetch();
	}

	public function getUsuarioByEmail($email) {
			$sql = "SELECT * FROM rc_usuarios WHERE usu_email = '$email'";
			$usuario = $this->_db->query($sql);
			$usuario->setFetchMode(PDO::FETCH_ASSOC);
			return $usuario->fetch();
	}

	public function usuarioExiste($dni) {
			$sql = "SELECT count(usu_dni) AS found FROM rc_usuarios WHERE usu_dni = $dni";
			$usuario = $this->_db->query($sql);
			$usuario->setFetchMode(PDO::FETCH_ASSOC);
			$existe = $usuario->fetch();
      return $existe['found'];
	}

	public function emailExiste($email) {
			$sql = "SELECT count(usu_email) AS found FROM rc_usuarios WHERE usu_email = '$email'";
			$usuario = $this->_db->query($sql);
			$usuario->setFetchMode(PDO::FETCH_ASSOC);
			$existe = $usuario->fetch();
      return $existe['found'];
	}

  public function saveUsuario($dni, $email, $pass, $nombre, $apellido, $role) {
    $sqlInsert = "REPLACE INTO rc_usuarios 
                  (usu_dni, 
                  usu_email, 
                  usu_pass,
                  usu_nombre,
                  usu_apellido,
                  usu_role,
                  usu_status) 
									VALUES
                  (:dni, 
                  :email, 
                  :pass,
                  :nombre,
                  :apellido,
                  :role,
                  0)";
    $result = $this->_db->prepare($sqlInsert)
    ->execute(
      array(
        ':dni'  => $dni,
        ':email' => $email,
        ':pass' => Hash::getHash('md5',$pass, HASH_KEY),
        ':nombre' => $nombre,
        ':apellido' => $apellido,
        ':role' => $role
      )
    );

    // Registro de códigos de activacion temporal via email (24 hs)
    $permitted_chars = '0123456789ABCDEFGHJKLMNPQRSTUVWXYZ';
		$code = substr(str_shuffle($permitted_chars), 0, 6);
		$vence=  date ('Y-m-d H:i:s', strtotime('+24 hours', strtotime(date('Y-m-d H:i:s'))));
    
		$sql1 = "REPLACE INTO rc_codigos (cod_codigo, usu_dni, cod_vence) VALUES ('$code', $dni, '$vence')";
		$this->_db->prepare($sql1)->execute();
		$dataresult = $code;

    return $dataresult;
  }

  public function updateCodigo($dni){
    $permitted_chars = '0123456789ABCDEFGHJKLMNPQRSTUVWXYZ';
		$code = substr(str_shuffle($permitted_chars), 0, 6);
    $vence=  date ('Y-m-d H:i:s', strtotime('+24 hours', strtotime(date('Y-m-d H:i:s'))));
    $sql1 = "REPLACE INTO rc_codigos (cod_codigo,usu_dni,cod_vence) VALUES ('$code',$dni, '$vence')";
		$this->_db->prepare($sql1)->execute();
		$dataresult = $code;
    return $dataresult;
  }

  public function getCodigo($dni,$codigo) {
    $sqlSelect = "SELECT * FROM rc_codigos WHERE usu_dni = $dni AND cod_codigo = '$codigo'";
    $usuarios = $this->_db->query($sqlSelect);
    $usuarios->setFetchMode(PDO::FETCH_ASSOC);
    return $usuarios->fetch();
  }

  public function getStatus($dni) {
    $sql = "SELECT usu_status FROM rc_usuarios WHERE usu_dni = $dni";
		$usuario = $this->_db->query($sql);
		$usuario->setFetchMode(PDO::FETCH_ASSOC);
		$status = $usuario->fetch();
    return $status['usu_status'];
  }

  public function getAllDocentes(){
    $sql = "SELECT u.usu_dni, u.usu_apellido, u.usu_nombre, u.usu_role, esc.esc_escuela 
    FROM rc_usuarios u 
    INNER JOIN 
      (SELECT eu.usu_dni, es.esc_escuela 
      FROM rc_esc_usu eu 
      INNER JOIN rc_escuelas es 
      ON eu.esc_id = es.esc_id) esc 
    ON esc.usu_dni = u.usu_dni";
    $usuarios = $this->_db->query($sql);
    $usuarios->setFetchMode(PDO::FETCH_ASSOC);
    return $usuarios->fetchall();
  }

  public function getUsuarios(){
    $esc_id = Session::get('usuario_escid');
    $sql = "SELECT doc.usu_dni, 
                doc.usu_nombre, 
                doc.usu_apellido, 
                doc.usu_role, 
                doc.usu_status,
                gru.are_id, 
                gru.are_nombre,
                gru.gru_id,
                gru.gru_anio, 
                gru.gru_grupo, 
                gru.gru_turno 
              FROM rc_usuarios doc
	            INNER JOIN 
	            (SELECT eu.usu_dni, es.esc_escuela FROM rc_esc_usu eu 
      	        INNER JOIN rc_escuelas es 
      	        ON eu.esc_id = es.esc_id 
     	          WHERE eu.esc_id = $esc_id) esc
              ON esc.usu_dni = doc.usu_dni
              LEFT JOIN 
              (SELECT gu.usu_dni, ar.are_id, ar.are_nombre, gr.gru_id, gr.gru_anio, gr.gru_grupo, gr.gru_turno 
     	          FROM rc_gru_usu gu 
     	            INNER JOIN rc_areas ar ON ar.are_id = gu.are_id	
     	            INNER JOIN rc_grupos gr ON gr.gru_id = gu.gru_id
                  WHERE gr.esc_id = $esc_id) gru
              ON gru.usu_dni = doc.usu_dni
              ORDER BY doc.usu_apellido";
    $usuarios = $this->_db->query($sql);
    $usuarios->setFetchMode(PDO::FETCH_ASSOC);
    return $usuarios->fetchall();
  }

  public function safeConexion($dni, $ip) {
    date_default_timezone_set('America/Argentina/Buenos_Aires');
    $fecha = date("Y-m-d H:i:s");
    $agente = $_SERVER['HTTP_USER_AGENT'];
    $sqlInsert = "INSERT INTO rc_conexiones (usu_dni,cnx_date,cnx_agent,cnx_ip) VALUES ($dni,'$fecha','$agente','$ip')";
    $result = $this->_db->prepare($sqlInsert)->execute();
    return $result;
  }

	public function disjoinDocenteEscuela($dni) {
    $esc_id = Session::get('usuario_escid');
		$sql = "DELETE FROM rc_gru_usu gu INNER JOIN rc_grupos g ON gu.gru_id = g.gru_id WHERE g.esc_id = $esc_id AND gu.usu_dni = $dni";
		$result1 = $this->_db->prepare($sql)->execute();
		$sql = "DELETE FROM rc_esc_usu WHERE esc_id = $esc_id AND gu.usu_dni = $dni";
		$result0 = $this->_db->prepare($sql)->execute();
		return $result0;
	}

	public function disjoinDocenteAreaGrupo($dni, $are_id, $gru_id) {
		$sql = "DELETE FROM rc_gru_usu WHERE usu_dni = $dni AND are_id = $are_id AND gru_id = $gru_id";
		$result = $this->_db->prepare($sql)->execute();
		return $result;
	}

  public function setStatusUsuario($dni,$status) {
    $sql = "UPDATE rc_usuarios SET usu_status = $status WHERE usu_dni = $dni";
    $result = $this->_db->prepare($sql)->execute();
    return $result;
	}

}
?>
