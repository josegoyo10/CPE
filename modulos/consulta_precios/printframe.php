<?
$SIN_PER = 1;
include "../../includes/aplication_top.php";

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

$MiTemplate->set_var("select_local",$select_local);
$MiTemplate->set_var("select_top",$select_top);
$MiTemplate->set_var("t_top",$t_top);
$MiTemplate->set_var("select_tipo",$select_tipo);
$MiTemplate->set_var("select_subtipo",$select_subtipo);
$MiTemplate->set_var("va",$va);
$MiTemplate->set_var("texto_dato",$texto_dato);
$MiTemplate->set_var("fecha_inicio",$fecha_inicio);
$MiTemplate->set_var("fecha_termino",$fecha_termino);
$MiTemplate->set_var("accion",$accion);
// Agregamos el main
$MiTemplate->set_file("main","consulta_precios/printframe.htm");


$MiTemplate->parse("OUT_M", array("main"), true);
$MiTemplate->p("OUT_M");

include "../../includes/application_bottom.php";
?>