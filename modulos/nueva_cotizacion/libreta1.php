<?
$SIN_PER = 1;

//$pag_ini = '../nueva_cotizacion/nueva_cotizacion_00.php';
include "../../includes/aplication_top.php";

// include_idioma_mod( $ses_idioma, "nueva_cotizacion_0303" );
// include_idioma_mod( $ses_idioma, "monitor_cotizaciones" );

// *************************************************************************************

/** ACCIONES*************/
if ($accion=='Utilizar'){
    $queryup="UPDATE os SET id_direccion = ".($id_direccion+0)." where id_os= ".($id_os+0) ;
    tep_db_query($queryup);
$id_direccion='';
$accion='';    
    ?>
	<script language="JavaScript">
		window.returnValue = 'refresh';
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
$MiTemplate->set_file("main","nueva_cotizacion/libreta1.htm");
     $MiTemplate->set_var("id_os",$id_os);
     $MiTemplate->set_var("id_chek",$id_chek);
     $MiTemplate->set_block("main", "DIRE", "BLO_dire");


     $queryDire="select D.dire_defecto, D.id_direccion , D.dire_nombre,D.dire_observacion,D.dire_direccion , D.dire_telefono , NE.DESCRIPTION AS comu_nombre, if (D.id_direccion=OS.id_direccion,'checked','') checked  
				from direcciones D 
				inner join cu_neighborhood NE on (D.dire_localizacion = NE.LOCATION) 
				inner join os OS on (OS.clie_rut=D.clie_rut) 
				where OS.id_os = $id_os and D.dire_activo=1 
				order by D.id_direccion";
     
     query_to_set_var( $queryDire, $MiTemplate, 1, 'BLO_dire', 'DIRE' ); 

     
     $consulta_dire = "select COUNT(D.id_direccion) AS numero
				from direcciones D 
				inner join cu_neighborhood NE on (D.dire_localizacion = NE.LOCATION) 
				inner join os OS on (OS.clie_rut=D.clie_rut) 
				where OS.id_os = $id_os and D.dire_activo=1 
				order by D.id_direccion";
				
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