<?
$pag_ini = '../monitor_ot_pe/index.php';
include "../../includes/aplication_top.php";
include "includes/funciones_pe.php";
require_once('wsgdclient.php');
///////////////////// ACCIONES //////////////////////
if ($accion=='ANULAR'){
	writelog($hist_descripcion."usuario ".get_nombre_usr( $ses_usr_id )."intentó borrar OT ".$id_ot);
	$hist="usuario ".get_nombre_usr( $ses_usr_id )."intentó borrar OT ".$id_ot;
	insertahistorial($hist, null, null, 'USR');
	}
if ($accion=='AgrTr'){
	insertahistorial($hist_descripcion, null, null, 'USR');
	}

//cuando es despachado desde el proveedor
if (($accion=='CambEst') && ($id_tipodespacho!=3) &&($desp_ddpr==1) && (($destino=='ET') || ($destino=='EM'))  ){
	cambia_estado($id_ot,$destino,$transicion,1,$accion,$id_estado); 
}

if (($accion=='CambEst') && ($id_tipodespacho!=3)&& ($desp_ddpr!=1)&&($destino!='EM')){
	$wml_desp=Datos_ws($id_ot);
	$resultado_ws = envia_nueva_gd($wml_desp,$msgerror);
	
	if ($resultado_ws==0){
		//error
		?><script language="JavaScript">
	        window.alert("Existe un problema al enviar Desapacho: \n\t");
        </script>
		<?
		insertahistorial('No se ha generado la orden de Despacho en el sistema de Despacho a domicilio, para la Orden de Trabajo nº'.$id_ot.'-'.$msgerror);		
		}
	//ok
	if ($resultado_ws>0){
	cambia_estado($id_ot,'ED',$desctransaccion,1,'CambEst',$id_estado);
	insertahistorial('Se ha generado la orden de Despacho nº'. $resultado_ws .' en el sistema de Despacho a domicilio');
	}
}
if (($accion=='CambEst') && ($id_tipodespacho==3)){
	cambia_estado($id_ot,'EG',$desctransaccion,1,'CambEst',$id_estado);
	insertahistorial('La OT Nº'.$id_ot." Con Despacho Retira Cliente");
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
$MiTemplate->set_file("main","monitor_ot_pe/monitor_detalle_01_ca.htm");

//################################### DATOS OT ####################################
$MiTemplate->set_block("main", "datosot", "LIS_datosot");
$query="SELECT	ot.ot_id, 
				esta_nombre, 
				t.nombre tipo_despacho, t.id_tipodespacho,
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
				if (osde_descuento<>0,ROUND((osde_precio-osde_descuento)*osde_cantidad),ROUND(osde_precio*osde_cantidad)) 'total'
		FROM os_detalle osd			
		WHERE ot_id = " . ($id_ot + 0) ; 
query_to_set_var( $query, $MiTemplate, 1, 'DET_datosot', 'DetalleOT' );

//################################### DATOS CLIENTE ####################################
$query="SELECT	os.clie_rut, 
				if (clie_tipo = 'P', concat(clie_nombre, ' ', clie_paterno, ' ', clie_materno), clie_razonsocial) nombre,
				d.dire_direccion,
				comu_nombre,
				dire_telefono,
				ot.id_os
		FROM ot join os on os.id_os = ot.id_os 
				join clientes c on c.clie_rut = os.clie_rut 
				join direcciones d on c.clie_rut =  d.clie_rut and dire_defecto = 'P'
				join comuna co on co.id_comuna = d.id_comuna
		WHERE ot.ot_id = " . ($id_ot + 0) ; 
$rq = tep_db_query($query);
$res = tep_db_fetch_array( $rq );

$MiTemplate->set_var('clie_rut',$res["clie_rut"]);
$MiTemplate->set_var('clie_rutdv',dv($res["clie_rut"]));
$MiTemplate->set_var('nombre',$res["nombre"]);
$MiTemplate->set_var('dire_direccion',$res["dire_direccion"]);
$MiTemplate->set_var('comu_nombre',$res["comu_nombre"]);
$MiTemplate->set_var('dire_telefono',$res["dire_telefono"]);

$MiTemplate->set_block("main", "DireServicio", "DET_direservicio");
$query="SELECT	d.dire_direccion dire_direccion_2,
				comu_nombre comu_nombre_2,
				dire_telefono dire_telefono_2
		FROM ot join os on os.id_os = ot.id_os 
				join direcciones d on os.id_direccion =  d.id_direccion 
				join comuna co on co.id_comuna = d.id_comuna
		WHERE ot.ot_id = " . ($id_ot + 0) ; 
query_to_set_var( $query, $MiTemplate, 1, 'DET_direservicio', 'DireServicio' );
//################################### PROVEEDOR ####################################
$qry=" select id_estado ,id_proveedor, id_os from ot  where ot_id=" . ($id_ot + 0) ;
//writelog($qry);
$rq = tep_db_query($qry);
$res = tep_db_fetch_array( $rq );
	if ($res["id_proveedor"]){
		$idp=$res["id_proveedor"];
	}
$id_os2=$res["id_os"];
if ($idp){
	$wherep=" and pv.id_proveedor=".$idp;
	$tieneprv="true";
	$MiTemplate->set_var('msg','');
}else{
	$MiTemplate->set_var('msg','No se ha asignado proveedor a esta OT');
	$tieneprv="false";
	$tipoE="hidden";
}

$beginC="";
$endC="";

if (($res["id_estado"]=='EC')&&(!$idp)){
	$beginC="<!---";
	$endC="-->";
	$MiTemplate->set_var('begincomment',$beginC);
	$MiTemplate->set_var('endcomment',$endC);
}else {
		$MiTemplate->set_var('begincomment',$beginC);
		$MiTemplate->set_var('endcomment',$endC);
	 //Recuperamos proveedor
		$query_PR = "SELECT pv.id_proveedor, pv.rut_prov,pv.razsoc_prov,pv.nom_prov, ex.nomcontacto,pv.fonocto_prov,ex.mailcontacto,pp.cod_prod1 ,osd.id_tipodespacho
		FROM proveedores pv
			left join proveedores_ext ex on (pv.id_proveedor=ex.id_proveedor)
			join prodxprov pp on (pp.cod_prov=pv.cod_prov)
			join os_detalle osd on (osd.id_producto=pp.id_producto)	
		where osd.ot_id= " . ($id_ot + 0)." $wherep" ;
//writelog($query_PR);
		$rq = tep_db_query($query_PR);
		$res = tep_db_fetch_array( $rq );
		$MiTemplate->set_var('rut_prov',$res["rut_prov"]);
		$MiTemplate->set_var('prov_rutdv',dv($res["rut_prov"]));
		$MiTemplate->set_var('razsoc_prov',$res["razsoc_prov"]);
		$MiTemplate->set_var('nomcontacto',$res["nomcontacto"]);
		$MiTemplate->set_var('fonocto_prov',$res["fonocto_prov"]);
		$MiTemplate->set_var('mailcontacto',$res["mailcontacto"]);
		$MiTemplate->set_var('id_tipodespacho',$res["id_tipodespacho"]);

}
################################### TRACKING ####################################
$MiTemplate->set_block("main", "Tracking", "BLO_hist");
    $queryHist="Select id_historial, DATE_FORMAT(hist_fecha, '%e/%m/%Y %H:%i:%S') hist_fecha,hist_usuario,his_tipo,hist_descripcion from historial where ot_id=".($id_ot+0)." order by id_historial";
query_to_set_var( $queryHist, $MiTemplate, 1, 'BLO_hist', 'Tracking' );

//################################### ACCIONES ####################################
//Asigno el tipo de flujo
$query = "	SELECT	id_tipodespacho, ot_tipo, desp_ddp,id_estado
			FROM	ot
			WHERE	ot_id = " . ($id_ot + 0) ; 
$rq = tep_db_query($query);
$res = tep_db_fetch_array( $rq );

if ($res['desp_ddp'])
	$flujo = 3;
elseif ($res['id_tipodespacho'] == '3')
	$flujo = 1;
else 
	$flujo = 2;

if ($res['desp_ddp'])
	$flujo = 3;

	if($flujo==1||$flujo==2){
		$flujoot=" flujo in (0,".$flujo.")";
	}else{
		$flujoot=" flujo in (0,".$flujo.")";
	}

//$MiTemplate->set_block("main", "Botones", "BLO_botones");
$MiTemplate->set_block("main", "Botones", "BLO_botones");
$queryHist="SELECT distinct desc_transicion, ce.orden,flujo,id_estado_destino, estadoterminal ,desp_ddp
			FROM ot join cambiosestado ce on 
						ce.id_estado_origen = ot.id_estado 
					join estados e on 
						e.id_estado = id_estado_destino 
			WHERE ot_id = ".($id_ot+0)." and 
				ot_tipo = 'PE' and $flujoot
				and ot.id_estado not in ('ED','EG')
			ORDER BY ce.orden limit 2";
	if ( $rq = tep_db_query($queryHist) ){
		while( $res = tep_db_fetch_array( $rq ) ) {
			if($res['id_estado_destino']!='ER'){
				$MiTemplate->set_var('desc_transicion',tohtml( $res['desc_transicion']));
				$MiTemplate->set_var('orden',tohtml( $res['orden']));
				$MiTemplate->set_var('id_estado_destino',$res['id_estado_destino']);
				$MiTemplate->set_var('estadoterminal',tohtml( $res['estadoterminal']));
				$MiTemplate->set_var('desp_ddp',tohtml( $res['desp_ddp']));
				$MiTemplate->parse("BLO_botones", "Botones", true);   
			}
		}
	}

//query_to_set_var( $queryHist, $MiTemplate, 1, 'BLO_botones', 'Botones' );


$tipob="hidden";
if ($tieneprv=='false'){
	$tipob="button";
	$MiTemplate->set_var('tipob',$tipob);
	$MiTemplate->set_block("main", "boton_aux", "BLO_boto");
	$queryH="SELECT distinct desc_transicion, ce.orden,flujo,id_estado_destino, estadoterminal 
				FROM ot join cambiosestado ce on 
							ce.id_estado_origen = ot.id_estado 
						join estados e on 
							e.id_estado = id_estado_destino 
				WHERE ot_id = ".($id_ot+0)." and 
					ot_tipo = 'PE' and $flujoot 
					and ot.id_estado not in ('EC','ED','EG')
					and desc_transicion<>'Anular OT'
				ORDER BY ce.orden";
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