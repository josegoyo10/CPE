<?php
$pag_ini = '../lista_Regalos/nueva_lista_00.php';
include_once('../../includes/aplication_top.php');
include_once ('../monitor_despacho/wsgdclient.php');

$USR_LOGIN = get_login_usr( $ses_usr_id );
$idLista = $_GET['idLista'];
$zona = $_GET['zona'];
$ot_idList = $_GET['ot_idList'];


$XML = XMLdespacho($idLista,$USR_LOGIN,$zona);

$GD = envia_nueva_gd($XML,&$msgerror);
if ($GD != 0){
	dispalyLista($idLista, $GD, $ot_idList);
}

function XMLdespacho($idLista,$USR_LOGIN,$zona){
		//header
	$xml_header="<header>
		<fecha>".date("Y-m-d")."</fecha>
		<hora>".date("H:i:s")."</hora>
		<operador>".$USR_LOGIN."</operador>
		<sistema>centro proy</sistema></header>";
	//encabezado
	$xml_headermd5="<header>
		<operador>".$USR_LOGIN."</operador>
		<sistema>centro proy</sistema></header>";

		// Datos de Ubicacion del Despacho
		$QryDes = "SELECT L.idLista, L.clie_Rut, L.fec_creacion, T.cod_local, L.festejado, L.fec_Evento AS fec_entrega, V.nombre AS evento,  C.clie_tipo, CONCAT(C.clie_nombre,' ',C.clie_materno,' ',C.clie_paterno)AS clie_nombre, C.clie_rut, C.clie_telefonocasa, C.clie_telcontacto1, C.clie_telcontacto2, C.clie_barrio, D.dire_direccion, D.dire_observacion, D.dire_localizacion, L.id_Usuario
					FROM list_regalos_enc L
					LEFT JOIN clientes C ON (C.clie_rut=L.clie_Rut)
					LEFT JOIN list_eventos V ON (V.idEvento=L.id_Evento)
					LEFT JOIN direcciones D ON (D.id_direccion=L.id_Direccion)
					LEFT JOIN locales T ON (T.id_local=L.id_Local)
					WHERE idLista= $idLista";
		$res = tep_db_query($QryDes);
		$res = tep_db_fetch_array($res);

		$localizacion =getlocalizacion($res['dire_localizacion']);

		$xml_enc = "<encabezado>
				<tipodocref>6</tipodocref>
				<fechahingreso>".$res['fec_creacion']."</fechahingreso>
				<ndocref>".$res['idLista']."</ndocref>
				<lugarcompra>".$res['cod_local']."</lugarcompra>
				<fechacompra>".fecha_db2php($res['fec_creacion'])."</fechacompra>
				<origeningreso>4</origeningreso>
				<descripcion>"."Lista de Regalos N°.".$res['idLista']." - Evento:".$res['evento']." - Festejado:".$res['festejado']."</descripcion>
				<nomcliente>".$res['clie_nombre']." </nomcliente>
				<clie_rut>".$res['clie_rut']."</clie_rut>
				<telfonodes>".$res['clie_telefonocasa']." </telfonodes>
				<direcciondes>".$res['dire_direccion']."</direcciondes>
				<clie_tipo>".$res['clie_tipo']."</clie_tipo>
				<direcomuna>".$res['clie_barrio']." </direcomuna>
				<tipocarga>1</tipocarga>
				<tipodesp>1</tipodesp>
				<tipobulto>2</tipobulto>
				<jornada>1</jornada>
				<usrid>".$res['id_Usuario']."</usrid>
				<telcontacto1>".$res['clie_telcontacto1']."</telcontacto1>
				<telcontacto2>".$res['clie_telcontacto2']."</telcontacto2>
				<indicacion>".$res['dire_observacion']."</indicacion>
				<departamento>".$localizacion['departamento']."</departamento>
				<ciudad>".$localizacion['ciudad']."</ciudad>
				<localidad>".$localizacion['localidad']."</localidad>
				<location>".$res['dire_localizacion']."</location>
				<barrio>".$localizacion['barrio']."</barrio>
				<zona>".$zona."</zona>
				<fechaentrega>".fecha_db2php($res['fec_entrega'])."</fechaentrega>
			</encabezado>";
		$xml_items ="<items>";

		// MODIFICACION 18 DE JUNIO 2.008 ADICION DE PESO DE PRODUCTOS PARA ENVIO EN EL XML DE INSERCION DE OD
		// Autor : Ing. Teodosio Varela
		// Analista Funcional - Easy Colombia

		$query_detalle= "SELECT DISTINCT LO.ot_idList, LD.idLista_OS_enc, LT.idLista_det, LT.cod_Ean, LT.descripcion, SUM(LT.peso) AS peso, SUM(LD.OS_cantPick) AS cantPick
							FROM list_os_det LD
							LEFT JOIN list_regalos_det LT ON (LT.idLista_det=LD.idLista_det)
							LEFT JOIN list_os_enc LE ON LE.idLista_OS_enc = LD.idLista_OS_enc
							LEFT JOIN list_ot LO ON LO.ot_idList =LD.OS_idOT
							WHERE LE.idLista_enc=".$idLista." AND LO.ot_idEstado='PD'
							GROUP BY LT.cod_Ean";
		if ( $rq2 = tep_db_query($query_detalle) ){
			while( $res2 = tep_db_fetch_array( $rq2 ) ) {
				$pesoFinal = $res2['peso']/1000;
			//armo xml encabezado
			$xml_items .="<producto>
					<id_detalle>".$res2['idLista_det']."</id_detalle>
					<codbarra>".$res2['cod_Ean']."</codbarra>
					<tipocodigo>3</tipocodigo>
					<unimed>4</unimed>
					<cantidad>".$res2['cantPick']."</cantidad>
					<descriprod>".$res2['descripcion']."</descriprod>
					<pesoprod>".$pesoFinal."</pesoprod>
				</producto>";
			}
		$xml_items .="</items>";
		}
	$xml ="<?xml version='1.0'?><main>".$xml_header."<data>".$xml_enc.$xml_items."</data></main>";
	$xmlmd5 ="<?xml version='1.0'?><main>".$xml_headermd5."<data>".$xml_enc.$xml_items."</data></main>";
	$xmd5 = md5($xmlmd5);
	$xml_md5="<md5><ident>".$xmd5."</ident></md5>";

	//writelog('OT -> '.$ot.' MD5 '.$xmd5);
	$xml ="<?xml version='1.0'?><main>".$xml_header."<data>".$xml_enc.$xml_items.$xml_md5."</data></main>";
	return $xml;
}

function dispalyLista($idLista, $GD, $ot_idList){
	// Agrega el número de despacho a la Lista.
	$updt = "UPDATE list_regalos_enc SET GD_id ='".$GD."', id_Estado='LC', fec_cierre=now() WHERE idLista='".$idLista."'";
	tep_db_query($updt);

	$updt = "UPDATE list_ot SET ot_idEstado='LD' WHERE ot_idList in (".$ot_idList.")";
	tep_db_query($updt);

	insertahistorial_ListaReg("Se cambio el estado a Por despachar a las OT N°. ".$ot_idList.", correspondiente a la Lista de regalos N°.".$idLista.".", $USR_LOGIN, null, $idLista, null, $tipo = 'SYS');
	insertahistorial_ListaReg("Se cerro la Lista de regalos N°. ".$idLista.", generando la Guía de despacho N°.".$GD.".", $USR_LOGIN, null, $idLista, null, $tipo = 'SYS');
	header("Location: print_despachoLista.php?idLista=".$idLista."");
}
?>