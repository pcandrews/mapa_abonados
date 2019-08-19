<?php

	/*
	*/
	header('Content-Type: text/html; charset=UTF-8');
	ini_set("display_errors", "On");
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL | E_STRICT);
	header("Content-Type: text/html; charset=UTF-8");
	date_default_timezone_set('America/Argentina/Tucuman');
	setlocale(LC_ALL, 'es-AR');

    // Esta ruta tiene que ser estatica
    require_once("config.php");

	// MySQL	
	defined ("DB_SERVER") ? null : define("DB_SERVER", "localhost");
	defined ("DB_USER") ? null : define("DB_USER", "ccc_admin");
	defined ("DB_PASSUSER") ? null : define("DB_PASSUSER", "sanlorenzo");
	defined ("DB_NAME") ? null : define("DB_NAME", "ccc");	
	defined ("DB_ROOT") ? null : define("DB_ROOT", "root");
	defined ("DB_PASSROOT") ? null : define("DB_PASSROOT", "rockyrocky");
	defined ("DB_CHARSET") ? null : define("DB_CHARSET", "utf8");
    defined ("DB_COLLATION") ? null : define("DB_COLLATION", "utf8_spanish_ci");
    defined ("DB_PASS_VAL") ? null : define("DB_PASS_VAL", "SET GLOBAL  validate_password_policy = low");

	// Crea el usuario ccc_admin
    defined ("MYSQL_CREAR_USUARIO") ? null : define("MYSQL_CREAR_USUARIO", "CREATE USER IF NOT EXISTS '" . DB_USER . "'@'" . DB_SERVER . "' IDENTIFIED BY '" . DB_PASSUSER . "';");
       
    // Garantizar privilegios usuario
	defined ("MYSQL_REV_PRIV") ? null : define("MYSQL_REV_PRIV", "REVOKE ALL PRIVILEGES ON *.* FROM '" . DB_USER . "'@'" . DB_SERVER ."';");
    defined ("MYSQL_GRANT_PRIV") ? null : define("MYSQL_GRANT_PRIV", 
    "GRANT ALL PRIVILEGES ON "  . DB_NAME . ".* TO '" . DB_USER . "'@'" . DB_SERVER . "' 
    REQUIRE NONE WITH GRANT OPTION 
    MAX_QUERIES_PER_HOUR 0 
    MAX_CONNECTIONS_PER_HOUR 0 
    MAX_UPDATES_PER_HOUR 0 
    MAX_USER_CONNECTIONS 0;");

    // Crear base de datos
    defined ("MYSQL_CREAR_BD") ? null : define("MYSQL_CREAR_BD", 
    "CREATE DATABASE IF NOT EXISTS " . DB_NAME . "
        DEFAULT CHARACTER SET " . DB_CHARSET . "
        DEFAULT COLLATE " . DB_COLLATION . ";");

    // Privilegios usuario.													
    defined ("MYSQL_GRANT_PRIV_BD") ? null : define("MYSQL_GRANT_PRIV_BD", 
    "GRANT ALL PRIVILEGES ON " . DB_NAME . ".* TO ". DB_USER ."@" . DB_SERVER . ";");
    
    // Refrescar todos los privilegios. siempre que haya un cambio de privilegios.
    defined ("MYSQL_FLUSH_PRIV") ? null : define("MYSQL_FLUSH_PRIV", "FLUSH PRIVILEGES;");
    
    // Crear tablas
    defined ("MYSQL_T_ARCHIVOS_UHFAPP") ? null : define("MYSQL_T_ARCHIVOS_UHFAPP", 
    "CREATE TABLE IF NOT EXISTS ccc.archivos_uhfapp (
        id_a_uhfapp INT(11) UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE,
    
        nombre_a_uhfapp VARCHAR(255),
        ruta_a_uhfapp VARCHAR(255),
        extension_a_uhfapp VARCHAR(5),
        ultima_modificacion_a_uhfapp INT(11) UNSIGNED,
        hash_a_uhfapp CHAR(64),
        contenido_a_uhfapp MEDIUMBLOB,
    
        PRIMARY KEY(id_a_uhfapp)
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 DEFAULT COLLATE=utf8_spanish_ci;");

    defined ("MYSQL_T_CRUDO_UHFAPP") ? null : define("MYSQL_T_CRUDO_UHFAPP", 
    "CREATE TABLE IF NOT EXISTS ccc.crudo_uhfapp (
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
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 DEFAULT COLLATE=utf8_spanish_ci;");

    defined ("MYSQL_T_PERSONAS") ? null : define("MYSQL_T_PERSONAS", 
    "CREATE TABLE IF NOT EXISTS ccc.personas (
        id_persona INT(11) UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE,
        
        dni_persona INT(11) UNSIGNED NULL UNIQUE,
        cuil_persona INT(11) UNSIGNED NULL UNIQUE,
        nombres_persona VARCHAR(255),
        apellidos_persona VARCHAR(255),

        PRIMARY KEY(id_persona)
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 DEFAULT COLLATE=utf8_spanish_ci;");

    defined ("MYSQL_T_ABONADOS") ? null : define("MYSQL_T_ABONADOS", 
    "CREATE TABLE IF NOT EXISTS ccc.abonados (
        numero_abonado INT(11) UNSIGNED NOT NULL UNIQUE,
        id_persona INT(11) UNSIGNED NOT NULL,
        
        estado_abonado INT(11),
        servicio_abonado VARCHAR(255),
    
        PRIMARY KEY(numero_abonado),
        FOREIGN KEY (id_persona) REFERENCES ccc.personas (id_persona) 
            ON DELETE CASCADE 
            ON UPDATE CASCADE
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 DEFAULT COLLATE=utf8_spanish_ci;");

    defined ("MYSQL_T_DOMICILIOS") ? null : define("MYSQL_T_DOMICILIOS", 
    "CREATE TABLE IF NOT EXISTS ccc.domicilios (
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
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 DEFAULT COLLATE=utf8_spanish_ci;");

    defined ("MYSQL_T_MOVILES") ? null : define("MYSQL_T_MOVILES", 
    "CREATE TABLE IF NOT EXISTS ccc.moviles (
        id_movil INT(11) UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE,
        
        nombre_movil VARCHAR (255) UNIQUE,	
        
        PRIMARY KEY(id_movil)
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 DEFAULT COLLATE=utf8_spanish_ci;");

    defined ("MYSQL_T_COORDENADAS") ? null : define("MYSQL_T_COORDENADAS", 
    "CREATE TABLE IF NOT EXISTS ccc.coordenadas_gps (
        id_c_gps INT(11) UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE,
        id_domicilio INT(11) UNSIGNED NOT NULL,
    
        lat_c_gps FLOAT(10,6) NULL,
        lng_c_gps FLOAT(10,6) NULL,
        
        PRIMARY KEY(id_c_gps),
        FOREIGN KEY (id_domicilio) REFERENCES ccc.domicilios (id_domicilio) 
            ON DELETE CASCADE 
            ON UPDATE CASCADE
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 DEFAULT COLLATE=utf8_spanish_ci;");

    defined ("MYSQL_T_INSTALACIONES") ? null : define("MYSQL_T_INSTALACIONES", 
    "CREATE TABLE IF NOT EXISTS ccc.instalaciones (
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
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 DEFAULT COLLATE=utf8_spanish_ci;");

    defined ("MYSQL_T_FOTOS_DOMICILIOS") ? null : define("MYSQL_T_FOTOS_DOMICILIOS", 
    "CREATE TABLE IF NOT EXISTS ccc.fotos_domicilios (
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
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 DEFAULT COLLATE=utf8_spanish_ci;");

    // Querys
    defined ("MYSQL_PTS_GPS_MAS_RECIENTES") ? null : define("MYSQL_PTS_GPS_MAS_RECIENTES", 
    "SELECT a.`numero_abonado`, c.`lat_c_gps`, c.`lng_c_gps`
    FROM ccc.`coordenadas_gps` c
    INNER JOIN (
        SELECT i1.`id_c_gps`
        FROM ccc.`instalaciones` i1
        INNER JOIN (
            SELECT i2.`id_domicilio`, MAX(i2.`fecha_instalacion`) fechaMax
            FROM ccc.`instalaciones` i2
            GROUP BY i2.`id_domicilio`
        ) i3 
        ON i1.`id_domicilio` = i3.`id_domicilio`
        AND i1.`fecha_instalacion` = i3.`fechaMax`
    ) i4
    ON c.`id_c_gps` = i4.`id_c_gps`
    INNER JOIN domicilios d
    ON c.`id_domicilio` = d.`id_domicilio`
    INNER JOIN abonados a
    ON d.`id_persona` = a.`id_persona`");

?>