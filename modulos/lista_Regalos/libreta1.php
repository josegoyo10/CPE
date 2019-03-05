<?
$SIN_PER = 1;

include "../../includes/aplication_top.php";

// *************************************************************************************

/** ACCIONES*************/
if ($accion=='Utilizar'){
    $queryup="UPDATE list_regalos_enc SET id_Direccion = ".($id_direccion+0)." WHERE idLista= ".($idLista+0) ;      
    tep_db_query($queryup); 

    ?>
	<script language="JavaScript">
		window.opener.location.reload();
		window.close();
	</script>
	<?
   tep_exit();
}


/***********************/

$MiTemplate = new Template();
// asignamos degug maximo
$MiTemplate->debug = 0;
// root directory de los templates
$MiTemplate->set_root(DIRTEMPLATES);
// variables perdidas
$MiTemplate->set_unknowns("remove");

$MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
$MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);
$MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));
// Agregamos el main
$MiTemplate->set_file("main","lista_Regalos/libreta1.htm");
     $MiTemplate->set_var("idLista",$idLista);
     $MiTemplate->set_var("id_chek",$id_chek);
     $MiTemplate->set_var("clie_rut",$clie_rut);
     
     $MiTemplate->set_block("main", "DIRE", "BLO_dire");
     $queryDire="SELECT D.dire_defecto, D.id_direccion , D.dire_nombre,D.dire_observacion,D.dire_direccion , D.dire_telefono , NE.DESCRIPTION AS comu_nombre, if (D.id_direccion=L.id_Direccion,'checked','') checked  
				 FROM direcciones D 
				 INNER JOIN cu_neighborhood NE on (D.dire_localizacion = NE.LOCATION) 
				 INNER JOIN list_regalos_enc L on (L.clie_Rut=D.clie_rut) 
				 WHERE L.idLista = $idLista AND D.dire_activo=1 
				 ORDER BY D.id_direccion";
     
     query_to_set_var( $queryDire, $MiTemplate, 1, 'BLO_dire', 'DIRE' ); 

     
     $consulta_dire = "SELECT COUNT(D.id_direccion) AS numero
					   FROM direcciones D 
				       INNER JOIN cu_neighborhood NE ON (D.dire_localizacion = NE.LOCATION) 
				       INNER JOIN list_regalos_enc L ON (L.clie_Rut=D.clie_rut) 
				       WHERE L.idLista = $idLista AND D.dire_activo=1 
				       ORDER BY D.id_direccion";
				
	$resultado = tep_db_query($consulta_dire);
	$arreglo = tep_db_fetch_array($resultado);

	if($arreglo['numero'] > 1)
	{		
	}
	else{
		$MiTemplate->set_var("desactivado", 'disabled');
	}
	
$MiTemplate->pparse("OUT_H", array("header"), true);
$MiTemplate->parse("OUT_M", array("main","footer"), true);
$MiTemplate->p("OUT_M");

include "../../includes/application_bottom.php";
?>