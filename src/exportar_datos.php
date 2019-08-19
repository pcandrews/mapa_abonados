<?php
	
	header('Content-Type: text/html; charset=UTF-8');
	ini_set("display_errors", "On");
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL | E_STRICT);
	header("Content-Type: text/html; charset=UTF-8");
	date_default_timezone_set('America/Argentina/Tucuman');
	setlocale(LC_ALL, 'es-AR');

	// Esta ruta tiene que ser estatica
	require_once("cfg/config.php");

	try {
		$ret = exportar_datos("http://192.168.88.8/orion_ws.php?Request=geo_");
		echo $ret;
	}

	catch(Exception $e) {
		echo 'Mensaje: ' .$e->getMessage();
	}	
?>