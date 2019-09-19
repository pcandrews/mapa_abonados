SET FOREIGN_KEY_CHECKS=0;

TRUNCATE `personas`;
TRUNCATE `abonados`;
TRUNCATE `direcciones_domicilios`;
TRUNCATE `coordenadas_gps`;
TRUNCATE `instalaciones_servicios`;
TRUNCATE `fotos_instalaciones`;

SET FOREIGN_KEY_CHECKS=1;

# Relacion persona - empleados - instaldores
# Esto se alimenta desde otro punto. Luego hacer la iteracion.
#INSERT INTO ccc.personas (id_persona) VALUES (1);

#####

DROP PROCEDURE IF EXISTS cargar_csv;

DELIMITER $$
	CREATE PROCEDURE cargar_csv()
	BEGIN
		DECLARE control BOOLEAN DEFAULT 0;

		DECLARE var_id_c_uhfapp INT(11);
		DECLARE var_id_a_uhfapp INT(11);
		DECLARE var_nombre_c_uhfapp VARCHAR(255);
		DECLARE var_mac_c_uhfapp VARCHAR(255);
		DECLARE var_n_abonado_c_uhfapp INT(11) UNSIGNED DEFAULT 0;   
		DECLARE var_lat_c_uhfapp FLOAT(10,6);
		DECLARE var_lng_c_uhfapp FLOAT(10,6);
		DECLARE var_gps_dcml_c_uhfapp POINT;
		DECLARE var_gps_sexa_c_uhfapp VARCHAR(255);
		DECLARE var_obs_c_uhfapp TEXT;		
		DECLARE var_fecha_c_uhfapp DATETIME;

		DECLARE ultimo_id_persona INT DEFAULT 0;
		DECLARE ultimo_id_domicilio INT DEFAULT 0;
		DECLARE ultimo_id_gps INT DEFAULT 0;
		DECLARE ultimo_id_servicios INT DEFAULT 0;

		#DECLARE count INT DEFAULT 0;		
		
		DECLARE explicito CURSOR 
		FOR SELECT 	id_c_uhfapp,
					id_a_uhfapp,
					nombre_c_uhfapp,
					mac_c_uhfapp,
					n_abonado_c_uhfapp,
					lat_c_uhfapp,
					lng_c_uhfapp,
					gps_dcml_c_uhfapp,
					gps_sexa_c_uhfapp,
					obs_c_uhfapp,
					fecha_c_uhfapp
			 FROM ccc.crudo_uhfapp;
						 
		DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET control = 1;
	
	
		/**
		 * 
		**/
		/*CREATE TEMPORARY TABLE IF NOT EXISTS ccc.fotos_instalaciones_t (
			id_f_instalacion_t INT(11) UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE,
			nombre_f_instalaciones_t nombre_c_uhfapp VARCHAR(255),
			contenido_a_uhfapp MEDIUMBLOB,

		) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 DEFAULT COLLATE=utf8_spanish_ci;*/

		OPEN explicito;
			REPEAT
			#WHILE count < 50 DO
				FETCH explicito INTO 	var_id_c_uhfapp,
										var_id_a_uhfapp,
										var_nombre_c_uhfapp,
										var_mac_c_uhfapp,
										var_n_abonado_c_uhfapp,
										var_lat_c_uhfapp,
										var_lng_c_uhfapp,
										var_gps_dcml_c_uhfapp,
										var_gps_sexa_c_uhfapp,
										var_obs_c_uhfapp,
										var_fecha_c_uhfapp;
				IF NOT control THEN                
					IF ((SELECT COUNT(DISTINCT numero_abonado) FROM ccc.abonados WHERE numero_abonado = var_n_abonado_c_uhfapp LIMIT 1) < 1) THEN
						INSERT INTO ccc.personas (id_persona) 
						VALUES (NULL);
						
						SET ultimo_id_persona = LAST_INSERT_ID();						
						
						INSERT INTO ccc.abonados (numero_abonado, id_persona) 
						VALUES(	var_n_abonado_c_uhfapp, 
								ultimo_id_persona);
					END IF;
					IF((SELECT COUNT(DISTINCT lat_c_gps, lng_c_gps) FROM ccc.coordenadas_gps WHERE lat_c_gps = var_lat_c_uhfapp AND lng_c_gps = var_lng_c_uhfapp LIMIT 1) < 1) THEN
						
						INSERT INTO ccc.direcciones_domicilios (id_d_domicilio, id_persona)
						VALUES	(	NULL, 
									(	SELECT p.id_persona FROM ccc.personas p 
										JOIN ccc.abonados a on p.id_persona = a.id_persona
								 		WHERE a.numero_abonado = var_n_abonado_c_uhfapp	));

						SET ultimo_id_domicilio = LAST_INSERT_ID();

						INSERT INTO ccc.coordenadas_gps (id_c_gps, id_d_domicilio, lat_c_gps, lng_c_gps, gps_dcml_c_gps, gps_sexa_c_gps)
						VALUES(	NULL, 
								ultimo_id_domicilio,
								var_lat_c_uhfapp,
								var_lng_c_uhfapp,
								var_gps_dcml_c_uhfapp,
								var_gps_sexa_c_uhfapp);	

						SET ultimo_id_gps = LAST_INSERT_ID();

						INSERT INTO ccc.instalaciones_servicios (id_i_servicios, id_c_gps, observacion_i_servicios, fecha_y_hora_i_servicios)
						VALUES(	NULL, 
								ultimo_id_gps,
								var_obs_c_uhfapp,
								var_fecha_c_uhfapp);

						SET ultimo_id_servicios = LAST_INSERT_ID();	
									
						IF((SELECT COUNT(ar.contenido_a_uhfapp) FROM ccc.archivos_uhfapp ar 
							WHERE CONCAT("clie_", var_n_abonado_c_uhfapp, ".jpg") = ar.nombre_a_uhfapp
							AND ar.extension_a_uhfapp = 'jpg' LIMIT 1) > 0) THEN

							INSERT INTO  ccc.fotos_instalaciones (id_f_instalacion, id_d_domicilio, id_c_gps, contenido_a_uhfapp)
							VALUES(	NULL, 
									ultimo_id_domicilio,
									ultimo_id_servicios,
									(	SELECT ar.contenido_a_uhfapp FROM ccc.archivos_uhfapp ar 
										WHERE CONCAT("clie_", var_n_abonado_c_uhfapp, ".jpg") = ar.nombre_a_uhfapp
										AND ar.extension_a_uhfapp = 'jpg'));
						END IF;
					END IF;						
				END IF;

			#SET count = count + 1;
			#END WHILE;  	
			UNTIL control END REPEAT;			
		CLOSE explicito;
	END $$
DELIMITER ;

CALL cargar_csv();