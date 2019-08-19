<?php
	
	header('Content-Type: text/html; charset=UTF-8');
	ini_set("display_errors", "On");
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL | E_STRICT);
	header("Content-Type: text/html; charset=UTF-8");
	date_default_timezone_set('America/Argentina/Tucuman');
	setlocale(LC_ALL, 'es-AR');

	// Esta ruta tiene que ser estatica
	require_once("cfg/config.php"); // Esta tiene que ir completa

	indexar_datos(DIR_UHFAPP);

	$bd = new BaseDeDatos(DB_SERVER, DB_USER, DB_PASSUSER, DB_NAME);
	$archivo = DIR_TMP.'/abonados_gps.csv';
	
	$filas = $bd->query(MYSQL_PTS_GPS_MAS_RECIENTES);

	/*	
		tabla crudo

		SELECT c1.`n_abonado_c_uhfapp`, c1.`lat_c_uhfapp`, c1.`lng_c_uhfapp` 
		FROM `crudo_uhfapp` c1 
		WHERE c1.`fecha_c_uhfapp` = (	SELECT MAX(`fecha_c_uhfapp`) 
										FROM `crudo_uhfapp` c2 
										WHERE c1.`n_abonado_c_uhfapp` = c2.`n_abonado_c_uhfapp`)
	
		con join

		SELECT c1.`n_abonado_c_uhfapp`, c1.`lat_c_uhfapp`, c1.`lng_c_uhfapp`
		FROM ccc.`crudo_uhfapp` c1
		INNER JOIN 
		(SELECT c2.`n_abonado_c_uhfapp`, MAX(c2.`fecha_c_uhfapp`) as fechaMasReciente
		FROM ccc.`crudo_uhfapp` c2
		GROUP BY c2.`n_abonado_c_uhfapp`) AS c3 
		ON c1.`n_abonado_c_uhfapp` = c3.`n_abonado_c_uhfapp`
		AND c1.`fecha_c_uhfapp` = c3.`fechaMasReciente`

		///////////////////////////////////////////////////////////////////////////////////////////
		
		tablas indivuales <- En uso

		SELECT a.numero_abonado, c.lat_c_gps, c.lng_c_gps
		FROM coordenadas_gps c
		INNER JOIN (

			SELECT i1.id_c_gps
			FROM ccc.`instalaciones` i1
			INNER JOIN (

				SELECT i2.id_domicilio, MAX(i2.`fecha_instalacion`) fechaMax
				FROM ccc.`instalaciones` i2
				GROUP BY i2.id_domicilio

			) i3 
			ON i1.id_domicilio = i3.id_domicilio 
			AND i1.fecha_instalacion = i3.fechaMax 

		) i4
		ON c.id_c_gps = i4.id_c_gps
		INNER JOIN domicilios d
		ON c.id_domicilio = d.id_domicilio
		INNER JOIN abonados a
		ON d.id_persona = a.id_persona
	*/

	$fp = fopen($archivo, 'w');

	foreach ($filas as $val) {
		fputcsv($fp, $val);
	}

	fclose($fp);

	if (file_exists($archivo)) {			
		header('Content-Description: File Transfer');
		header("Content-type: text/csv");
		header('Content-Disposition: attachment; filename='.basename($archivo));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		ob_end_clean();
		readfile($archivo);
		unlink($archivo);
	}
	else {
		echo "Ocurrio un error durante la creación del archivo";
		echo "<br>";
	}
?>