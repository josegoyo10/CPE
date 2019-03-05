<?
$SIN_PER = 1;
include_once('../../includes/aplication_top.php');

include_idioma_mod( $ses_idioma, "sin_perm" );

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
$MiTemplate->set_var("TEXT",TEXT_1);

// Agregamos el header
$MiTemplate->set_file("header","header_sc.html");

// Agregamos el main
$MiTemplate->set_file("main","start/sin_perm.html");

// Agregamos el footer
$MiTemplate->set_file("footer","footer.html");

$MiTemplate->pparse("OUT_H", array("header"), true);
$MiTemplate->parse("OUT_M", array("main","footer"), true);
$MiTemplate->p("OUT_M");

include "../../includes/application_bottom.php";

?>