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
	
	indexar_datos(DIR_UHFAPP);
    tiempo_de_ejecucion();

	echo "Instalacion Exitosa."

?>