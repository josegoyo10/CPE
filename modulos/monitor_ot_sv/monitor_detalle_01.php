<?
$pag_ini = '../monitor_ot_pe/index.php';
include "../../includes/aplication_top.php";
include "includes/funciones_sv.php";

///////////////////// ACCIONES //////////////////////
if ($accion=='AgrTr'){
	insertahistorial($hist_descripcion, null, null, 'USR');
}
if ($accion=='CambEst'){
	cambia_estado($id_ot,$destino,$desctransaccion,1,$accion,$id_estado);
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
$MiTemplate->set_file("main","monitor_ot_sv/monitor_detalle_01.htm");

//################################### DATOS OT ####################################
$MiTemplate->set_block("main", "datosot", "LIS_datosot");
$query="SELECT	ot.ot_id, 
				esta_nombre, 
				t.nombre tipo_despacho, 
				date_format(ot_freactivacion, '%d/%m/%Y') as fechareac,
				case when ot_freactivacion > now() or estadoterminal = 1 then 'INACTIVA'
					 when ot_freactivacion < adddate( now(), -1) and estadoterminal = 0 then 'ALERTA'
					 else 'ACTIVA'
				end estadoactiva,
				case when estadoterminal = 1 then 'green'
					 when ot_freactivacion < adddate( now(), -1) then 'red'
					 else 'black'
				end color,
				ot_comentario,
				date_format(ot_fechacreacion, '%d/%m/%Y  %H:%m') ot_fechacreacion,
				os.id_os,
				l.nom_local,
				concat(u.USR_NOMBRES, ' ', u.USR_APELLIDOS) atendidopor,
				os.os_comentarios,
				estadoterminal
		FROM ot join estados e on e.id_estado = ot.id_estado 
				join os on os.id_os = ot.id_os 
				join tipos_despacho t on t.id_tipodespacho = ot.id_tipodespacho 
				join locales l on l.id_local = os.id_local
				join usuarios u on u.USR_ID = os.USR_ID 
		WHERE ot_id = " . ($id_ot + 0) ; 
query_to_set_var( $query, $MiTemplate, 1, 'LIS_datosot', 'datosot' );

$MiTemplate->set_block("main", "DetalleOT", "DET_datosot");
$query="SELECT	cod_sap, 
				cod_barra, 
				osde_cantidad, 
				osde_descripcion, 
				if (osde_especificacion, concat('<u>Especificaciones</u>: ', osde_especificacion), '') especificacion,
				if (osde_instalacion = 1, 'SI', 'NO') instalacion,
				osde_preciocosto,
				osde_precio,
				if (osde_descuento<>0,ROUND((osde_precio-osde_descuento)*osde_cantidad),ROUND(osde_precio*osde_cantidad)) 'total',osde_tipoprod,osde_subtipoprod
		FROM os_detalle osd			
		WHERE ot_id = " . ($id_ot + 0) ; 
query_to_set_var( $query, $MiTemplate, 1, 'DET_datosot', 'DetalleOT' );

$rq = tep_db_query($query);
$res = tep_db_fetch_array( $rq );
$tipoprod    = $res["osde_tipoprod"];
$subtipoprod = $res["osde_subtipoprod"];

//#### para controlar que no improma la ot si no es instalación ######//
if(($tipoprod!='SV') && ($subtipoprod!='AR')){
	$impreotinst="javascript:popUpWindowModal('printframe_oc.php?id_ot=$id_ot', 100, 100, 710, 500);";
	$MiTemplate->set_var('impreotinst',$impreotinst);
}else{
	$MiTemplate->set_var('impreotinst','#');
}
//################################### DATOS CLIENTE ####################################
$query="SELECT	os.clie_rut, 
				if (clie_tipo = 'P', concat(clie_nombre, ' ', clie_paterno, ' ', clie_materno), clie_razonsocial) nombre,
				D.dire_direccion,
				NE.DESCRIPTION AS comu_nombre,
				dire_telefono,
				ot.id_os
		FROM ot join os on os.id_os = ot.id_os 
				join clientes c on c.clie_rut = os.clie_rut 
				join direcciones D on c.clie_rut =  D.clie_rut and dire_defecto = 'P'
				LEFT JOIN cu_neighborhood NE ON (D.dire_localizacion = NE.LOCATION) 
		WHERE ot.ot_id = " . ($id_ot + 0) ; 
$rq = tep_db_query($query);
$res = tep_db_fetch_array( $rq );

$id_os2=$res["id_os"];
$MiTemplate->set_var('clie_rut',$res["clie_rut"]);
$MiTemplate->set_var('clie_rutdv',dv($res["clie_rut"]));
$MiTemplate->set_var('nombre',$res["nombre"]);
$MiTemplate->set_var('dire_direccion',$res["dire_direccion"]);
$MiTemplate->set_var('comu_nombre',$res["comu_nombre"]);
$MiTemplate->set_var('dire_telefono',$res["dire_telefono"]);

$MiTemplate->set_block("main", "DireServicio", "DET_direservicio");
$query="SELECT	D.dire_direccion dire_direccion_2,
				NE.DESCRIPTION AS comu_nombre_2,
				dire_telefono dire_telefono_2
		FROM ot join os on os.id_os = ot.id_os 
				join direcciones D on os.id_direccion =  D.id_direccion 				
				LEFT JOIN cu_neighborhood NE ON (D.dire_localizacion = NE.LOCATION) 
		WHERE ot.ot_id = " . ($id_ot + 0) ; 
query_to_set_var( $query, $MiTemplate, 1, 'DET_direservicio', 'DireServicio' );
//################################### Instalador ####################################

$qry=" select id_estado,id_instalador from ot  where ot_id=" . ($id_ot + 0) ;
$rq = tep_db_query($qry);
$res = tep_db_fetch_array( $rq );
$idp=$res["id_instalador"];
$iestado=$res["id_estado"];
$MiTemplate->set_var('msg','');
if ($idp){
	$wherep=" and i.id_instalador=".$idp;
	$tieneins="true";
}else{
	$tieneins="false";
}
if (($iestado=='VO')||($iestado=='VR')||($iestado=='VZ')){
	$reac=true;
}

$beginC="";
$endC="";

if ($iestado=='VC'){
//	$MiTemplate->set_var('BOTON',$res["id_estado"]);
	$MiTemplate->set_var('msg','No se ha asignado Instalador a esta OT');
	$beginC="<!---";
	$endC="-->";
	$MiTemplate->set_var('begincomment',$beginC);
	$MiTemplate->set_var('endcomment',$endC);
}else {
	$MiTemplate->set_var('begincomment',$beginC);
	$MiTemplate->set_var('endcomment',$endC);
 //Recuperamos Instalador
	$query_INS = "select inst_rut,inst_nombre,inst_paterno,inst_materno,inst_telefono,direccion,email 
	from instaladores i
	join ot o on (o.id_instalador=i.id_instalador)
	where ot_id= " . ($id_ot + 0) ." $wherep" ;
	$rq = tep_db_query($query_INS);
	$res = tep_db_fetch_array( $rq );
	
	$MiTemplate->set_var('inst_rut',$res["inst_rut"]);
	if(dv($res["inst_rut"])!=0)
		$MiTemplate->set_var('instdv','-'.dv($res["inst_rut"]));
	$MiTemplate->set_var('inst_nombre',$res["inst_nombre"]);
	$MiTemplate->set_var('inst_paterno',$res["inst_paterno"]);
	$MiTemplate->set_var('inst_materno',$res["inst_materno"]);
	$MiTemplate->set_var('inst_telefono',$res["inst_telefono"]);
	$MiTemplate->set_var('direccion',$res["direccion"]);
	$MiTemplate->set_var('email',$res["email"]);
}

//################################### TRACKING ####################################
$MiTemplate->set_block("main", "Tracking", "BLO_hist");
    $queryHist="Select id_historial, DATE_FORMAT(hist_fecha, '%e/%m/%Y %H:%i:%S') hist_fecha,hist_usuario,his_tipo,hist_descripcion from historial where ot_id=".($id_ot+0)." order by id_historial";
query_to_set_var( $queryHist, $MiTemplate, 1, 'BLO_hist', 'Tracking' );

//################################### ACCIONES ####################################
//Inicio todos los botones ocultos
$visible  = 'hidden';
$visible1 = 'hidden';
$visible2 = 'hidden';

//Asigno el tipo de flujo
$queryFlujo = "	SELECT	id_estado, ot_tipo
				FROM	ot
				WHERE	ot_id = " . ($id_ot + 0) ; 
$rqFlujo = tep_db_query($queryFlujo);
$resFlujo = tep_db_fetch_array($rqFlujo);

//Habilita Boton Asignar Fecha de Instalacion en estado Por Confirmar Contacto
	if($resFlujo['id_estado'] == 'VO')
		$visible = 'visible';

//Habilita Boton Ingresar Documento de Entrega en estado Por Confirmar realizacion
	if($resFlujo['id_estado'] == 'VZ')
		$visible1 = 'visible';

//Habilita los botones de ingreso de OS(Visita Toma de Medida, Servicio de Visita y Servicio de Materiales), 
//Solo para el estado Por Asignar y usuarios Diseñador y Jefe de Servicios.
$queryUSR="SELECT PEUS_PER_ID FROM perfilesxusuario where PEUS_USR_ID=".$ses_usr_id.";";
$resUSR = tep_db_fetch_array(tep_db_query($queryUSR));

	if((($resUSR['PEUS_PER_ID'] ==3 || $resUSR['PEUS_PER_ID']==4) && $resFlujo['ot_tipo']=='SV') && $resFlujo['id_estado'] == 'VC'){
		//Verifica si el Servicio es una Instalacion
			$queryInstala="SELECT COUNT(*) as cont
							FROM ot OT
							JOIN os_detalle OD ON OD.id_os= OT.id_os
							WHERE OT.ot_id=".$id_ot." AND OD.osde_subtipoprod='IN'";
			$rqInstala = tep_db_query($queryInstala);
			$resInstala = tep_db_fetch_array($rqInstala);
			if($resInstala[cont] != 0){
				$queryVerOSAso="SELECT os_asoInstala, os_asoMaterial FROM ot WHERE ot_id=".$id_ot.";";
				$rqVerOSAso = tep_db_query($queryVerOSAso);
				$resVerOSAso = tep_db_fetch_array($rqVerOSAso);
				if($resVerOSAso['os_asoInstala'] ==0 || $resVerOSAso['os_asoMaterial'] ==0)
					$visible2 = 'visible';
			}
			else
				$visible2 = 'hidden';
	}	

	$MiTemplate->set_var('visible',  $visible);
	$MiTemplate->set_var('visible1', $visible1);			
	$MiTemplate->set_var('visible2', $visible2);			

// Habilita el boton  de Devoluciones.
	if($resFlujo['id_estado'] == 'VM')
		$visible3 = 'visible';
	else
		$visible3 = 'hidden';
		
	$MiTemplate->set_var('visible3', $visible3);
		

$MiTemplate->set_block("main", "Botones", "BLO_botones");
$queryHist="select distinct id_estado_origen,esta_nombre,id_estado_destino, desc_transicion, estadoterminal, flujo,  (select  fecha_visita_inst from ot where ot_id = $id_ot ) AS fecha_visita  
			from cambiosestado ce
				join estados e on  (e.id_estado=ce.id_estado_destino) 				
			where ce.esta_tipo='TV' 
				and flujo in (0, 1) 
				and ce.id_estado_origen='".$iestado."' 
			ORDER BY ce.orden";
query_to_set_var( $queryHist, $MiTemplate, 1, 'BLO_botones', 'Botones' );

$tipob="hidden";
if (($tieneins=='false')||($reac)){
	if($iestado1!='VO'){
		$limit="limit 0,1";
	}else{
		$limit=" ";
	}
	$tipob="button";
	$MiTemplate->set_var('tipob',$tipob);
	$MiTemplate->set_block("main", "boton_aux", "BLO_boto");
	$queryH="select distinct id_estado_origen,esta_nombre,id_estado_destino, desc_transicion, estadoterminal ,flujo from cambiosestado ce
	join estados e on  (e.id_estado=ce.id_estado_origen) 
	where ce.esta_tipo='TV' 
	and flujo in (0, 1) 
	and desc_transicion<>'Anular OT'
	and desc_transicion<>'Confirmar Contacto'
	and ce.id_estado_origen='".$iestado."' ORDER BY ce.orden ".$limit;
	query_to_set_var( $queryH, $MiTemplate, 1, 'BLO_boto', 'boton_aux' );
}else{
	$MiTemplate->set_var('tipob',$tipob);
}

//################################### OT's Relacionadas ####################################
$MiTemplate->set_block("main", "Otes", "BLO_ot");
    $queryHist="SELECT ot.ot_id ot_id2, ot_tipo ot_tipo2, nombre ot_despacho2, DATE_FORMAT(ot_fechacreacion, '%e/%m/%Y %H:%i:%S') ot_fechacreacion2, esta_nombre esta_nombre2
				FROM ot
				JOIN tipos_despacho td on td.id_tipodespacho = ot.id_tipodespacho
				JOIN estados e on e.id_estado = ot.id_estado
				WHERE id_os = ".($id_os2+0)." order by 1";
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