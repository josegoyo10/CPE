<?
//$pag_ini = '../nueva_cotizacion/nueva_cotizacion_00.php';
$SIN_PER = 1;
include "../../includes/aplication_top.php";
// include "activewidgets.php";

// include_idioma_mod( $ses_idioma, "nueva_cotizacion_01" );

// *************************************************************************************

/*include_idioma_mod( $ses_idioma, "start" );*/

$MiTemplate = new Template();
// asignamos degug maximo
$MiTemplate->debug = 0;
// root directory de los templates
$MiTemplate->set_root(DIRTEMPLATES);
// variables perdidas
$MiTemplate->set_unknowns("remove");

$MiTemplate->set_var("id_os",$id_os);
$MiTemplate->set_var("dire_des",$dire_des);

$MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
$MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);
$MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));
//$MiTemplate->set_var("TEXT",ver_contenido_editable( 1 ));


// Agregamos el header
/*$MiTemplate->set_file("header","header_ident.html");*/

// Agregamos el main
$MiTemplate->set_file("main","nueva_cotizacion/printframeb.htm");

// Agregamos el footer
/* $MiTemplate->set_file("footer","footer_ident.html");*/

$MiTemplate->parse("OUT_M", array("main"), true);
$MiTemplate->p("OUT_M");

include "../../includes/application_bottom.php";
?>