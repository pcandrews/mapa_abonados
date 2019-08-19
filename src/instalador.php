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
	
	// Conectar base de datos
	$conex_root = new BaseDeDatos(DB_SERVER, DB_ROOT, DB_PASSROOT);

	// Validacion Password
	$conex_root->query(DB_PASS_VAL);

	// Crear usuario
	$conex_root->query(MYSQL_CREAR_USUARIO);

	// Garantizar privilegios usuario
	$conex_root->query(MYSQL_REV_PRIV);
	$conex_root->query(MYSQL_GRANT_PRIV);

	// Crear base de datos
	$conex_root->query(MYSQL_CREAR_BD);
	
	// Privilegios usuario.   
	$conex_root->query(MYSQL_GRANT_PRIV_BD);

	// Refrescar todos los privilegios. siempre que haya un cambio de privilegios.
	$conex_root->query(MYSQL_FLUSH_PRIV);

	// Terminar sesion root
	$conex_root->cerrar_conexion();

	/**********************/
	
	// Abrir conexion usuario ccc_admin
	$conex_ccc_admin = new BaseDeDatos(DB_SERVER, DB_USER, DB_PASSUSER);

	// Crear tablas
	$conex_ccc_admin->query(MYSQL_T_ARCHIVOS_UHFAPP);
	$conex_ccc_admin->query(MYSQL_T_CRUDO_UHFAPP);
	$conex_ccc_admin->query(MYSQL_T_PERSONAS);
	$conex_ccc_admin->query(MYSQL_T_ABONADOS);
	$conex_ccc_admin->query(MYSQL_T_DOMICILIOS);
	$conex_ccc_admin->query(MYSQL_T_MOVILES);
	$conex_ccc_admin->query(MYSQL_T_COORDENADAS);
	$conex_ccc_admin->query(MYSQL_T_INSTALACIONES);
	$conex_ccc_admin->query(MYSQL_T_FOTOS_DOMICILIOS);

	// Llenar informacion
	indexar_datos(DIR_UHFAPP);
    tiempo_de_ejecucion();


	// Terminar sesion ccc_admin
	$conex_ccc_admin->cerrar_conexion();

	echo "Instalacion Exitosa."

?>