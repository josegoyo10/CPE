<?
$pag_ini = '../adm_usr/adm_usr_01.php';
include "../../includes/aplication_top.php";

include_idioma_mod( $ses_idioma, "adm_usr" );

$nombre_pagina = NOMBRE_PAGINA;


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

    $MiTemplate->set_var("TEXT_1",TEXT_1);
    $MiTemplate->set_var("LARGO_IFRAME",LARGO_IFRAME);

    // Agregamos el header
    $MiTemplate->set_file("header","header_cc.html");

    // Agregamos el main
    $MiTemplate->set_file("main","adm_usr/adm_usr_01.html");

    // Agregamos el footer
    $MiTemplate->set_file("footer","footer.html");

    $MiTemplate->pparse("OUT_H", array("header"), false);
    include "../../menu/menu.php";
    $MiTemplate->parse("OUT_M", array("main","footer"), true);
    $MiTemplate->p("OUT_M");


include "../../includes/application_bottom.php";

?>