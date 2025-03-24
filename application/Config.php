<?php
// definicion de carpetas raiz de la aplicacion
define ('HOST', 'siga');
define ('BASE_URL','http://localhost/'. HOST . '/');
define ('DEFAULT_CONTROLLER','index');
define ('DEFAULT_LAYOUT','default');

// definicion de constantes de la aplicacion
define ('APP_NAME','SIGA');
define ('APP_SLOGAN','...');
define ('APP_COMPANY','');
define ('DEFAULT_MAIL','siga_universitario@gmail.com');
define ('SESSION_TIME', 5); // la sesion se cierra tras 5 minutos
define ('DEFAULT_ROLE', 'estudiante');
define ('HASH_KEY','Z8PUyhGR32McP'); // contanste de encriptación (NO CAMBIAR)

// definicion de constantes de conexion a base de datos
define ('DB_HOST','localhost' );
define ('DB_USER','siga_admin');
define ('DB_PASS','d33B5VPDGrYnqtBy71x@zd');
define ('DB_NAME','siga_db');
define ('DB_CHAR','utf8');

?>
