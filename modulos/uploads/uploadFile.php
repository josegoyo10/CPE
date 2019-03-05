<?
/* Esta página sólo se ejecuta como CGI           */
/* Permite Cargar los archivos al Servidor para   */
/* Actualizar productos de Tipo Servicios         */

$SIN_PER = 1;
include "../../includes/aplication_top.php";
include "uploadXLS.php";

// Función que carga los codigos a actualizar 
function openFileCSV($nombre_archivo){

// Revisamos el archivo para incluirlo en la tabla
    	$fp = fopen(DIR_UPLOAD.$nombre_archivo,"rb") or die("Error al abrir el archivo"); 
		$line = eregi_replace("[\n|\r|\n\r]",'', fread($fp, filesize(DIR_UPLOAD.$nombre_archivo)));
    	
	    fclose($fp);
    	unlink(DIR_UPLOAD.$nombre_archivo);
    	return $line;
}


function openFileXLS($nombre_archivo){
	$line = uploadXLS($nombre_archivo);
echo $line;exit();
	fclose($fp);
    unlink(DIR_UPLOAD.$nombre_archivo);
	return $line;
}
?>