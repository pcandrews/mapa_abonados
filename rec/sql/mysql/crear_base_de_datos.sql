
/* Borrar base de datos */ 
DROP DATABASE IF EXISTS ccc;

/*  Base de datos   */
CREATE DATABASE IF NOT EXISTS ccc
DEFAULT CHARACTER SET utf8
DEFAULT COLLATE utf8_spanish_ci;

/*  Privilegios usuario.    */
GRANT ALL PRIVILEGES ON ccc.* TO ccc_admin@localhost; 

/*  Refrescar todos los privilegios. siempre que haya un cambio de privilegios. */
FLUSH PRIVILEGES;