# Documentación General

IMPORTANTE: UNA VEZ REALIZADA LA INSTALACION, SE DEBE BORRAR LA CARPETA INSTALADOR DE SISTEMA EN SERVICIO.

## Configurar Laravel

## Editar laravel/.env

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ccc
DB_USERNAME=ccc_admin
DB_PASSWORD=sanlorenzo

## Instalar

Lo único que se tiene realmente configurar son:
MySQL:
    · nombre root.
    · contraseña root.
    · servidor mysql.

Datos:
    · path directorio de datos.

Si estos 3 datos son correctos, la instalacion y la carga de datos deberia correr sin problemas.

1. Se supone que para MySQL:
    usuario: root
    contraseña: rockyrocky

   Si los datos son diferentes, actualizar la constante DB_PASSROOT en mapa_abonados/src/cfg/config.php

2. Path datos: especificar.

3. Correr instalador/instalador.php
    * Crea usuario mysql.
    * Base de datos.
    * Tablas.
    * Llena bases de datos.
