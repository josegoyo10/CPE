<?
function cambia_estado($id_ot,$destino,$desctransaccion,$flujo,$accion,$id_estado) {
	$query_ES=" UPDATE ot SET id_estado ='".($destino)."'  WHERE ot_id =".$id_ot;
	tep_db_query($query_ES);

	/*nombre del estado*/
	$qn="SELECT id_estado,esta_nombre,estadoterminal FROM estados where id_estado='".($destino)."'";
	$rq = tep_db_query($qn);
	$res = tep_db_fetch_array($rq);
	$nombre=$res['esta_nombre'];
	$final =$res['estadoterminal'];
	insertahistorial("OT $id_ot ha cambiado a estado $nombre");

	//ìnserta fecha termino si el estado es terminal//
	if ($final){
			$query_ES=" UPDATE ot SET ot_ftermino =now()  WHERE ot_id =".$id_ot;
			tep_db_query($query_ES);
	}
}

/* botones de accion para las paginas*/
function Botones($id_estado,$esta_tipo,$tipoDes,$ot_tipo){
	$otipo=$ot_tipo;
	$origen=$id_estado;
	$tDes=$tipoDes;
	$ie="'".$id_estado."'";
	$et="'".$esta_tipo."'";

	$fl=Flujo($otipo,$tDes,$esta_tipo,$origen);
	$query_B="select desc_transicion,orden, flujo,id_estado_destino as destino from cambiosestado
	where esta_tipo=$et and id_estado_origen=$ie and flujo in (0,$fl) order by orden";
	return $query_B;
}

function Flujo($otipo,$tDes,$esta_tipo,$origen){
	if ($esta_tipo=='TP'){
		if (($origen=='PC')||($origen=='PG')||($origen=='PD')||($origen=='PE')){
			if (($otipo=='PS')&&($tDes==3)){
				$f=1;
			}elseif (($otipo=='PS')&&( ($tDes==2)||($tDes==1) )){
				$f=2;
			}
		}
	}

	if ($esta_tipo=='TE'){
		if (($origen=='ER')||($origen=='ED')||($origen=='EE')||($origen=='EG')){
			if (($otipo=='PE')&&($tDes==3)){
				$f=1;
			}elseif (($otipo=='PE')&&( ($tDes==2)||($tDes==1) )){
				$f=2;
			}
		}
		if (($origen=='ET')||($origen=='EP')||($origen=='EC')){
			$f=3;
		}
	}
return $f;
}
function Datos_ws($ot){
	global $ses_usr_id;
	//header
	$xml_header="<header>
		<fecha>".date("Y-m-d")."</fecha>
		<hora>".date("H:i:s")."</hora>
		<operador>".get_login_usr( $ses_usr_id )."</operador>
		<sistema>centro proy</sistema></header>";
	//encabezado
	$xml_headermd5="<header>
		<operador>".get_login_usr( $ses_usr_id )."</operador>
		<sistema>centro proy</sistema></header>";

	$id_ot=$ot;
		$query="SELECT ot.ot_id, ot.ot_tipo,ot.id_tipodespacho,os.os_fechaboleta,os.os_comentarios,os.os_descripcion, os.id_os, D.id_direccion,
		l.cod_local,D.id_comuna,D.dire_direccion,D.dire_telefono,concat(C.clie_nombre ,' ', C.clie_paterno,' ', C.clie_materno) Cnombre,
		C.clie_tipo ,C.clie_rut,C.clie_telcontacto1, C.clie_telcontacto2, D.dire_observacion, D.dire_localizacion, os.zona, os_detalle.osde_fecha_entrega
			FROM ot 
			left join os on os.id_os = ot.id_os
			left join os_detalle on os_detalle.id_os = os.id_os
			left join locales l on l.id_local = os.id_local
			left join direcciones D on (os.id_direccion=D.id_direccion)
			left join clientes C on (D.clie_rut=C.clie_rut)
		WHERE ot.ot_id = " . ($id_ot + 0) ." and os_detalle.osde_tipoprod <>'SV' GROUP by ot.ot_id, os_detalle.id_tipodespacho"; 
		//os_detalle.osde_fecha_entrega

		$rq = tep_db_query($query);
		$res = tep_db_fetch_array( $rq );

	//echo $query;
	//exit();
	writelog("SENTENCIA OT: ".$query);
	
	$dirServ = consulta_localizacion($res['id_direccion'],2);
	$dirServicio = getlocalizacion($dirServ);
	
	writelog("FECHA ENTREGA : ".$res["osde_fecha_entrega"]);
	
	$tipodespacho	=	$res["id_tipodespacho"];
	$ot_tipo		=	$res["ot_tipo"];
	$tel_contacto1  =	$res["clie_telcontacto1"];
	$tel_contacto2  =	$res["clie_telcontacto2"];
	$indicacion	  	=	$res["dire_observacion"];
	$nomciudad		=	$dirServicio["ciudad"];
	$nomdepto		=	$dirServicio["departamento"];
	$nomlocalidad	=	$dirServicio["localidad"];
	$barrio			=	$dirServicio["barrio"];
	$location		=	$res["dire_localizacion"];
	$zona			=	$res["zona"];
	
	/* sacar de la tabla de configuracion */

	$querycfg="select variable, valor from cfg_cp_dd";
		if ( $rq1 = tep_db_query($querycfg) ){
			while( $res1 = tep_db_fetch_array( $rq1 ) ) {
				if ($res1["variable"]=='tipo_doc_ref')
					$tipo_doc_ref	 =	$res1["valor"];			
				if ($res1["variable"]=='origen_ingreso')
					$origen_ingreso	 =	$res1["valor"];
				if ($res1["variable"]=='desp_'.$tipodespacho)
					$tipodespachof	 =	$res1["valor"];
				if ($res1["variable"]=='tipocarga')
					$tipocarga		 =	$res1["valor"];
				if ($res1["variable"]=='usr_id')
					$usr_id			=	$res1["valor"];
				if ($res1["variable"]=='tipobulto_'.$ot_tipo)
					$tipobulto			=	$res1["valor"];
				if ($res1["variable"]=='htopeentrega')
					$htopeentrega		=	$res1["valor"];
				if ($res1["variable"]=='jornada_'.$tipodespachof)
					$jornada		=	$res1["valor"];
			   }	
		}

		$tipo_doc_ref	=	$tipo_doc_ref;
		$origen_ingreso	 =	$origen_ingreso;
		$descingreso	=	$res["os_descripcion"]." ".$res["os_comentarios"];
		//$tipodespacho	=	$tipodespachof;
		$tipocarga		=	$tipocarga;
		$tipobulto		=	$tipobulto;
		$jornada		=   $jornada;
		$ot				=	$res["ot_id"];

		$xml_enc = "<encabezado>
				<tipodocref>".$tipo_doc_ref."</tipodocref>
				<fechahingreso>".$tipo_doc_ref."</fechahingreso>
				<ndocref>".$res["ot_id"]."</ndocref>
				<lugarcompra>".$res["cod_local"]."</lugarcompra>
				<fechacompra>".$res["os_fechaboleta"]."</fechacompra>
				<origeningreso>".$origen_ingreso."</origeningreso>
				<descripcion>".$res["id_os"].' - '.$descingreso."</descripcion>
				<nomcliente>".$res["Cnombre"]." </nomcliente>
				<clie_rut>".$res["clie_rut"]."</clie_rut>
				<telfonodes>".$res["dire_telefono"]." </telfonodes>
				<direcciondes>".$res["dire_direccion"]."</direcciondes>
				<clie_tipo>".$res["clie_tipo"]."</clie_tipo>
				<direcomuna>".$res["id_comuna"]." </direcomuna>
				<tipocarga>".$tipocarga."</tipocarga>
				<tipodesp>".$tipodespacho."</tipodesp>
				<tipobulto>".$tipobulto."</tipobulto>
				<jornada>".$jornada."</jornada>
				<usrid>".$usr_id."</usrid>
				<telcontacto1>".$tel_contacto1."</telcontacto1>
				<telcontacto2>".$tel_contacto2."</telcontacto2>
				<indicacion>".$indicacion."</indicacion>
				<departamento>".$nomdepto."</departamento>
				<ciudad>".$nomciudad."</ciudad>
				<localidad>".$nomlocalidad."</localidad>
				<location>".$location."</location>
				<barrio>".$barrio."</barrio>
				<zona>".$zona."</zona>
				<fechaentrega>".$res["osde_fecha_entrega"]."</fechaentrega>
			</encabezado>";
		$xml_items ="<items>";
		writelog("Encabezado de Despacho: ".$xml_enc);
		// MODIFICACION 18 DE JUNIO 2.008 ADICION DE PESO DE PRODUCTOS PARA ENVIO EN EL XML DE INSERCION DE OD
		// Autor : Ing. Teodosio Varela
		// Analista Funcional - Easy Colombia

		//$query_detalle="SELECT id_os_detalle,cod_barra,1,4, osde_cantidad, osde_descripcion 
		//FROM os_detalle osd	 WHERE ot_id = " . ($ot + 0) ;
		
		$query_detalle= "SELECT os_detalle.id_os_detalle,os_detalle.cod_barra,1,4, os_detalle.osde_cantidad, os_detalle.osde_descripcion, prod.peso
		FROM os_detalle join productos prod on prod.id_producto = os_detalle.id_producto WHERE ot_id= " . ($ot + 0) ;
		
		if ( $rq2 = tep_db_query($query_detalle) ){
			while( $res2 = tep_db_fetch_array( $rq2 ) ) {
				$pesoFinal = $res2['peso']/1000;
			//armo xml encabezado
			$xml_items .="<producto>
					<id_detalle>".$res2['id_os_detalle']."</id_detalle>
					<codbarra>".$res2['cod_barra']."</codbarra>
					<tipocodigo>1</tipocodigo>
					<unimed>4</unimed>
					<cantidad>".$res2['osde_cantidad']."</cantidad>
					<descriprod>".$res2['osde_descripcion']."</descriprod>
					<pesoprod>".$pesoFinal."</pesoprod>
				</producto>";
			}
		$xml_items .="</items>";
		}
//	echo "Items [".$xml_items."]\n<BR>";
	$xml ="<?xml version='1.0'?><main>".$xml_header."<data>".$xml_enc.$xml_items."</data></main>";
	$xmlmd5 ="<?xml version='1.0'?><main>".$xml_headermd5."<data>".$xml_enc.$xml_items."</data></main>";
	$xmd5 = md5($xmlmd5);
	$xml_md5="<md5><ident>".$xmd5."</ident></md5>";

	//writelog('OT -> '.$ot.' MD5 '.$xmd5);
	$xml ="<?xml version='1.0'?><main>".$xml_header."<data>".$xml_enc.$xml_items.$xml_md5."</data></main>";
	return $xml;
}//function datos

?>