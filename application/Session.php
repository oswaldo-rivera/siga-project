<?php
class Session {
    // Iniciar una nueva sesion o reanudar la existente
	public static function init(){
		session_start();
	}

    // destruye la sesion
	public static function destroy($clave = false) {
		if($clave){
			if(is_array($clave)){
				for($i = 0; $i< count($clave); $i++){
					if(isset($_SESSION[$clave[$i]])){
						unset($_SESSION[$clave[$i]]);
					}
				}
			} else {
				if(isset($_SESSION[$clave])){
					unset($_SESSION[$clave]);
				}
			}
		} else {
			session_destroy();
		}
	}

    // crea una variable de sesion y le asigna un valor
	public static function set($clave, $valor){
		if(!empty($clave))
		$_SESSION[$clave] = $valor;
	}

    // obtienen el valor de una variable de sesion
	public static function get($clave){
		if(isset($_SESSION[$clave]))
		return $_SESSION[$clave];
	}

	public static function acceso($level){
		// si existe la variable de sesion "autenticado" ...
        if(!Session::get('autenticado')){
			header('Location:' . BASE_URL . 'error/access/5050');
			exit;
		}

        // inicia el contador de tiempo de session
		Session::tiempo();

        // verifica que el nivel de acceso requerido
        // sea el adecuado para dar acceso
		if(Session::getLevel($level) > Session::getLevel(Session::get('level'))) {
			header('Location:' . BASE_URL . 'error/access/5050');
			exit;
		}
	}

    // Valida nivel de acceso para la vista
	public static function accesoView($level){
		if(!Session::get('autenticado')){
			return false;
		} else {
			if(Session::getLevel($level) > Session::getLevel(Session::get('level'))) {
				return false;
			}
		}
		return true;
	}

    // 
	public static function getLevel($level){

		// listado de roles
		$role['estudiante'] = 1;
        $role['docente'] = 2;
		$role['administrativo'] = 3;
		$role['directivo'] = 4;
        $role['admin'] = 5;

		if(!array_key_exists($level, $role)){
			throw new Exception('Error: el rol no existe');
		} else {
			return $role[$level];
		}
	}

	public static function accesoEstricto(array $level, $noAdmin = false){
		if(!Session::get('autenticado')){
			header('Location:' . BASE_URL . 'error/access/5050');
			exit;
		}

		if($noAdmin == false) {
			if(Session::get('Level') == 'admin') {
				return;
			}
		}

		if(count($level)) {
			if(in_array(Session::get('level'), $level)){
				return;
			}
		}

		header('Location:' . BASE_URL . 'error/access/5050');
	}

	public static function acccesoViewEstricto (array $level, $noAdmin = false ){
		if(!Session::get('autenticado')){
			return false;
		}

		Session::tiempo();

		if($noAdmin == false) {
			if(Session::get('Level') == 'admin') {
				return true;
			}
		}

		if(count($level)) {
			if(in_array(Session::get('level'), $level)){
				return true;
			}
		}

		return false;
	}

	public static function tiempo(){
		if(!Session::get('tiempo') || !defined('SESSION_TIME')) {
			throw new Exception('No se ha definido el tiempo de sessión');
		}

		if(SESSION_TIME == 0){
			return;
		}

		if ((time() - Session::get('tiempo')) > (SESSION_TIME * 60)) {
			Session::destroy();
			header('Location:' . BASE_URL . 'error/access/8080');
		} else {
			Session::set('tiempo', time());
		}
	}

	public static function autenticar() {
		if(!Session::get('autenticado')) {
			header('location:' . BASE_URL);
			exit;
		}
			
        Session::tiempo();
	}
}

?>
