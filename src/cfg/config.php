<?php

	header('Content-Type: text/html; charset=UTF-8'); 
	ini_set("display_errors", "On");
	error_reporting(E_ALL | E_STRICT);
 	header("Content-Type: text/html; charset=UTF-8");
 	date_default_timezone_set('America/Argentina/Tucuman');
 	setlocale(LC_ALL, 'es-AR');

	// Esta ruta tiene que ser estatica
	require_once("/home/pablo/Proyectos/config/milib/php/cfg/config_milib.php"); 
		
	// Clases
	require_once(MILIB_LIB_PATH."/base_de_datos.php");	
	require_once(MILIB_LIB_PATH."/directorio.php");	
	require_once(MILIB_LIB_PATH."/log.php");	

	// Rutas
	defined('SRC_PATH') ? null : define('SRC_PATH', ROOT_PATH.DS.'ccc'.DS.'mapa_abonados'.DS.'src');
	defined('CONFIG_PATH') ? null : define('CONFIG_PATH', SRC_PATH.DS.'cfg');
	defined('LIB_PATH') ? null : define('LIB_PATH', SRC_PATH.DS.'lib');

	require_once(LIB_PATH."/funciones.php");
	require_once(CONFIG_PATH."/config_mysql.php");

	// Directorios REDEFINIR CON DIRECTORIO REAL
	defined('DIR_UHFAPP') ? null : define('DIR_UHFAPP', '/home/pablo/Proyectos/ccc/uhfapp');
	
	// Directorios Codigo
	defined('DIR_TMP') ? null : define('DIR_TMP', SRC_PATH.DS.'tmp'); // sudo chown -R www-data:www-data tmp 
	defined('DIR_DES') ? null : define('DIR_DES', SRC_PATH.DS.'des');

	// Directorios Rec
	//defined('DIR_REC_DES') ? null : define('DIR_REC_DES', '');

?>