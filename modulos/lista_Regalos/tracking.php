<?
$SIN_PER = 1;
$pag_ini = '../lista_Regalos/nueva_lista_00.php';
include "../../includes/aplication_top.php";

$USR_LOGIN = get_login_usr( $ses_usr_id );
/////// ACCIONES ////////
if ($accion=='Agregar'){
	insertahistorial_ListaReg($hist_descripcion, $USR_LOGIN, null, $idLista, null, 'USR');
}
////////////////////////

$MiTemplate = new Template();
$MiTemplate->debug = 0;
$MiTemplate->set_root(DIRTEMPLATES);
$MiTemplate->set_unknowns("remove");

$MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
$MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);

$MiTemplate->set_var("clie_rut",$clie_rut);
$MiTemplate->set_var("accion",$accion);
$MiTemplate->set_var("idLista",$idLista);
    
// Agregamos el header
$MiTemplate->set_file("header","header_ident.html");

// Agregamos el main
$MiTemplate->set_file("main","lista_Regalos/tracking.htm");

$queryHead="SELECT LE.idLista, CONCAT(CL.clie_nombre, ' ', CL.clie_paterno, ' ', CL.clie_materno) AS clie_nombre, if(clie_telcontacto1,clie_telcontacto1,clie_telcontacto2) AS clie_telefono , DR.dire_direccion, DR.dire_localizacion
			FROM list_regalos_enc LE
			LEFT JOIN clientes CL ON CL.clie_rut=LE.clie_Rut
			LEFT JOIN direcciones DR ON DR.id_direccion = LE.id_Direccion
			WHERE LE.idLista = ".($idLista+0)." ";
			$res1 = tep_db_query($queryHead);
			$result1 = tep_db_fetch_array( $res1 );
			
			$direccion = getlocalizacion($result1['dire_localizacion']);
		
$MiTemplate->set_var("idLista", $result1['idLista']);
$MiTemplate->set_var("clie_nombre", $result1['clie_nombre']);
$MiTemplate->set_var("clie_telefono", $result1['clie_telefono']);
$MiTemplate->set_var("dire_direccion", $result1['dire_direccion'].", ".$direccion[barrio]);


$MiTemplate->set_block("main", "Tracking", "BLO_hist");
    $queryHist="SELECT id_historial, DATE_FORMAT(hist_fecha, '%e/%m/%Y %H:%i:%S') hist_fecha, hist_usuario, his_tipo, hist_descripcion 
    FROM list_historial WHERE idLista=".($idLista+0)." order by id_historial";
query_to_set_var( $queryHist, $MiTemplate, 1, 'BLO_hist', 'Tracking' );

// Agregamos el footer
include "../../includes/footer_cproy.php";

$MiTemplate->pparse("OUT_H", array("header"), true);
include "../../menu/menu.php";
$MiTemplate->parse("OUT_M", array("main","footer"), true);
$MiTemplate->p("OUT_M");

include "../../includes/application_bottom.php";
?>