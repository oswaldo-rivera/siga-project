<?php

class Database extends PDO {
	public function __construct() {
		// Hay tres formas de establecer la codificación cuando se conecta a MySQL de PDO
		// dependiendo de la versión de PHP. El orden de preferencia sería:
		//
		// 1. Usar parametro charset en la cadena DSN
		// 2. Ejecutar SET NAMES utf8 con la opción de conexión PDO :: MYSQL_ATTR_INIT_COMMAND
		// 3. Ejecutar SET NAMES utf8 manualmente
		//
		// Aca implemento las tres formas

		$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME;
		$options = array(
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		);

		if( version_compare(PHP_VERSION, '5.3.6', '<') ) {
			if( defined('PDO::MYSQL_ATTR_INIT_COMMAND') ) {
				$options[PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES ' . DB_CHAR;
			}
		} else {
			$dsn .= ';charset=' . DB_CHAR;
		}

		parent::__construct($dsn, DB_USER, DB_PASS, $options);

		if( version_compare(PHP_VERSION, '5.3.6', '<') && !defined('PDO::MYSQL_ATTR_INIT_COMMAND') ) {
			$sql = 'SET NAMES ' . DB_CHAR;
			$this->exec($sql);
		}
	}
}

?>
