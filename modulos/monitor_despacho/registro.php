<?php
//include_once('../../includes/aplication_top.php');
$SIN_PER = 1; $longitud=0; $cadena=0;

//variables POST
	$OT=$_POST['OT'];
	sleep(0);

// Cuenta las it_od que vienen en la cadena.
	$longitud = count(split(",",$OT));
	// Obtiene las id_ot de la cadena separandolas por ,.
	$cadena = split(",",$OT);
	
	echo $longitud;
	echo $cadena;

//creamos el objeto 
//y usamos su método consultar
	/*for ($i=0; $i<$longitud; $i++){ 
		$query = "SELECT print_Pick, print_Guia FROM ot WHERE ot.ot_id =".($cadena[$i]+0);
		$rq = tep_db_query($query); 
    	$res = tep_db_fetch_array( $rq ); 
		 
		if($res['print_Pick'] != 0){
			echo ('Necesita Autorizacion');
		}
		else
			$id_OT_pick; 
			
		
		if($res['print_Guia'] != 0){
			echo ('Necesita Autorizacion');
		}
		else
			$id_OT_guia;
			
		
	}*/
?>
