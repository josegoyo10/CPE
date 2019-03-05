<?
$pag_ini = '../adm_instalaciones/adm_instalacion.php';
include "../../includes/aplication_top.php";

/**********************************************************************************************/


/****************************************************************
 *
 * Despliega Listado Búsqueda
 *
 ****************************************************************/

function DisplayListado() {
    global $ses_usr_id;

/*acciones*/
    $qrysvcat_ir="select codigos from tipo_subtipo_adm where tipo='SV' and subtipo='IR' and tipocatprod='C'";
    $svcat_ir= tep_db_query($qrysvcat_ir);

    $qrysvsap_ir="select codigos from tipo_subtipo_adm where tipo='SV' and subtipo='IR' and tipocatprod='P'";
    $svsap_ir = tep_db_query($qrysvsap_ir);

    $qrysvcat_in="select codigos from tipo_subtipo_adm where tipo='SV' and subtipo='IN' and tipocatprod='C'";
    $svcat_in = tep_db_query($qrysvcat_in);

    $qrysvsap_in="select codigos from tipo_subtipo_adm where tipo='SV' and subtipo='IN' and tipocatprod='P'";
    $svsap_in = tep_db_query($qrysvsap_in);

    $qrysvcat_de="select codigos from tipo_subtipo_adm where tipo='SV' and subtipo='DE' and tipocatprod='C'";
    $svcat_de= tep_db_query($qrysvcat_de);

    $qrysvsap_de="select codigos from tipo_subtipo_adm where tipo='SV' and subtipo='DE' and tipocatprod='P'";
    $svsap_de = tep_db_query($qrysvsap_de);

    $qrysvcat_vi="select codigos from tipo_subtipo_adm where tipo='SV' and subtipo='VI' and tipocatprod='C'";
    $svcat_vi = tep_db_query($qrysvcat_vi);

    $qrysvsap_vi="select codigos from tipo_subtipo_adm where tipo='SV' and subtipo='VI' and tipocatprod='P'";
    $svsap_vi = tep_db_query($qrysvsap_vi);
/*para los nuevos servicios de arriendo y de seguro*/

    $qrysvcat_ar="select codigos from tipo_subtipo_adm where tipo='SV' and subtipo='AR' and tipocatprod='C'";
    $svcat_ar   = tep_db_query($qrysvcat_ar);

    $qrysvsap_ar="select codigos from tipo_subtipo_adm where tipo='SV' and subtipo='AR' and tipocatprod='P'";
    $svsap_ar   = tep_db_query($qrysvsap_ar);

    $qrysvcat_se="select codigos from tipo_subtipo_adm where tipo='SV' and subtipo='SE' and tipocatprod='C'";
    $svcat_se   = tep_db_query($qrysvcat_se);

    $qrysvsap_se="select codigos from tipo_subtipo_adm where tipo='SV' and subtipo='SE' and tipocatprod='P'";
    $svsap_se   = tep_db_query($qrysvsap_se);
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
        $MiTemplate->set_file("main","adm_instalaciones/adm_instalacion.html");

    while ($row = mysql_fetch_row($svcat_ir)) {
         $MiTemplate->set_var("ir_categoria",$row[0]);
    }

    while ($row1 = mysql_fetch_row($svsap_ir)) {
         $MiTemplate->set_var("ir_sap",$row1[0]);
    }
    while ($row2= mysql_fetch_row($svcat_in)) {
         $MiTemplate->set_var("in_categoria",$row2[0]);
    }
    while ($row3= mysql_fetch_row($svsap_in)) {
         $MiTemplate->set_var("in_sap",$row3[0]);
    }

    while ($row4 = mysql_fetch_row($svcat_de)) {
         $MiTemplate->set_var("de_categoria",$row4[0]);
    }
    while ($row5 = mysql_fetch_row($svsap_de)) {
         $MiTemplate->set_var("de_sap",$row5[0]);
    }
    while ($row6 = mysql_fetch_row($svcat_vi)) {
         $MiTemplate->set_var("vi_categoria",$row6[0]);
    }
    while ($row7 = mysql_fetch_row($svsap_vi)) {
         $MiTemplate->set_var("vi_sap",$row7[0]);
    }
    
	while ($row8 = mysql_fetch_row($svsap_ar)) {
         $MiTemplate->set_var("ar_sap",$row8[0]);
    }
    while ($row9 = mysql_fetch_row($svsap_se)) {
         $MiTemplate->set_var("se_sap",$row9[0]);
    }

	// Agregamos el footer
	include "../../includes/footer_cproy.php";

    $MiTemplate->pparse("OUT_H", array("header"), false);
    include "../../menu/menu.php";
    $MiTemplate->parse("OUT_M", array("main","footer"), true);
    $MiTemplate->p("OUT_M");
}


/****************************************************************
 *
 *inserta categorias de pedido especial CATALOGO
 *
 ****************************************************************/
function Serv_svir_cate($tipo,$subtipo,$ir_categoria,$acccion,$tipocatprod){
    $codigos=$ir_categoria;
    adm_pedesp($tipo,$subtipo,$codigos,$acccion,$tipocatprod);   
}
/****************************************************************
 *
 *inserta sap de pedido especial CATALOGO
 *
 ****************************************************************/
function Serv_svir_sap($tipo,$subtipo,$ir_sap,$acccion,$tipocatprod){
    $codigos=$ir_sap;
    adm_pedesp($tipo,$subtipo,$codigos,$acccion,$tipocatprod); 
}
/***************************************************************
 *
 *inserta categorias de pedido especial GENERICO
 *
 ****************************************************************/
function Serv_svin_cate($tipo,$subtipo,$in_categoria,$acccion,$tipocatprod){
    $codigos=$in_categoria;
    adm_pedesp($tipo,$subtipo,$codigos,$acccion,$tipocatprod); 
}
/***************************************************************
 *
 *inserta sap de pedido especial GENERICO
 *
 ****************************************************************/
function Serv_svin_sap($tipo,$subtipo,$in_sap,$acccion,$tipocatprod){
    $codigos=$in_sap;
    adm_pedesp($tipo,$subtipo,$codigos,$acccion,$tipocatprod); 
}
/***************************************************************/
function Serv_svvi_cate($tipo,$subtipo,$vi_categoria,$acccion,$tipocatprod){
    $codigos=$vi_categoria;
    adm_pedesp($tipo,$subtipo,$codigos,$acccion,$tipocatprod); 
}
/***************************************************************/
function Serv_svvi_sap($tipo,$subtipo,$vi_sap,$acccion,$tipocatprod){
    $codigos=$vi_sap;
    adm_pedesp($tipo,$subtipo,$codigos,$acccion,$tipocatprod); 
}
/***************************************************************/
function Serv_svde_cate($tipo,$subtipo,$de_categoria,$acccion,$tipocatprod){
    $codigos=$de_categoria;
    adm_pedesp($tipo,$subtipo,$codigos,$acccion,$tipocatprod); 
}
/***************************************************************/
function Serv_svde_sap($tipo,$subtipo,$de_sap,$acccion,$tipocatprod){
    $codigos=$de_sap;
    adm_pedesp($tipo,$subtipo,$codigos,$acccion,$tipocatprod); 
}


/***************************************************************/
function Serv_svar_sap($tipo,$subtipo,$ar_sap,$acccion,$tipocatprod){
    $codigos=$ar_sap;
    adm_pear($tipo,$subtipo,$codigos,$acccion,$tipocatprod); 
}
/***************************************************************/
function Serv_svse_sap($tipo,$subtipo,$se_sap,$acccion,$tipocatprod){
    $codigos=$se_sap;
    adm_pees($tipo,$subtipo,$codigos,$acccion,$tipocatprod); 
}



/**********************************************************************************************/
if(isset($ir_categoria))
        adm_pedesp('SV','IR',$ir_categoria,'C');
if(isset($ir_sap))
        adm_pedesp('SV','IR',$ir_sap,'P');
if(isset($in_categoria))
        adm_pedesp('SV','IN',$in_categoria,'C');
if(isset($in_sap))
        adm_pedesp('SV','IN',$in_sap,'P');
if(isset($vi_categoria))
        adm_pedesp('SV','VI',$vi_categoria,'C');
if(isset($vi_sap))
        adm_pedesp('SV','VI',$vi_sap,'P');
if(isset($de_categoria))
        adm_pedesp('SV','DE',$de_categoria,'C');
if(isset($de_sap))
        adm_pedesp('SV','DE',$de_sap,'P');
if(isset($ar_sap))
        adm_pedesp('SV','AR',$ar_sap,'P');
if(isset($se_sap))
        adm_pedesp('SV','SE',$se_sap,'P');

DisplayListado();
/**********************************************************************************************/

include "../../includes/application_bottom.php";

?>