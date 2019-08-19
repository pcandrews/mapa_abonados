<?php

	header("Content-Type: text/html; charset=UTF-8");
	ini_set("display_errors", "On");	
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL | E_STRICT);
	header("Content-Type: text/html; charset=UTF-8");
	date_default_timezone_set("America/Argentina/Tucuman");
	setlocale(LC_ALL, "es-AR");
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
	
	function indexar_datos ($ruta) {		
		$fp = fopen(DIR_TMP.'/lock.txt', 'r+');
  		//Evitar ejecucion multiple
  		if(flock($fp, LOCK_EX)) {	
  			$archivos_nuevos = array();		
  			$archivos_nuevos = encontrar_archivos_nuevos ($ruta);	
  			//var_dump($archivos_nuevos);
			if (count($archivos_nuevos) > 0) {
				$id_archivos = indexar_archivo($archivos_nuevos);
				indexar_datos_crudos_csv($id_archivos, $archivos_nuevos);
				//cursor();
				cargar_datos_tablas();
				cargar_fotos();
			}
	    	flock($fp, LOCK_UN);
	  	} 
	  	else {
	    	echo "Fallo al intentar obtener el archivo lock";
	  	}

		fclose($fp);
		  
		//echo "indexar datos";
	}


	/*
	  	Descripcion: 
			Busca e indexa los archivos de una ruta dada, que no esten en la base de datos:
			Buscar archivo por nombre (path completo):
	  		Si lo encuentra:
	  			Comparar ultima modificacion:
	       			Si la ultima modificacion es diferente:
	       			 	Comparar hash:
	       			 		Si es diferente:	
	       			 			Se indexa.   				 	
	      	Si no lo encuentra:
	      		Lo indexa. 	
	*/
	function encontrar_archivos_nuevos ($ruta) {		
		$bd = new BaseDeDatos(DB_SERVER, DB_USER, DB_PASSUSER, DB_NAME);
		$dir = new Directorio();
		$rutas_archivos = array();
		$archivos_nuevos = array();

		$rutas_archivos = $dir->listar_archivos($ruta);

		// echo "<br><br><br><br><br><br>";
		// var_dump($rutas_archivos);
		// echo "<br><br><br><br><br><br>";
		//for($i=0; $i<10; $i++) {
		for($i=0; $i<count($rutas_archivos); $i++) {
			$filas_encontradas = $bd->num_rows("SELECT ruta_a_uhfapp FROM ccc.archivos_uhfapp WHERE ruta_a_uhfapp = '{$rutas_archivos[$i]}' LIMIT 1");

			if($filas_encontradas > 0) {
				$resultado = $bd->fetch_array("SELECT ultima_modificacion_a_uhfapp, hash_a_uhfapp FROM ccc.archivos_uhfapp WHERE ruta_a_uhfapp = '{$rutas_archivos[$i]}' LIMIT 1");
				$ultima_modificacion = $resultado["ultima_modificacion_a_uhfapp"];
				$hash = $resultado["hash_a_uhfapp"];

				if(filemtime($rutas_archivos[$i]) != $ultima_modificacion) {					
					if(hash_file('crc32' , $ruta[$i]) != $hash) {
						array_push($archivos_nuevos,$rutas_archivos[$i]);
					}
				}
			} else{
				array_push($archivos_nuevos,$rutas_archivos[$i]);
			}
		}
		$bd->cerrar_conexion();	

		//var_dump($archivos_nuevos);
		return $archivos_nuevos;
	}


	/*
		Retorna el indice.
	*/
	function indexar_archivo ($archivos) {		
		$bd = new BaseDeDatos(DB_SERVER, DB_USER, DB_PASSUSER, DB_NAME);
		$id_archivos = array();

		$j=0;
		for($i=0; $i<count($archivos); $i++) {
			$nombre = basename($archivos[$i]);
			$extension = pathinfo($nombre, PATHINFO_EXTENSION);

			if(($extension=='csv') || ($extension=='kml') || ($extension=='jpg')) {
				$ultima_modificacion = filemtime($archivos[$i]);
				$hash = hash_file('crc32' , $archivos[$i]);

				$fp = fopen($archivos[$i], "rb");
				$tamanio = filesize($archivos[$i]);

				if($tamanio > 0)
					$contenido = fread($fp, $tamanio);
				
				$contenido = addslashes($contenido);
				//$contenido = gzcompress($contenido, 9);
				fclose($fp); 

				//extraer texto
				//SELECT CONVERT(UNCOMPRESS(contenido_a_uhfapp) USING utf8) FROM ccc.archivos_uhfapp;
				//extraer imagenes
				//SELECT UNCOMPRESS(contenido_a_uhfapp) FROM ccc.archivos_uhfapp where extension_a_uhfapp = "jpg";
				$bd->query("INSERT INTO ccc.archivos_uhfapp (nombre_a_uhfapp, 
															 ruta_a_uhfapp, 
															 extension_a_uhfapp, 
															 ultima_modificacion_a_uhfapp,
															 hash_a_uhfapp,
															 contenido_a_uhfapp) 
							VALUES ('$nombre',
									'$archivos[$i]',
									'$extension',
									'$ultima_modificacion',
									'$hash',
									COMPRESS('$contenido'))");

				$id_archivo = $bd->fetch_array("SELECT MAX(id_a_uhfapp) FROM ccc.archivos_uhfapp");

				$id_archivos[$i] = $id_archivo[0];
			}
		}
		$bd->cerrar_conexion();

		return $id_archivos;
	}

	function tiempo_de_ejecucion() {
		$tiempo_de_ejecucion = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
		echo "Tiempo de ejecución: ".number_format($tiempo_de_ejecucion,3,',','')."seg."; 	
		echo "<br>";
	}


	/*
		Indexa los datos dentro de archivo csv. 
		Usa una tabla temporal.
	*/
	function indexar_datos_crudos_csv ($id_archivos, $archivos) {
		$bd = new BaseDeDatos(DB_SERVER, DB_USER, DB_PASSUSER, DB_NAME);

		$bd->query("SET AUTOCOMMIT=0");
		$bd->query("START TRANSACTION;");

		$bd->query("DROP TABLE IF EXISTS ccc.crudo_uhfapp_temp;");
		$bd->query("CREATE TABLE IF NOT EXISTS ccc.crudo_uhfapp_temp (		
						id_c_uhfapp_t INT(11) UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE,
						id_a_uhfapp INT(11) UNSIGNED NOT NULL,

						nombre_c_uhfapp_t VARCHAR(255),
						mac_c_uhfapp_t VARCHAR(255),

						n_abonado_c_uhfapp_t INT(11) UNSIGNED,

						lat_c_uhfapp_t FLOAT(10,6) NULL,
						lng_c_uhfapp_t FLOAT(10,6) NULL,
						gps_dcml_c_uhfapp_t POINT,
						gps_sexa_c_uhfapp_t VARCHAR(255),

						obs_c_uhfapp_t TEXT,
						
						fecha_c_uhfapp_t DATETIME,

						PRIMARY KEY(id_c_uhfapp_t),
						FOREIGN KEY (id_a_uhfapp) REFERENCES ccc.archivos_uhfapp (id_a_uhfapp) 
							ON DELETE CASCADE 
							ON UPDATE CASCADE
					) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 DEFAULT COLLATE=utf8_spanish_ci;");


			$bd->query("CREATE TABLE IF NOT EXISTS ccc.crudo_uhfapp_nuevos (
							id_c_uhfapp INT(11) UNSIGNED NOT NULL UNIQUE,
							id_a_uhfapp INT(11) UNSIGNED NOT NULL,

							nombre_c_uhfapp VARCHAR(255),
							mac_c_uhfapp VARCHAR(255),

							n_abonado_c_uhfapp INT(11) UNSIGNED,

							lat_c_uhfapp FLOAT(10,6) NULL,
							lng_c_uhfapp FLOAT(10,6) NULL,
							gps_dcml_c_uhfapp POINT,
							gps_sexa_c_uhfapp VARCHAR(255),

							obs_c_uhfapp TEXT,
							
							fecha_c_uhfapp DATETIME,

							PRIMARY KEY(id_c_uhfapp),
							FOREIGN KEY (id_a_uhfapp) REFERENCES ccc.archivos_uhfapp (id_a_uhfapp) 
								ON DELETE CASCADE 
								ON UPDATE CASCADE
						) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 DEFAULT COLLATE=utf8_spanish_ci;");


		for ($i=0; $i<count($archivos); $i++) {

				/*echo "a: ".$id_archivo[$i];
				echo "<br>";*/

				$extension = pathinfo($archivos[$i], PATHINFO_EXTENSION);				
				if($extension == 'csv') {
					$fecha = str_replace(".csv", "", $archivos[$i]);
					//echo $fecha;
					//echo "<br>";
					
					$fecha = pathinfo($archivos[$i]);
					$fecha = $fecha['filename'];
					$fecha = substr($fecha,-8);
					//echo $fecha;
					//echo "<br>";
					
					$año = substr($fecha,0,4);
					$mes = substr($fecha,4,2);
					$dia = substr($fecha,6,2);

					$fecha = "{$año}-{$mes}-{$dia}";
					//echo $fecha;
					//echo "<br>";		
										
					/////////////////
					// simplificar //
					////////////////////////////////////////
					// añadir foreing key de los archivos //
					// //////////////////////////////////////
					// prevenir ingresos de lat y long = 0 //
					// //////////////////////////////////////
					$bd->query("LOAD DATA LOCAL INFILE '$archivos[$i]'
								INTO TABLE ccc.crudo_uhfapp_temp
								FIELDS TERMINATED BY '\",'
								LINES TERMINATED BY '\\n'
									(@nombre, 
									@mac_celular, 
									@numero_abonado, 
									@coord_gps_dcml, 
									@coord_gps_sexa, 
									@observacion)
								SET							 
									nombre_c_uhfapp_t = SUBSTR(TRIM(@nombre), 2),
									mac_c_uhfapp_t = SUBSTR(TRIM(@mac_celular), 2),
									n_abonado_c_uhfapp_t = CAST(SUBSTR(TRIM(@numero_abonado), 2) AS UNSIGNED),

									gps_sexa_c_uhfapp_t = SUBSTR(TRIM(@coord_gps_sexa), 2),
									lat_c_uhfapp_t = CAST(SUBSTRING_INDEX(SUBSTR(TRIM(@coord_gps_dcml), 2), ',', 1) AS DECIMAL(10,6)),
									lng_c_uhfapp_t  = CAST(SUBSTRING_INDEX(SUBSTR(TRIM(@coord_gps_dcml), 2), ',', -1) AS DECIMAL(10,6)),					
									gps_dcml_c_uhfapp_t  = ST_GeomFromText(CONCAT('POINT(',REPLACE(SUBSTR(TRIM(@coord_gps_dcml), 2), ',',' '),')')),

									obs_c_uhfapp_t  = SUBSTR(TRIM(@observacion), 2),
									obs_c_uhfapp_t  = SUBSTRING(obs_c_uhfapp_t , 1, LENGTH(obs_c_uhfapp_t)-1),
									obs_c_uhfapp_t  = REPLACE(obs_c_uhfapp_t, '\'', '\\''),
									obs_c_uhfapp_t  = REPLACE(obs_c_uhfapp_t, '\"', 'º|@#'),
									obs_c_uhfapp_t  = REPLACE(obs_c_uhfapp_t, 'º|@#', '\"'),

									fecha_c_uhfapp_t = '$fecha',
									id_a_uhfapp='$id_archivos[$i]';");					
			}
		}

		//borra las filas con valores de lat o lng igual 0		
		$bd->query('DELETE FROM ccc.crudo_uhfapp_temp WHERE lat_c_uhfapp_t = 0 OR lng_c_uhfapp_t = 0');
		
		//inserta en la tabla crudo_uhfapp registros que tengan lat, lng y numero de abonados diferentes a los almacenados. Para evitar duplicados. 
		$bd->query("INSERT INTO ccc.crudo_uhfapp (	nombre_c_uhfapp,
													mac_c_uhfapp,
													n_abonado_c_uhfapp,
													lat_c_uhfapp,
													lng_c_uhfapp,
													gps_dcml_c_uhfapp,
													gps_sexa_c_uhfapp,
													obs_c_uhfapp,
													fecha_c_uhfapp,
													id_a_uhfapp )	  
					SELECT	nombre_c_uhfapp_t,
							mac_c_uhfapp_t,
							n_abonado_c_uhfapp_t,
							lat_c_uhfapp_t,
							lng_c_uhfapp_t,
							gps_dcml_c_uhfapp_t,
							gps_sexa_c_uhfapp_t,
							obs_c_uhfapp_t,
							fecha_c_uhfapp_t, 
							id_a_uhfapp
					FROM ccc.crudo_uhfapp_temp
					WHERE NOT EXISTS (	SELECT lat_c_uhfapp, lng_c_uhfapp, n_abonado_c_uhfapp 
										FROM ccc.crudo_uhfapp  
										WHERE ccc.crudo_uhfapp.lat_c_uhfapp = ccc.crudo_uhfapp_temp.lat_c_uhfapp_t 
										AND ccc.crudo_uhfapp.lng_c_uhfapp =  ccc.crudo_uhfapp_temp.lng_c_uhfapp_t 
										AND ccc.crudo_uhfapp.n_abonado_c_uhfapp = ccc.crudo_uhfapp_temp.n_abonado_c_uhfapp_t )
					ORDER BY id_c_uhfapp_t ASC;");

		$bd->query("INSERT INTO ccc.crudo_uhfapp_nuevos (	id_c_uhfapp,
															nombre_c_uhfapp,
															mac_c_uhfapp,
															n_abonado_c_uhfapp,
															lat_c_uhfapp,
															lng_c_uhfapp,
															gps_dcml_c_uhfapp,
															gps_sexa_c_uhfapp,
															obs_c_uhfapp,
															fecha_c_uhfapp,
															id_a_uhfapp )	  
					SELECT	cr.id_c_uhfapp,
							cr.nombre_c_uhfapp,
							cr.mac_c_uhfapp,
							cr.n_abonado_c_uhfapp,
							cr.lat_c_uhfapp,
							cr.lng_c_uhfapp,
							cr.gps_dcml_c_uhfapp,
							cr.gps_sexa_c_uhfapp,
							cr.obs_c_uhfapp,
							cr.fecha_c_uhfapp, 
							cr.id_a_uhfapp
					FROM ccc.crudo_uhfapp cr
					JOIN ccc.crudo_uhfapp_temp crt 
					ON cr.lat_c_uhfapp = crt.lat_c_uhfapp_t 
					AND cr.lng_c_uhfapp = crt.lng_c_uhfapp_t
					GROUP BY cr.id_c_uhfapp;");

		$bd->query("DROP TABLE IF EXISTS ccc.crudo_uhfapp_temp;");
		$bd->query("COMMIT;");
		$bd->cerrar_conexion();

		echo "Indexación Existosa";
		echo "<br>";
		echo "<br>";
	}

	function cargar_datos_tablas() {
		$bd = new BaseDeDatos(DB_SERVER, DB_USER, DB_PASSUSER, DB_NAME);

		$bd->query("SET autocommit=0");
		$bd->query("START TRANSACTION;");

		/*$bd->query("SET FOREIGN_KEY_CHECKS=0;");
		$bd->query("TRUNCATE `personas`;");
		$bd->query("TRUNCATE `abonados`;");
		$bd->query("TRUNCATE `domicilios`;");
		$bd->query("TRUNCATE `coordenadas_gps`;");
		$bd->query("TRUNCATE `instalaciones_servicios`;");
		$bd->query("SET FOREIGN_KEY_CHECKS=1;");*/
	
		$abonados = $bd->fetch_all("SELECT DISTINCT n_abonado_c_uhfapp FROM ccc.crudo_uhfapp_nuevos;");

		//hay n personas
		//que son n abonados
		//y viven en n direcciones 
		for($i=0; $i<count($abonados); $i++) {
			$numero_abonado = $abonados[$i]['n_abonado_c_uhfapp'];

			$control_abonados = $bd->num_rows("	SELECT DISTINCT numero_abonado
												FROM ccc.abonados 
												WHERE numero_abonado = {$numero_abonado} LIMIT 1;");
			
			// $controla si el numero del abonado no existe, para no añadir un numero de abonado existente en la bd  
			if ($control_abonados < 1) {

				$bd->query("INSERT INTO ccc.personas (id_persona) 
							VALUES (NULL);");

				$ultimo_id_persona = $bd->last_id();

				$bd->query("INSERT INTO ccc.abonados (numero_abonado, id_persona) 
							VALUES(	{$numero_abonado}, 
									{$ultimo_id_persona});");

				$bd->query("INSERT INTO ccc.domicilios (id_domicilio, id_persona)
							VALUES	(NULL,								
									{$ultimo_id_persona});");
			}
		}


		$moviles = $bd->fetch_all("SELECT DISTINCT nombre_c_uhfapp FROM ccc.crudo_uhfapp_nuevos;");

		for($i=0; $i<count($moviles); $i++) {
			$nombre_movil = $moviles[$i]['nombre_c_uhfapp'];

			$control_moviles = $bd->num_rows("SELECT DISTINCT nombre_movil
											FROM ccc.moviles 
											WHERE nombre_movil = '{$nombre_movil}'
											LIMIT 1;");
			
			if ($control_moviles < 1) {
				$bd->query("INSERT IGNORE INTO ccc.moviles (nombre_movil)
							SELECT DISTINCT nombre_c_uhfapp FROM ccc.crudo_uhfapp_nuevos;");
			}
		}


		$nuevos = $bd->fetch_all("SELECT * FROM ccc.crudo_uhfapp_nuevos;");

		for($i=0; $i<count($nuevos); $i++) {
			$numero_abonado = $nuevos[$i]['n_abonado_c_uhfapp'];
			$lat = $nuevos[$i]['lat_c_uhfapp'];
			$lng = $nuevos[$i]['lng_c_uhfapp']; 
			$obs = $nuevos[$i]['obs_c_uhfapp']; 
			$f = new DateTime($nuevos[$i]['fecha_c_uhfapp']);	
			$nombre_movil = $nuevos[$i]['nombre_c_uhfapp'];  	

			$id_domicilio_array = $bd->fetch_array("SELECT DISTINCT d.id_domicilio
													FROM ccc.domicilios d
													INNER JOIN abonados a
													ON d.id_persona = a.id_persona
													WHERE a.numero_abonado = {$numero_abonado}");

			$id_domicilio = $id_domicilio_array['id_domicilio'];

			$bd->query("INSERT INTO ccc.coordenadas_gps (id_c_gps, id_domicilio, lat_c_gps, lng_c_gps)
						VALUES(	NULL,
								{$id_domicilio},
								{$lat},
								{$lng});");

			$ultimo_id_coord = $bd->last_id();

			$obs = $bd->escape_string($obs);
			$fecha = $f->getTimestamp();

	

			$id_moviles_array = $bd->fetch_array("SELECT DISTINCT id_movil
			FROM ccc.moviles 
			WHERE nombre_movil = '{$nombre_movil}'");

			$id_movil = $id_moviles_array['id_movil'];	

			
			$bd->query("INSERT INTO ccc.instalaciones (id_instalacion, id_domicilio, id_movil, id_c_gps, observacion_instalacion, fecha_instalacion)
						VALUES(	NULL, 
								{$id_domicilio},
								{$id_movil},
								{$ultimo_id_coord},
								'{$obs}',
								{$fecha});");
		}

		$bd->query("DROP TABLE IF EXISTS ccc.crudo_uhfapp_nuevos;");	
		$bd->query("COMMIT;");		
		$bd->cerrar_conexion();
	}

	function cargar_fotos() {
		$bd = new BaseDeDatos(DB_SERVER, DB_USER, DB_PASSUSER, DB_NAME);

		$bd->query("SET autocommit=0");
		$bd->query("START TRANSACTION;");

		/*$bd->query("SET FOREIGN_KEY_CHECKS=0;");
		$bd->query("TRUNCATE `fotos_domicilios`;");
		$bd->query("SET FOREIGN_KEY_CHECKS=1;");*/

		$numero_abonados_array = $bd->fetch_all("SELECT numero_abonado FROM ccc.abonados;");	
		
		//echo count($numero_abonados_array);
		//echo "<br>";

		for($i=0; $i<count($numero_abonados_array); $i++) {		

			$numero_abonado = $numero_abonados_array[$i]['numero_abonado'];				

			$id_archivo_array = $bd->fetch_array("SELECT DISTINCT ar.id_a_uhfapp
												FROM ccc.archivos_uhfapp  ar
												INNER JOIN abonados a
												ON CAST(TRIM(TRAILING '.jpg' FROM TRIM(LEADING 'clie_' FROM ar.nombre_a_uhfapp )) AS UNSIGNED) = {$numero_abonado}
												LEFT JOIN ccc.fotos_domicilios f 
												ON ar.id_a_uhfapp = f.id_a_uhfapp
												WHERE f.id_a_uhfapp IS NULL
												AND ar.extension_a_uhfapp = 'jpg';");

			$id_domicilio_array = $bd->fetch_array("SELECT DISTINCT d.id_domicilio
													FROM ccc.domicilios d
													INNER JOIN abonados a
													ON d.id_persona = a.id_persona
													WHERE a.numero_abonado = {$numero_abonado};");

			$id_archivo = $id_archivo_array['id_a_uhfapp'];
			$id_domicilio = $id_domicilio_array['id_domicilio'];			

			if($id_archivo != NULL) {
				if($id_domicilio != NULL) {
					$bd->query("INSERT IGNORE INTO ccc.fotos_domicilios (id_f_domicilio, id_a_uhfapp, numero_abonado, id_domicilio)
										VALUES(	NULL,
												{$id_archivo},
												{$numero_abonado},
												{$id_domicilio});");
				}
			}
		}

		$bd->query("COMMIT;");		
		$bd->cerrar_conexion();
	}

	function exportar_datos($url) {
		$bd = new BaseDeDatos(DB_SERVER, DB_USER, DB_PASSUSER, DB_NAME);
		$info_mapa = [];

		$hoy=date("Y-m-d");
		//$hoy='2018-09-12 00:00:00';
		$datos = $bd->fetch_all("SELECT n_abonado_c_uhfapp, lat_c_uhfapp, lng_c_uhfapp FROM ccc.crudo_uhfapp WHERE fecha_c_uhfapp = '$hoy'");

		//$datos = $bd->fetch_all(MYSQL_PTS_GPS_MAS_RECIENTES);
		//print_r($datos);

		if($datos == NULL ) {
			$ret = "No se encontraron datos para agregar el día de hoy.";
		}
		else {

			$i=0;
			foreach ($datos as $clave => $dato) {

				$lat = floatval($dato['lat_c_uhfapp']);
				$lng = floatval($dato['lng_c_uhfapp']);
				$numero_abonado = (int) $dato["n_abonado_c_uhfapp"];				

				/*$lat = floatval($dato['lat_c_gps']);
				$lng = floatval($dato['lng_c_gps']);
				$numero_abonado = (int) $dato["numero_abonado"];*/				


				$info_mapa[$i] =  ["CLIENTE" => $numero_abonado,"LATITUD" => $lat, "LONGITUD" => $lng];

				// $ret = file_get_contents($url.json_encode($info_mapa[1],JSON_PRETTY_PRINT)); //con este no funca
				$ret = file_get_contents($url.json_encode($info_mapa[$i]));

				//$ret = '{"PROCESADO":"UPDATE OK"}';
				if($ret != '{"PROCESADO":"UPDATE OK"}') {
		    		throw new Exception("Error al exportar datos.");
		  		} 

		  		echo $url.json_encode($info_mapa[$i]);
		  		echo "<br>";

		  		$i++;
			}

			echo "<br><br><br>";
			echo "Se agregaron {$i} entradas.";
		}

		//$ret = 0;
  		return $ret;
	}

?>