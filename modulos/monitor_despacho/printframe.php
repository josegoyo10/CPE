<?
session_start();
$SIN_PER = 1;
include "../../includes/aplication_top.php";

$_SESSION['id_ot2'] = $_GET['id_ot'];

// *************************************************************************************

/*include_idioma_mod( $ses_idioma, "start" );*/

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

$MiTemplate->set_var("NUM",$NUM);
$MiTemplate->set_var("RAD_IO",$RAD_IO);
$MiTemplate->set_var("ORDEN",$ORDEN);
$MiTemplate->set_var("TO",$TO);
$MiTemplate->set_var("TD",$TD);
$MiTemplate->set_var("OTF",$OTF);
$MiTemplate->set_var("EST",$EST);

// Agregamos el main
$MiTemplate->set_file("main","monitor_despacho/printframe.htm");


$MiTemplate->parse("OUT_M", array("main"), true);
$MiTemplate->p("OUT_M");

include "../../includes/application_bottom.php";
?>