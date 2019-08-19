
<?php


//$ret = file_get_contents('http://192.168.88.8/orion_ws.php?Request=geo_{"CLIENTE":123456,"LATITUD":-27.12345678,"LONGITUD":-65.12345678}');

$ret = file_get_contents('http://192.168.88.8/orion_ws.php?Request=geo_{"CLIENTE":123456,"LATITUD":-27.12345678,"LONGITUD":-65.12345678}');

echo $ret; 