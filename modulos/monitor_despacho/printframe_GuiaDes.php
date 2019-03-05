<?
session_start();
$SIN_PER = 1;
$pag_ini = '../monitor_despacho/monitor_despacho.php';
include "../../includes/aplication_top.php";
//include "ConvertCharset.class.php";

$_SESSION['id_ot2']=$_GET['id_ot'];
/****************************************************************
 *
 * IMPRIME Listado Monitor Despacho
 *
 ****************************************************************/
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
	$MiTemplate->set_file("main","monitor_despacho/printframe_GuiaDes.htm");
	
	$MiTemplate->parse("OUT_M", array("main"), true);
	$MiTemplate->p("OUT_M");
	
	include "../../includes/application_bottom.php";
	
?>
