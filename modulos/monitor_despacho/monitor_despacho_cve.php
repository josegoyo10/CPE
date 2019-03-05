<?
$pag_ini = '../monitor_despacho/monitor_despacho.php';
include "../../includes/aplication_top.php";

//include_idioma_mod( $ses_idioma, "monitor_despacho" );

/****************************************************************
 *
 * Despliega Listado Monitor Despacho
 *
 ****************************************************************/
function DisplayListado() {
    global $ses_usr_id;
    global $texto_osot,$radioE,$busqueda,$select_tipo,$select_tipo,$select_des,$select_estado,
	$select4,$orden,$select_fecha,$impresion,$where0,$radioE,$checkedS,$checkedT,$select_pag,$limite;

    $MiTemplate = new Template();
    // asignamos degug maximo
    $MiTemplate->debug = 0;
    // root directory de los templates
    $MiTemplate->set_root(DIRTEMPLATES);
    // variables perdidas
    $MiTemplate->set_unknowns("remove");

    $MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
    $MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);
    $MiTemplate->set_var("SUBTITULO1",TEXT_2);
    $MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));

    $MiTemplate->set_var("BOTON_VER",BOTON_VER);
    $MiTemplate->set_var("TEXT_BOTON",TEXT_BOTON);
    $MiTemplate->set_var("TEXT_SELECT",TEXT_SELECT);

    // Agregamos el header
    $MiTemplate->set_file("header","header_ident.html");

    // Agregamos el main
    $MiTemplate->set_file("main","monitor_despacho/monitor_despacho_cve.htm");
	//busqueda de os u ot, con número
	    $MiTemplate->set_var("URL_CVE",URL_CVE);
	    $MiTemplate->set_var("MO_PI_CVE",MO_PI_CVE);
	    $MiTemplate->set_var("login",get_login_usr($ses_usr_id));
	    $MiTemplate->set_var("clave",get_login_clave($ses_usr_id));
	    $MiTemplate->set_var("ses_usr_id",$ses_usr_id);

    // Agregamos el footer
	include "../../includes/footer_cproy.php";

    $MiTemplate->pparse("OUT_H", array("header"), false);
    include "../../menu/menu.php";
    $MiTemplate->parse("OUT_M", array("main","footer"), true);
    $MiTemplate->p("OUT_M");
}

/**********************************************************************************************/
switch($req){
	default:
		DisplayListado();
		break;
}

/**********************************************************************************************/

include "../../includes/application_bottom.php";

?>
