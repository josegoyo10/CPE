<?
$pag_ini = '../nueva_cotizacion/nueva_cotizacion_00.php';
include "../../includes/aplication_top.php";

// include_idioma_mod( $ses_idioma, "nueva_cotizacion_0303" );
// include_idioma_mod( $ses_idioma, "monitor_cotizaciones" );

// *************************************************************************************

/** Inicio Acciones  **/
if ($accion == "editar") {
	$query =  "UPDATE os_detalle SET osde_especificacion = '". trim($osde_especificacion)."' WHERE id_os_detalle = " . ($id_os_detalle+0) ;
	tep_db_query($query);
	?>
	<SCRIPT LANGUAGE="JavaScript">
		window.returnValue = "reload";
		window.close();
	</SCRIPT>
	<?
	tep_exit();
}
/** Fin Acciones  **/

$MiTemplate = new Template();
// asignamos degug maximo
$MiTemplate->debug = 0;
// root directory de los templates
$MiTemplate->set_root(DIRTEMPLATES);
// variables perdidas
$MiTemplate->set_unknowns("remove");

// Agregamos el main
$MiTemplate->set_file("main","nueva_cotizacion/editar_especificaciones.htm");

// Esto es en caso de que no exista previamente la os.
if (!$id_os && $clie_rut) 
	$id_os = $clie_rut*-1;

$query="SELECT osd.*
		FROM os_detalle osd 
		WHERE osd.id_os = " . ($id_os+0) . "
		ORDER BY osd.id_os_detalle desc
		limit $IndexSelected, 1";
$tdq_1 = tep_db_query($query);
$res_1 = tep_db_fetch_array( $tdq_1 );

$MiTemplate->set_var("id_os_detalle",$res_1['id_os_detalle']);
$MiTemplate->set_var("osde_especificacion",$res_1['osde_especificacion']);

$MiTemplate->parse("OUT_M", array("main"), true);
$MiTemplate->p("OUT_M");
