<?
//$pag_ini = '../monitor_ot_pe/monitor_ot_pe_00.php';
$SIN_PER = 1;
include "../../includes/aplication_top.php";
// include "activewidgets.php";

// include_idioma_mod( $ses_idioma, "monitor_ot_pe_01" );

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
//$MiTemplate->set_var("TEXT",ver_contenido_editable( 1 ));

// Agregamos el main
$MiTemplate->set_file("main","facturacion/prthdr_pf.htm");

$MiTemplate->parse("OUT_M", array("main","footer"), true);
$MiTemplate->p("OUT_M");

include "../../includes/application_bottom.php";
?>