<?
function puede_modificar($idpl) {
	$db_1 =	tep_db_query("select estado from promociones where id_promocion = " . ($idpl+0));
	return (($res_1 = tep_db_fetch_array( $db_1 )) && $res_1['estado'] == "T")?1:0;
}

/****************************************************
MyErrorHandler : Rutina de captura de errores
*****************************************************/
function MyErrorHandler($errno, $errstr){
	echo "<BR><TABLE bgcolor='cccccc'><TR><TD><p><B>ERROR:</B>$errstr($errno)<p>Intente otra vez, o contacte al Administrador. Error en línea ".__LINE__." del archivo '".__FILE__."'";
	if ($errno == E_USER_ERROR || $errno== E_ERROR){
		echo "<p>Error grave, programa terminado.";
		echo "</TD></TR></TABLE>";
		//cierra borrando recursos como el footer, etc
		exit;
	}
	echo "</TD></TR></TABLE>";
} //funcion

/****************************************************
bbri_send : Escribe en el socket dado por la conexion
n bytes dados por el largo del buffer de envio.
*****************************************************/
function bbri_send($conex, $buffer, $largo) {
		//echo "bbri_send : comienzo	<br>\n";	
	if(!$conex) {
		echo "bbri_send ERROR : No esta conectado<br>\n";
		return 0;
	}
	if ($largo <= 0)  {		
		echo "bbri_send ERROR : EL largo del buffer no corresponde<br>\n";
		return 0;
	}

	//echo "bbri_send : envia $largo bytes	<br>\n";
	//echo "bbri_send : buffer [$buffer]<br>\n";
	$rc = fwrite ($conex, $buffer, $largo);
	//echo "bbri_send : envio return code [$rc]<br>\n";
	return $rc;

	}//bbr_send

/****************************************************
bbri_recv : Lee n bytes (largo) desde el socket dada 
la conexion un buffer de largo n.
*****************************************************/
function bbri_recv($conex, &$buffer, $largo) {
	//echo "bbri_recv : comienzo	<br>\n";
	if(!$conex) {
		echo "bbri_recv ERROR : No esta conectado<br>\n";
		return 0;
	}
	if ($largo <= 0)  {		
		echo "bbri_recv ERROR : EL largo del buffer no corresponde<br>\n";
		return 0;
	}
	//echo "bbri_recv : recibido $largo bytes	<br>\n";
	//trigger_error("No se ha podido leer los $largo bytes", E_USER_NOTICE);
	$buffer = fread ($conex, $largo);
	//echo "bbri_recv : buffer [$buffer]<br>\n";
	//echo "bbri_recv : leido [$largo]<br>\n";
	return 1;

}//bbr_recv

/****************************************************
GeneraHeaderC3
*****************************************************/
function GeneraHeaderC3($cotizacion, &$buffer, $largo_data) {
	$tipo_trx = "C3";
	$buffer = $tipo_trx . sprintf("%012d",$cotizacion) . sprintf("%04d", $largo_data);
	return;
}

?>