<?
$pag_ini = '../lista_monitor_ot_pe/index.php';
include "../../includes/aplication_top.php";

$USR_LOGIN = get_login_usr( $ses_usr_id );

///////////////////// ACCIONES //////////////////////
if ($accion=='AgrTr'){
	insertahistorial_ListaReg($hist_descripcion,$USR_LOGIN, $id_ot,$idLista,null, $tipo = 'SYS');
	$accion ='';
	}
	
if ($accion=='recibir'){
	insertahistorial_ListaReg("Los productos de la OT N°.".$id_ot.", han sido recibidos, y pasaran a estado 'Por Despachar'",$USR_LOGIN, $id_ot, $idLista, null, $tipo = 'SYS');
	
	$QryProd = "SELECT OD.idLista_OS_det, OD.OS_idOT, OD.OS_cantidad, OD.OS_cantPick
				FROM list_os_det OD
				JOIN list_regalos_det LD ON (LD.idLista_det=OD.idLista_det)
				WHERE OD.OS_idOT='".$id_ot."'";
	$rqProd = tep_db_query($QryProd);
	while($resProd = tep_db_fetch_array( $rqProd )){
			$updtProd = "UPDATE list_os_det SET OS_cantPick=(OS_cantidad), OS_cantidad=(OS_cantidad-OS_cantidad) WHERE idLista_OS_det='".$resProd['idLista_OS_det']."'";
			tep_db_query($updtProd);
	}	
	
	$updateRE = "UPDATE list_ot SET ot_idEstado='PD' WHERE ot_idList='".$id_ot."'";
	tep_db_query($updateRE);
	
	header("Location:../cotiza_Regalos/detalleOT_Regalos.php?id_OT=".$id_ot."");
	}

////////////////////////////////////////////////////

$MiTemplate = new Template();
$MiTemplate->debug = 0;
$MiTemplate->set_root(DIRTEMPLATES);
$MiTemplate->set_unknowns("remove");

$MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO);
$MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);
$MiTemplate->set_var("SUBTITULO1",TEXT_2);
$MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));
$MiTemplate->set_var("id_ot",$id_ot);
$MiTemplate->set_var("indexPanel",($indexPanel+0));

// Agregamos el header
$MiTemplate->set_file("header","header_ident.html");

// Agregamos el main
$MiTemplate->set_file("main","lista_monitor_ot_pe/monitor_detalle_01.htm");

//################################### PESTAÑA DATOS OT ####################################
$MiTemplate->set_block("main", "datosot", "LIS_datosot");
		$query="SELECT DISTINCT LE.idLista_enc AS Lista_No, OT.ot_idList, LO.idLista_OS_enc, DATE_FORMAT(OT.ot_listFeccrea, '%Y-%m-%d') AS ot_listFeccrea , DATE_FORMAT(LR.fec_Evento, '%Y-%m-%d') AS fec_Evento, ES.esta_nombre, LR.descripcion,TD.nombre, if(OT.no_OC_SAP<>0, OT.no_OC_SAP, ' ') AS no_OC_SAP, if(OT.no_TR_SAP<>0, OT.no_TR_SAP=0, ' ') AS no_TR_SAP, L.nom_local, CONCAT(U.USR_NOMBRES, ' ',U.USR_APELLIDOS )AS asesor
			FROM list_ot OT 
			LEFT JOIN list_os_det LO ON LO.OS_idOT = OT.ot_idList
			LEFT JOIN list_os_enc LE ON LE.idLista_OS_enc = LO.idLista_OS_enc
			LEFT JOIN list_regalos_det LD ON LD.idLista_det = LO.idLista_det
			LEFT JOIN list_regalos_enc LR ON LR.idLista = LD.idLista_enc 
			LEFT JOIN direcciones DR ON DR.id_direccion = LR.id_Direccion
			LEFT JOIN tipos_despacho TD ON TD.id_tipodespacho = LD.list_idTipodespacho
			LEFT JOIN estados ES ON ES.id_estado = OT.ot_idEstado
            LEFT JOIN locales L ON L.id_local=LE.OS_local 
            LEFT JOIN usuarios U ON U.USR_ID=LE.OS_idUsuario
			WHERE OT.ot_idList=" . ($id_ot + 0) ; 
		query_to_set_var( $query, $MiTemplate, 1, 'LIS_datosot', 'datosot' );


$MiTemplate->set_block("main", "DetalleOT", "DET_datosot");
			$query="SELECT  LD.cod_ean, LD.cod_Easy, LD.descripcion, LD.list_precio, LO.OS_cantidad, if(LD.list_instalacion='false','NO','SI') AS instalacion, (list_precio * OS_cantidad)AS Total
					FROM list_ot OT 	
					LEFT JOIN list_os_det LO ON LO.OS_idOT = OT.ot_idList 
					LEFT JOIN list_regalos_det LD ON LD.idLista_det = LO.idLista_det
					WHERE OT.ot_idList= ".($id_ot + 0)." GROUP By LD.cod_Easy "; 
			query_to_set_var( $query, $MiTemplate, 1, 'DET_datosot', 'DetalleOT' );

			
//################################### PESTAÑA DATOS CLIENTE ####################################
$query="SELECT DISTINCT C.clie_rut, CONCAT(C.clie_nombre,' ', C.clie_paterno,' ',C.clie_materno)AS nom_cliente, C.clie_telefonocasa, DR.id_direccion, DR.dire_direccion, DR.dire_telefono 
			FROM list_ot OT 
			LEFT JOIN list_os_det LO ON LO.OS_idOT = OT.ot_idList
			LEFT JOIN list_os_enc LE ON LE.idLista_OS_enc = LO.idLista_OS_enc
            LEFT JOIN list_regalos_enc LR ON LR.idLista=LE.idLista_enc
            LEFT JOIN direcciones DR ON DR.id_direccion = LR.id_Direccion
            LEFT JOIN clientes C ON C.clie_rut=LE.OS_clieRut 
			WHERE OT.ot_idList=" . ($id_ot + 0) ; 
$rq = tep_db_query($query);
$res = tep_db_fetch_array( $rq );

$clie_direccion = "SELECT dire_direccion, dire_localizacion FROM direcciones WHERE clie_rut = ".($res['clie_rut']+0);
$rq1 = tep_db_query($clie_direccion);
$res1 = tep_db_fetch_array( $rq1 );
$clie_barrio = getlocalizacion( $res1['dire_localizacion'] );
$clie_barrio = $clie_barrio['barrio'];

$dire_direccion = "SELECT dire_direccion, dire_telefono, dire_localizacion FROM direcciones WHERE id_direccion = ".($res['id_direccion']+0);
$rq2 = tep_db_query($dire_direccion);
$res2 = tep_db_fetch_array( $rq2 );
$dir_barrio = getlocalizacion( $res2['dire_localizacion'] );
$dir_barrio = $dir_barrio['barrio'];

// Datos del Cliente
$MiTemplate->set_var('clie_rut', $res['clie_rut']);
$MiTemplate->set_var('clie_nombre',$res["nom_cliente"]);
$MiTemplate->set_var('clie_direccion',$res1["dire_direccion"]);
$MiTemplate->set_var('clie_barrio', $clie_barrio);
$MiTemplate->set_var('clie_telefono',$res["clie_telefonocasa"]);

//Datos del despacho
$MiTemplate->set_var('dir_direccion',$res2["dire_direccion"]);
$MiTemplate->set_var('dir_barrio', $dir_barrio);
$MiTemplate->set_var('dir_telefono',$res2["dire_telefono"]);


//################################### PESTAÑA PROVEEDOR ####################################
$qry="SELECT OT.ot_idEstado, OT.id_proveedor
		FROM proveedores PV
		LEFT JOIN proveedores_ext EX on (PV.id_proveedor=EX.id_proveedor)
		LEFT JOIN prodxprov PP on (PP.cod_prov=PV.cod_prov)
		LEFT JOIN list_regalos_det LD ON (LD.cod_Easy=PP.cod_prod1)
		LEFT JOIN list_os_det LO ON (LO.idLista_det=LD.idLista_det)
		LEFT JOIN list_ot OT ON (OT.ot_idList=LO.OS_idOT)
		WHERE OT.ot_idList =" . ($id_ot + 0) ;
	$rq = tep_db_query($qry);
	$res = tep_db_fetch_array( $rq );

	if ($res["id_proveedor"]){
		$idp=$res["id_proveedor"];
	}
	if ($idp){
		$wherep="AND PV.id_proveedor=".$idp;
		$MiTemplate->set_var('msg','');
	}else{
		$MiTemplate->set_var('msg','No se ha asignado proveedor a esta OT');
	}

if (($res["ot_idEstado"]=='EC')&&(!$idp)){
	$MiTemplate->set_var('Boton', '<input type="button" value="Realizar Compra" onclick="asignaInstalador()">');
}else {
	 //Recuperamos proveedor
		$query_PR = "SELECT PV.id_proveedor, PV.rut_prov, PV.razsoc_prov, PV.nom_prov,  PV.fonocto_prov, PV.emailcto_prov, OT.compra_obs 
						FROM proveedores PV
						LEFT JOIN proveedores_ext EX on (PV.id_proveedor=EX.id_proveedor)
						LEFT JOIN prodxprov PP on (PP.cod_prov=PV.cod_prov)
						LEFT JOIN list_regalos_det LD ON (LD.cod_Easy=PP.cod_prod1)
						LEFT JOIN list_os_det LO ON (LO.idLista_det=LD.idLista_det)
						LEFT JOIN list_ot OT ON (OT.ot_idList=LO.OS_idOT)
						WHERE OT.ot_idList=".($id_ot + 0)." $wherep" ;
		$rq = tep_db_query($query_PR);
		$res = tep_db_fetch_array( $rq );
		$MiTemplate->set_var('rut_prov',$res["rut_prov"]);
		$MiTemplate->set_var('prov_rutdv',dv($res["rut_prov"]));
		$MiTemplate->set_var('razsoc_prov',$res["razsoc_prov"]);
		$MiTemplate->set_var('nomcontacto',$res["nom_prov"]);
		$MiTemplate->set_var('fonocto_prov',$res["fonocto_prov"]);
		$MiTemplate->set_var('mailcontacto',$res["emailcto_prov"]);
		$MiTemplate->set_var('compra_obs',$res["compra_obs"]);

}
//################################### TRACKING ####################################
$MiTemplate->set_block("main", "Tracking", "BLO_hist");
    $queryHist="SELECT id_historial, DATE_FORMAT(hist_fecha, '%e/%m/%Y %H:%i:%S') hist_fecha,hist_usuario,his_tipo,hist_descripcion 
				FROM list_historial WHERE ot_idList=".($id_ot+0)." order by id_historial";
query_to_set_var( $queryHist, $MiTemplate, 1, 'BLO_hist', 'Tracking' );

//################################### ACCIONES ####################################
//Asigno el tipo de flujo
		$Qry_est = "SELECT ot_idEstado FROM list_ot WHERE ot_idList = ".($id_ot + 0)." "; 
		$rq_est = tep_db_query($Qry_est);
		$res_est = tep_db_fetch_array( $rq_est );
		
		if($res_est['ot_idEstado'] == 'ER')
			$MiTemplate->set_var('Btn_acc', '<input type="button" value="Recibir" onclick="recibirOT()">');
		if($res_est['ot_idEstado'] == 'PT')
			$MiTemplate->set_var('Btn_acc1', '<input type="button" value="Transferir" onclick="transferirOT()">');

//################################### OT's Relacionadas ####################################
$MiTemplate->set_block("main", "Otes", "BLO_ot");
    $queryHist="SELECT OT.ot_idList AS ot_id2, OT.ot_listTipo AS ot_tipo2, TD.nombre AS ot_despacho2, DATE_FORMAT(OT.ot_listFeccrea, '%e/%m/%Y %H:%i:%S')AS ot_fechacreacion2, E.esta_nombre AS esta_nombre2
				FROM list_ot OT
				LEFT JOIN list_os_det LO ON (LO.OS_idOT=OT.ot_idList) 
				LEFT JOIN list_os_enc LE ON (LE.idLista_OS_enc = LO.idLista_OS_enc)
				LEFT JOIN list_regalos_det LD ON (LD.idLista_det = LO.idLista_det)
				LEFT JOIN tipos_despacho TD ON (TD.id_tipodespacho = LD.list_idTipodespacho)
				LEFT JOIN estados E ON (E.id_estado = LE.OS_estado) 
				WHERE OT.ot_idList=".($id_ot+0)." order by 1";
$num_otes = query_to_set_var( $queryHist, $MiTemplate, 1, 'BLO_ot', 'Otes' );
$MiTemplate->set_var("num_otes",$num_otes);

//##########################################################################################

// Agregamos el footer
include "../../includes/footer_cproy.php";

$MiTemplate->pparse("OUT_H", array("header"), false);
include "../../menu/menu.php";
$MiTemplate->parse("OUT_M", array("main","footer"), true);
$MiTemplate->p("OUT_M");
include "../../includes/application_bottom.php";

?>