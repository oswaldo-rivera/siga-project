<?php
class Hash {

  // esta funcion cifra las contraseñas para guardar las en la BD
	public static function getHash($algoritmo, $data, $key){
		$hash = hash_init($algoritmo, HASH_HMAC, $key);
		hash_update($hash, $data);

		return hash_final($hash);
	}

	public static function getRandom(){
		$fecha = new DateTime();
		$ts = rand(100000, $fecha->getTimestamp());
		return substr($ts, -6);
	}
}
?>
