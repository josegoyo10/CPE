<?
$pag_ini = '../adm_pedesp/pedido_especial.php';
include "../../includes/aplication_top.php";

include_idioma_mod( $ses_idioma, "sp" );

/**********************************************************************************************/


/****************************************************************
 *
 * Despliega Listado Búsqueda
 *
 ****************************************************************/

function DisplayListado() {
    global $ses_usr_id;
    global $radiobutton1, $radiobutton2;
    global $bus_1, $bus_2;
    global $select_pag;
    global $orden, $filtro;


/*acciones*/
    $qryPECA_CAT="select codigos from tipo_subtipo_adm where tipo='PE' and subtipo='CA' and tipocatprod='C'";
    $peca_cat = tep_db_query($qryPECA_CAT);

    $qryPECA_SAP="select codigos from tipo_subtipo_adm where tipo='PE' and subtipo='CA' and tipocatprod='P'";
    $peca_sap = tep_db_query($qryPECA_SAP);

    $qryPEGE_CAT="select codigos from tipo_subtipo_adm where tipo='PE' and subtipo='GE' and tipocatprod='C'";
    $pege_cat = tep_db_query($qryPEGE_CAT);

    $qryPEGE_SAP="select codigos from tipo_subtipo_adm where tipo='PE' and subtipo='GE' and tipocatprod='P'";
    $pege_sap = tep_db_query($qryPEGE_SAP);

/*acciones*/

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
    
    
    // Agregamos el header
    $MiTemplate->set_file("header","header_ident.html");
    // Agregamos el main
        $MiTemplate->set_file("main","adm_pedesp/pedido_especial.html");

    while ($row = mysql_fetch_row($peca_cat)) {
         $MiTemplate->set_var("ca_categoria",$row[0]);
    }

    while ($row1 = mysql_fetch_row($peca_sap)) {
         $MiTemplate->set_var("ca_sap",$row1[0]);
    }
    while ($row2= mysql_fetch_row($pege_cat)) {
         $MiTemplate->set_var("ge_categoria",$row2[0]);
    }
    while ($row3= mysql_fetch_row($pege_sap)) {
         $MiTemplate->set_var("ge_sap",$row3[0]);
    }

    // Agregamos el footer
	include "../../includes/footer_cproy.php";

    $MiTemplate->pparse("OUT_H", array("header"), false);
/*    include "../../menu/menu.php";*/
    $MiTemplate->parse("OUT_M", array("main","footer"), true);
    $MiTemplate->p("OUT_M");
}


/****************************************************************
 *
 *inserta categorias de pedido especial CATALOGO
 *
 ****************************************************************/
function Ins_peca_cate($tipo,$subtipo,$ca_categoria,$acccion,$tipocatprod){
    $codigos=$ca_categoria;
    adm_pedesp($tipo,$subtipo,$codigos,$acccion,$tipocatprod);   
}
/****************************************************************
 *
 *inserta sap de pedido especial CATALOGO
 *
 ****************************************************************/
function Ins_peca_sap($tipo,$subtipo,$ca_sap,$acccion,$tipocatprod){
    $codigos=$ca_sap;
    adm_pedesp($tipo,$subtipo,$codigos,$acccion,$tipocatprod); 
}
/***************************************************************
 *
 *inserta categorias de pedido especial GENERICO
 *
 ****************************************************************/
function Ins_pege_cate($tipo,$subtipo,$ge_categoria,$acccion,$tipocatprod){
    $codigos=$ge_categoria;
    adm_pedesp($tipo,$subtipo,$codigos,$acccion,$tipocatprod); 
}
/***************************************************************
 *
 *inserta sap de pedido especial GENERICO
 *
 ****************************************************************/
function Ins_pege_sap($tipo,$subtipo,$ge_sap,$acccion,$tipocatprod){
    $codigos=$ge_sap;
    adm_pedesp($tipo,$subtipo,$codigos,$acccion,$tipocatprod); 
}
/***************************************************************/





/**********************************************************************************************/
switch($accion){
	case "PECA_CAT":
        Ins_peca_cate('PE','CA',$ca_categoria,$accion,'C');
		break;
	case "PECA_SAP":
        Ins_peca_sap('PE','CA',$ca_sap,$accion,'P');
		break;
	case "PEGE_CAT":
        Ins_pege_cate('PE','GE',$ge_categoria,$accion,'C');
		break;
	case "PEGE_SAP":
        Ins_pege_sap('PE','GE',$ge_sap,$accion,'P');
		break;
	default:
		DisplayListado();
		break;
}

/**********************************************************************************************/

include "../../includes/application_bottom.php";

?>