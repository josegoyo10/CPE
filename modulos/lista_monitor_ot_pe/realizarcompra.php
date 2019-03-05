<?
$SIN_PER = 1;
include "../../includes/aplication_top.php";

$USR_LOGIN = get_login_usr( $ses_usr_id );
global $el_comentario;
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
$qry_prov="SELECT PV.id_proveedor, PV.nom_prov, PV.rut_prov
			FROM proveedores PV
			LEFT JOIN proveedores_ext EX on (PV.id_proveedor=EX.id_proveedor)
			LEFT JOIN prodxprov PP on (PP.cod_prov=PV.cod_prov)
			LEFT JOIN list_regalos_det LD ON (LD.cod_Easy=PP.cod_prod1)
			LEFT JOIN list_os_det LO ON (LO.idLista_det=LD.idLista_det)
			LEFT JOIN list_ot OT ON (OT.ot_idList=LO.OS_idOT)
			WHERE OT.ot_idList=".($id_ot+0);
	$rq = tep_db_query($qry_prov);
	$res = tep_db_fetch_array( $rq );
	if (!$res['rut_prov']){
	?>
	<script language="JavaScript">
		window.alert('Este producto no tiene proveedores');
		window.close();
	</script>
	<?
	exit();
	}else{
// Agregamos el main
$MiTemplate->set_file("main","lista_monitor_ot_pe/realizarCompra.htm");
/* recuperamos los proveedores*/
$MiTemplate->set_block("main", "proveedores", "BLO_pro");

$MiTemplate->set_var("id_ot", $id_ot);

$qry_prov="SELECT PV.id_proveedor, PV.nom_prov, PV.rut_prov, LD.idLista_enc
			FROM proveedores PV
			LEFT JOIN proveedores_ext EX on (PV.id_proveedor=EX.id_proveedor)
			LEFT JOIN prodxprov PP on (PP.cod_prov=PV.cod_prov)
			LEFT JOIN list_regalos_det LD ON (LD.cod_Easy=PP.cod_prod1)
			LEFT JOIN list_os_det LO ON (LO.idLista_det=LD.idLista_det)
			LEFT JOIN list_ot OT ON (OT.ot_idList=LO.OS_idOT)
			WHERE OT.ot_idList=".($id_ot+0);
query_to_set_var( $qry_prov, $MiTemplate, 1, 'BLO_pro', 'proveedores' );
	


if ($accion){
	$updateOC = "UPDATE list_ot SET ot_idEstado='ER', no_OC_SAP='".$noc_sap."', id_proveedor='".$select_prov."', fec_compra=now(), compra_obs='".$comentario."' WHERE ot_idList='".$id_ot."'";
	tep_db_query($updateOC);
	
	insertahistorial_ListaReg("Se realiza la compra de los Productos de la OT N°.$id_ot",$USR_LOGIN, $id_ot,$idLista,null, $tipo = 'SYS');
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
