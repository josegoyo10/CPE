<?php
// Datos de Conexion
$hostname = "localhost";
$database = "cpprod";
$username = "root";
$password = "3des*.";

$dias = 1;            // dias de pasada del log
$dias_limpieza = 3; // dias de limpieza de tabla teradata

$carpeta_info = "/var/www/html/centroproyectos/procesos/archivos/arch_out_temp/";  // Carpeta de almacenamiento interfase;
$log_dir   = "/var/www/html/centroproyectos/log/";                       //  Carpeta almacenamiento log;
$file_log  = "cpe_co_interfaces.log"   ;                    // archivo log

// FEchas para correr la rutina manual para correrlo con el Cron se deben
// comentar las lineas $dia_ayer  y $dia_hoy.
//$dia_ayer  = "2009-01-04";
//$dia_hoy = "2009-01-05";

?>
