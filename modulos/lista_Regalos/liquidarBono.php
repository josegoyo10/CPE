<?
$pag_ini = '../lista_Regalos/nueva_lista_00.php';
include "../../includes/aplication_top.php";

include_idioma_mod( $ses_idioma, "liquidarBono" );

$local_id = get_local_usr( $ses_usr_id );
$USR_LOGIN = get_login_usr( $ses_usr_id );
$idLista = $_GET['idLista'];
$usuario = $_GET['usuario'];

function calculaBono($Total){
	$vlrBono = ( PRCTJ_LIQ_BONO * $Total );
	return $vlrBono;
}

function insertBono($vlrBono, $porcentaje, $usuario, $idLista){
	$insrtBono = "INSERT INTO `list_bono` (`fec_crea`, `valor`,`liq_porcent`,`usu_creacion`, `id_Lista`) VALUES (now(), '".$vlrBono."', '".$porcentaje."', '".$usuario."', '".$idLista."')";
	tep_db_query($insrtBono);
}
// *************************************************************************************
// *************** ACCIONES ****************** //
// Crea las cotizaciones de los invitados de la Lista de regalos

	$QryBono = "SELECT id_Bono FROM list_bono where id_Lista=".$idLista."";
	$res1 = tep_db_query($QryBono);
	$result1 = tep_db_fetch_array( $res1 );
	$bono = $result1['id_Bono'];
	
	$QryEnc ="SELECT (LT.list_Cantcomp * LT.list_precio) AS Total,  DATEDIFF( date_format(now(), '%Y-%m-%d' ), date_format(LR.fec_cierre, '%Y-%m-%d' ) ) AS valDias
			   FROM list_os_det LD
	    	   LEFT JOIN list_regalos_det LT ON (LT.idLista_det=LD.idLista_det)
	           LEFT JOIN list_regalos_enc LR ON (LR.idLista=LT.idLista_enc)
			   LEFT JOIN list_os_enc LE ON (LE.idLista_OS_enc = LD.idLista_OS_enc AND LE.OS_estado='SP')
			   LEFT JOIN list_ot LO ON (LO.ot_idList =LD.OS_idOT)
			   WHERE LE.idLista_enc=".$idLista."";
	$res = tep_db_query($QryEnc);
	while ( $result = tep_db_fetch_array( $res ) ){
		$Total = $result['Total']+$Total;
		$valDias = $result['valDias'];
	}
	
	if ($bono == ''){
		if($valDias > VAL_LIQ_BONO ){
			alert("El tiempo de vigencia para Liquidar el Bono ha vencido.");
		}
		else{
			if ( $vlrBono = calculaBono($Total) ){
				insertBono($vlrBono, PRCTJ_LIQ_BONO, $usuario, $idLista);
			}
		}
	}
	
	$QryBono = "SELECT B.*, date_format(B.fec_impresion, '%Y-%m-%d') AS fec_impresion, date_format(E.fec_creacion, '%Y-%m-%d') AS fec_creacion, L.nom_local, E.clie_Rut, CONCAT(C.clie_nombre,' ',C.clie_paterno,' ',C.clie_materno) AS nom_cliente, C.clie_telefonocasa
				FROM list_bono B
				LEFT JOIN list_regalos_enc E ON E.idLista=B.id_Lista  
				LEFT JOIN locales L ON L.id_local=E.id_Local
				LEFT JOIN clientes C ON C.clie_rut=E.clie_Rut
				WHERE E.idLista=".$idLista."";
	$res2 = tep_db_query($QryBono);
	$result2 = tep_db_fetch_array( $res2 );
			
$MiTemplate = new Template();
// asignamos degug maximo
$MiTemplate->debug = 0;
// root directory de los templates
$MiTemplate->set_root(DIRTEMPLATES);
// variables perdidas
$MiTemplate->set_unknowns("remove");

// Pasamos las variables a la Plantilla
$MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
$MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);

$MiTemplate->set_var("USR_LOCAL",get_local_usr( $ses_usr_id ));
$id_local=get_local_usr( $ses_usr_id );

$MiTemplate->set_var("idBono", $result2['id_Bono']);
$MiTemplate->set_var("idLista", $result2['id_Lista']);
$MiTemplate->set_var("fecImpre", $result2['fec_impresion']?$result2['fec_impresion']:"No se ha Impreso el Bono.");
$MiTemplate->set_var("tienda", $result2['nom_local']);
$MiTemplate->set_var("fec_crea", $result2['fec_creacion']);
$MiTemplate->set_var("clie_rut", $result2['clie_Rut']);
$MiTemplate->set_var("nom_cliente", $result2['nom_cliente']);
$MiTemplate->set_var("telefono", $result2['clie_telefonocasa']);
$MiTemplate->set_var("total", formato_precio($Total) );
$MiTemplate->set_var("vlrBono", formato_precio($result2['valor']) );
$MiTemplate->set_var("numImp", $result2['num_impre'] );

$MiTemplate->set_var("NOMBRE_PAGINA",NOMBRE_PAGINA);
$MiTemplate->set_var("TEXT_CAMPO_1",TEXT_CAMPO_1);
$MiTemplate->set_var("TEXT_CAMPO_2",TEXT_CAMPO_2);
$MiTemplate->set_var("TEXT_CAMPO_3",TEXT_CAMPO_3);
$MiTemplate->set_var("TEXT_CAMPO_4",TEXT_CAMPO_4);
$MiTemplate->set_var("TEXT_CAMPO_5",TEXT_CAMPO_5);
$MiTemplate->set_var("TEXT_CAMPO_6",TEXT_CAMPO_6);
$MiTemplate->set_var("TEXT_CAMPO_7",TEXT_CAMPO_7);
$MiTemplate->set_var("TEXT_CAMPO_8",TEXT_CAMPO_8);
$MiTemplate->set_var("TEXT_CAMPO_9",TEXT_CAMPO_9);
$MiTemplate->set_var("TEXT_CAMPO_10",TEXT_CAMPO_10);
$MiTemplate->set_var("TEXT_CAMPO_11",TEXT_CAMPO_11);
$MiTemplate->set_var("TEXT_CAMPO_12",TEXT_CAMPO_12);
$MiTemplate->set_var("TEXT_CAMPO_13",TEXT_CAMPO_13);
$MiTemplate->set_var("TEXT_CAMPO_14",TEXT_CAMPO_14);
$MiTemplate->set_var("TEXT_CAMPO_15",TEXT_CAMPO_15);
$MiTemplate->set_var("TEXT_CAMPO_16",TEXT_CAMPO_16);
$MiTemplate->set_var("TEXT_CAMPO_17",TEXT_CAMPO_17);
$MiTemplate->set_var("TEXT_CAMPO_18",TEXT_CAMPO_18);
$MiTemplate->set_var("TEXT_CAMPO_19",TEXT_CAMPO_19);
$MiTemplate->set_var("TEXT_CAMPO_20",TEXT_CAMPO_20);
$MiTemplate->set_var("TEXT_CAMPO_21",TEXT_CAMPO_21);
$MiTemplate->set_var("TEXT_CAMPO_22",TEXT_CAMPO_22);
$MiTemplate->set_var("TEXT_CAMPO_23",TEXT_CAMPO_23);
$MiTemplate->set_var("TEXT_CAMPO_24",TEXT_CAMPO_24);
$MiTemplate->set_var("TEXT_CAMPO_25",TEXT_CAMPO_25);

// Agregamos el header
$MiTemplate->set_file("header","header_ident.html");

// Agregamos el main
$MiTemplate->set_file("main","lista_Regalos/liquidarBono.htm");

insertahistorial_ListaReg("Se Liquida el Bono  N°. ".$result2['id_Bono'].", correspodiente a la Lista de Regalos N°. ".$idLista."", $USR_LOGIN, null, $idLista, null, $tipo = 'SYS');
// Agregamos el footer
include "../../includes/footer_cproy.php";

$MiTemplate->pparse("OUT_H", array("header"), true);
$MiTemplate->parse("OUT_M", array("main","footer"), true);
$MiTemplate->p("OUT_M");

include "../../includes/application_bottom.php";

?>
