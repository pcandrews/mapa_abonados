/* 	Crear usuario con contraseña.	*/
CREATE USER IF NOT EXISTS 'ccc_admin'@'localhost' IDENTIFIED BY 's@nLorenz0';

/* 
	Garantizar privilegios usuario.
	Nota: aqui estoy dandole todos los privilegios, seria conveniente restringirlos en el futuro.
*/
REVOKE ALL PRIVILEGES ON *.* FROM 'ccc_admin'@'localhost'; 
GRANT ALL PRIVILEGES ON ccc.* TO 'ccc_admin'@'localhost' 
REQUIRE NONE WITH GRANT OPTION 
MAX_QUERIES_PER_HOUR 0 
MAX_CONNECTIONS_PER_HOUR 0 
MAX_UPDATES_PER_HOUR 0 
MAX_USER_CONNECTIONS 0;

/*  Refrescar todos los privilegios. siempre que haya un cambio de privilegios. */
FLUSH PRIVILEGES;