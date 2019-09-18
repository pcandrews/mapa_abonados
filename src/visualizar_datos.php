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
   require_once("cfg/config.php"); // Esta tiene que ir completa

   $bd = new BaseDeDatos(DB_SERVER, DB_USER, DB_PASSUSER, DB_NAME);
   

?>
<!DOCTYPE html>
<html>
   <head>
      <title>Marker Custom Icons Example</title>
      <link rel = "stylesheet" href = "http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.css"/>
      <script src = "http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.js"></script>
   </head>
   
   <body>
      <div id = "map" style = "width:100vw; height:100vh"></div>
      <script>
         // Creating map options
         var mapOptions = {
            center: [-26.8281,-65.2154],
            zoom: 12
         }
         // Creating a map object
         var map = new L.map('map', mapOptions);
         
         // Creating a Layer object
         var layer = new L.TileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png');

         // Adding layer to the map
         map.addLayer(layer);
         
         // Icon options
         var iconOptions = {
            iconUrl: 'map-pin3.png',
            iconSize: [30, 30]
         }
         // Creating a custom icon
         var customIcon = L.icon(iconOptions);
         
         // Creating Marker Options
         var markerOptions = {
            title: "MyLocation",
            clickable: true,
            draggable: true,
            icon: customIcon
         }
         // Creating a Marker
         var marker = L.marker([-26.8281,-65.2154], markerOptions);
         
         // Adding popup to the marker
         marker.bindPopup('Hi welcome to Tutorialspoint').openPopup();
         
         // Adding marker to the map
         marker.addTo(map);
      </script>
   </body>
   
</html>