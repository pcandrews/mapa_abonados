/*   	
	Estilo nombres de registros: 
		registro_x_tabla
		Ej:
			Tabla: ccc.archivos_uhfapp 
			Registro: nombre_a_uhfapp 
	Timestamps:
		Las marcas de tiempo se almacenarán preferentemente en unix time. 
*/


CREATE TABLE IF NOT EXISTS ccc.archivos_uhfapp (
	id_a_uhfapp INT(11) UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE,

	nombre_a_uhfapp VARCHAR(255),
	ruta_a_uhfapp VARCHAR(255),
	extension_a_uhfapp VARCHAR(5),
	ultima_modificacion_a_uhfapp INT(11) UNSIGNED,
	hash_a_uhfapp CHAR(64),
	contenido_a_uhfapp MEDIUMBLOB,

	PRIMARY KEY(id_a_uhfapp)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 DEFAULT COLLATE=utf8_spanish_ci;


CREATE TABLE IF NOT EXISTS ccc.crudo_uhfapp (
	id_c_uhfapp INT(11) UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE,
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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 DEFAULT COLLATE=utf8_spanish_ci;


CREATE TABLE IF NOT EXISTS ccc.personas (
	id_persona INT(11) UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE,
	
	dni_persona INT(11) UNSIGNED NULL UNIQUE,
	cuil_persona INT(11) UNSIGNED NULL UNIQUE,
	nombres_persona VARCHAR(255),
	apellidos_persona VARCHAR(255),

	PRIMARY KEY(id_persona)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 DEFAULT COLLATE=utf8_spanish_ci;


CREATE TABLE IF NOT EXISTS ccc.abonados (
	numero_abonado INT(11) UNSIGNED NOT NULL UNIQUE,
	id_persona INT(11) UNSIGNED NOT NULL,
	
	estado_abonado INT(11),
	servicio_abonado VARCHAR(255),

	PRIMARY KEY(numero_abonado),
	FOREIGN KEY (id_persona) REFERENCES ccc.personas (id_persona) 
		ON DELETE CASCADE 
		ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 DEFAULT COLLATE=utf8_spanish_ci;


CREATE TABLE IF NOT EXISTS ccc.domicilios (
	id_domicilio INT(11) UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE,
	id_persona INT(11) UNSIGNED NOT NULL,

	calle_domicilio VARCHAR(255),
	numero_domicilio INT(11) UNSIGNED,
	dpto_domicilio VARCHAR(255),
	piso_domicilio INT(11) UNSIGNED,
	barrio_domicilio VARCHAR(255),
	municipio_domicilio VARCHAR(255),
	zona_domicilio VARCHAR (255),	
	observacion_domicilio TEXT, 
	
	PRIMARY KEY(id_domicilio),
	FOREIGN KEY (id_persona) REFERENCES ccc.personas (id_persona) 
		ON DELETE CASCADE 
		ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 DEFAULT COLLATE=utf8_spanish_ci;


CREATE TABLE IF NOT EXISTS ccc.moviles (
	id_movil INT(11) UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE,

	nombre_movil VARCHAR (255) UNIQUE,	

	PRIMARY KEY(id_movil)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 DEFAULT COLLATE=utf8_spanish_ci;


CREATE TABLE IF NOT EXISTS ccc.coordenadas_gps (
	id_c_gps INT(11) UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE,
	id_domicilio INT(11) UNSIGNED NOT NULL,

	lat_c_gps FLOAT(10,6) NULL,
	lng_c_gps FLOAT(10,6) NULL,
	
	PRIMARY KEY(id_c_gps),
	FOREIGN KEY (id_domicilio) REFERENCES ccc.domicilios (id_domicilio) 
		ON DELETE CASCADE 
		ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 DEFAULT COLLATE=utf8_spanish_ci;


CREATE TABLE IF NOT EXISTS ccc.instalaciones (
	id_instalacion INT(11) UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE,
	id_domicilio INT(11) UNSIGNED NOT NULL,
	id_movil INT(11) UNSIGNED NOT NULL,
	id_c_gps INT(11) UNSIGNED NOT NULL,
	
	observacion_instalacion TEXT, 
	fecha_instalacion INT(11) UNSIGNED,
	
	PRIMARY KEY(id_instalacion),
	FOREIGN KEY (id_domicilio) REFERENCES ccc.domicilios (id_domicilio) 
		ON DELETE CASCADE 
		ON UPDATE CASCADE,
	FOREIGN KEY (id_movil) REFERENCES ccc.moviles (id_movil) 
		ON DELETE CASCADE 
		ON UPDATE CASCADE,
	FOREIGN KEY (id_c_gps) REFERENCES ccc.coordenadas_gps (id_c_gps) 
		ON DELETE CASCADE 
		ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 DEFAULT COLLATE=utf8_spanish_ci;

CREATE TABLE IF NOT EXISTS ccc.fotos_domicilios (
	id_f_domicilio INT(11) UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE,
	id_a_uhfapp INT(11) UNSIGNED NOT NULL,
	numero_abonado INT(11) UNSIGNED NOT NULL,
	id_domicilio INT(11) UNSIGNED,
	
	PRIMARY KEY(id_f_domicilio), 
	FOREIGN KEY (id_a_uhfapp) REFERENCES ccc.archivos_uhfapp (id_a_uhfapp) 
		ON DELETE CASCADE 
		ON UPDATE CASCADE,
	FOREIGN KEY (numero_abonado) REFERENCES ccc.abonados (numero_abonado) 
		ON DELETE CASCADE 
		ON UPDATE CASCADE,
	FOREIGN KEY (id_domicilio) REFERENCES ccc.domicilios (id_domicilio) 
		ON DELETE CASCADE 
		ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 DEFAULT COLLATE=utf8_spanish_ci;


##########################################################################################
# Sin uso actualmente


CREATE TABLE IF NOT EXISTS ccc.empleados (
	id_emp INT(11) UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE,
	id_per INT(11) UNSIGNED NOT NULL,

	PRIMARY KEY(id_emp),
	FOREIGN KEY (id_per) REFERENCES ccc.personas (id_per) 
	ON DELETE CASCADE 
	ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 DEFAULT COLLATE=utf8_spanish_ci;


CREATE TABLE IF NOT EXISTS ccc.celulares (
	mac_cel VARCHAR(255) NOT NULL,
	nombre_cel VARCHAR(255),

	PRIMARY KEY(mac_cel, nombre_cel),
    CONSTRAINT id_cel UNIQUE (mac_cel, nombre_cel)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 DEFAULT COLLATE=utf8_spanish_ci;


CREATE TABLE IF NOT EXISTS ccc.kml (
	id_kml INT(11) UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE,
	numero_abonado_kml_inst INT(11) UNSIGNED,
	observacion_kml_inst TEXT,
	coord_gps_dcml_str_kml_inst VARCHAR(255),

	PRIMARY KEY(id_kml)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 DEFAULT COLLATE=utf8_spanish_ci;


CREATE TABLE IF NOT EXISTS ccc.instalaciones_servicios (
	id_i_servicios INT(11) UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE,
	id_domicilio INT(11) UNSIGNED NOT NULL,
	id_movil INT(11) UNSIGNED NOT NULL,
	
	observacion_i_servicios TEXT, 
	fecha_i_servicios INT(11) UNSIGNED,
	
	PRIMARY KEY(id_i_servicios),
	FOREIGN KEY (id_domicilio) REFERENCES ccc.domicilios (id_domicilio) 
		ON DELETE CASCADE 
		ON UPDATE CASCADE,
	FOREIGN KEY (id_movil) REFERENCES ccc.moviles (id_movil) 
		ON DELETE CASCADE 
		ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 DEFAULT COLLATE=utf8_spanish_ci;
