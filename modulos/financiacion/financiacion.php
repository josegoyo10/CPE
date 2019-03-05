<?
$pag_ini = '../financiacion/financiacion.php';
include "../../includes/aplication_top.php";
include "idiomas/spanish/financiacion.php";


/**********************************************************************************************/
	DisplayListado();	

/****************************************************************/

function DisplayListado() {
    global $ses_usr_id;
    $total = $_GET['total'];
    $id_os = $_GET['id_os'];

    $MiTemplate = new Template();
    // asignamos degug maximo
    $MiTemplate->debug = 0;
    // root directory de los templates
    $MiTemplate->set_root(DIRTEMPLATES);
    // variables perdidas
    $MiTemplate->set_unknowns("remove");

    //Etiquetas
    $MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);
    $MiTemplate->set_var("SUBTITULO1",TEXT_2);
    $MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));
    $MiTemplate->set_var("TEXT_CAMPO_0",TEXT_CAMPO_0);
    $MiTemplate->set_var("TEXT_CAMPO_1",TEXT_CAMPO_1);
    $MiTemplate->set_var("TEXT_CAMPO_2",TEXT_CAMPO_2);
    $MiTemplate->set_var("TEXT_CAMPO_3",TEXT_CAMPO_3);
    $MiTemplate->set_var("TEXT_CAMPO_4",TEXT_CAMPO_4);
    $MiTemplate->set_var("TEXT_CAMPO_5",TEXT_CAMPO_5);
    $MiTemplate->set_var("TEXT_CAMPO_6",TEXT_CAMPO_6);
    $MiTemplate->set_var("TEXT_CAMPO_7",TEXT_CAMPO_7);
    
    // Agregamos el main
    $MiTemplate->set_file("main","financiacion/financiacion.html");

    // Paso de variables de Configuración de Cheques
    $MiTemplate->set_var("inicialMin", CHEQ_POS_MIN_INICIAL);
    $MiTemplate->set_var("interesCheq", CHEQ_POS_INTERES);
    $MiTemplate->set_var("maxCheq", CHEQ_POS_MAX_NUM);
    
    //Si no trae OS ni Total, permite ingresar el valor.
    if( isset($_GET['total']) && isset($_GET['total'])){
    	$MiTemplate->set_var("WriteTotal", "readonly");
    }

    $MiTemplate->set_var("print", '<a href="#" onclick="printForm()"><img src="../../modulos/img/print.gif" id="imprimir" style="border-color: 000000; border-bottom-width: 1px; border-left-width: 1px; display: none;" title="Imprimir"></a>');

	// Paso de variables a la Plantilla
	$MiTemplate->set_var("TotalFormat", $total);
	$MiTemplate->set_var("Total", $total);
	$MiTemplate->set_var("id_os", $id_os);
    

    $MiTemplate->pparse("OUT_H", array("header"), false);
    include "../../menu/menu.php";
    $MiTemplate->parse("OUT_M", array("main","footer"), true);
    $MiTemplate->p("OUT_M");
}

/**********************************************************************************************/

include "../../includes/application_bottom.php";

?>