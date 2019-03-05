<?
$pag_ini = '../monitor_ot/index.php';
include "../../includes/aplication_top.php";

include_idioma_mod( $ses_idioma, "monitor_ot" );

/**********************************************************************************************/

/*include_idioma_mod( $ses_idioma, "start" );*/

$MiTemplate = new Template();
// asignamos degug maximo
$MiTemplate->debug = 0;
// root directory de los templates
$MiTemplate->set_root(DIRTEMPLATES);
// variables perdidas
$MiTemplate->set_unknowns("remove");

$MiTemplate->set_var("id_ot",$id_ot);

$MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
$MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);
$MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));
//$MiTemplate->set_var("TEXT",ver_contenido_editable( 1 ));


// Agregamos el header
/*$MiTemplate->set_file("header","header_ident.html");*/

// Agregamos el main
$MiTemplate->set_file("main","monitor_ot/printframe.htm");

// Agregamos el footer
/* $MiTemplate->set_file("footer","footer_ident.html");*/

$MiTemplate->parse("OUT_M", array("main"), true);
$MiTemplate->p("OUT_M");

include "../../includes/application_bottom.php";
?>