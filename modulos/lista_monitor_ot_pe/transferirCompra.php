<?
$SIN_PER = 1;
include "../../includes/aplication_top.php";

$USR_LOGIN = get_login_usr( $ses_usr_id );
global $el_comentario, $OTtipo;
// *************************************************************************************

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

//preguntamos si existe proveedor //
$qry_prov="SELECT DISTINCT LE.idLista_OS_enc, OT.ot_idEstado, RD.idLista_enc, OT.ot_listTiendaPago AS tiendaPago, OT.no_TR_SAP, RE.id_Local AS tiendaOrigen
			FROM list_ot OT
			LEFT JOIN list_os_det LD ON (LD.OS_idOT=OT.ot_idList)
            LEFT JOIN list_os_enc LE ON (LE.idLista_OS_enc=LD.idLista_OS_enc)
			LEFT JOIN list_regalos_det RD ON (RD.idLista_det=LD.idLista_det)
			LEFT JOIN list_regalos_enc RE ON (RE.idLista=RD.idLista_enc)
			WHERE ot_idList=".($id_ot+0);
	$rq = tep_db_query($qry_prov);
	$res = tep_db_fetch_array( $rq );
	$idLista_enc = $res['idLista_enc'];
	$idLista_OS_enc = $res['idLista_OS_enc'];
	if ($res['no_TR_SAP']){
	?>
	<script language="JavaScript">
		window.alert("Esta OT ya tiene asignado un número de Transferencia");
		window.close();
	</script>
	<?
	exit();
	}else{
// Agregamos el main
$MiTemplate->set_file("main","lista_monitor_ot_pe/transferirCompra.htm");
/* recuperamos los proveedores*/
$MiTemplate->set_block("main", "proveedores", "BLO_pro");

$MiTemplate->set_var("id_ot", $id_ot);
$MiTemplate->set_var("idLista_enc", $idLista_enc);

$qry_tndorg="SELECT DISTINCT RE.id_Local AS tiendaOrigen, L.nom_local AS nomLocal_Origen
             FROM list_ot OT
			 LEFT JOIN list_os_det LD ON (LD.OS_idOT=OT.ot_idList)
			 LEFT JOIN list_regalos_det RD ON (RD.idLista_det=LD.idLista_det)
			 LEFT JOIN list_regalos_enc RE ON (RE.idLista=RD.idLista_enc)
             LEFT JOIN locales L ON (L.id_local=RE.id_Local) 
			 WHERE OT.ot_idList=".($id_ot+0)." ";
query_to_set_var( $qry_tndorg, $MiTemplate, 1, 'BLO_tienda_org', 'tienda_org' );

$qry_tndpag="SELECT DISTINCT OT.ot_listTipopago AS tiendaPago, L.nom_local AS nomLocal_Pago
			 FROM list_ot OT
			 LEFT JOIN locales L ON (L.id_local=OT.ot_listTipopago) 
			 WHERE OT.ot_idList=".($id_ot+0)." ";
query_to_set_var( $qry_tndpag, $MiTemplate, 1, 'BLO_tienda_pag', 'tienda_pag' );


if ($accion){
	if($select_origen != $select_destino){
		?>
		<script language="JavaScript">
			alert("¡¡¡ La Tienda a transferir debe corresponder a la Tienda origen de la Lista de Regalos !!!");
			window.close();
		</script>
		<?
		exit();
	}
	
	if($OTtipo == 'PS'){
		$updateTR = "UPDATE list_ot SET ot_idEstado='PC', no_TR_SAP='".$noc_sap."', fec_compra=now(), compra_obs='".$comentario."' WHERE ot_idList='".$id_ot."'";
		$updateTN = "UPDATE list_os_enc SET OS_local='".$select_destino."' WHERE idLista_OS_enc='".$idLista_OS_enc."'";
		tep_db_query($updateTN);
		tep_db_query($updateTR);
	
		insertahistorial_ListaReg("Se realiza la transferencia de la OT de Productos en Stock N°".$id_ot." de la Tienda de Pago ".$Local_Pago.",al origen de la Lista de Regalos ".$Local_Origen." ",$USR_LOGIN, $id_ot,$idLista,null, $tipo = 'SYS');
	}
	else{
		$updateTR = "UPDATE list_ot SET ot_idEstado='EC', no_TR_SAP='".$noc_sap."', fec_compra=now(), compra_obs='".$comentario."' WHERE ot_idList='".$id_ot."'";
		$updateTN = "UPDATE list_os_enc SET OS_local='".$select_destino."' WHERE idLista_OS_enc='".$idLista_OS_enc."'";
		tep_db_query($updateTN);
		tep_db_query($updateTR);
	
		insertahistorial_ListaReg("Se realiza la transferencia de la OT de Pedidos Especiales N°".$id_ot." de la tienda de pago ".$Local_Pago.",al origen de la Lista de Regalos ".$Local_Origen." ",$USR_LOGIN, $id_ot,$idLista,null, $tipo = 'SYS');
	}

	?>
	
	<script language="JavaScript">
		window.close();
	</script>
	<?
}

$MiTemplate->set_var("ot_comentario",$el_comentario);
$MiTemplate->pparse("OUT_H", array("header"), false);
$MiTemplate->parse("OUT_M", array("main"), true);
$MiTemplate->p("OUT_M");
include "../../includes/application_bottom.php";
	}
?>
