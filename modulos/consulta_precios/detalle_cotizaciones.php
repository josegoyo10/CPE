<?
$pag_ini = '../consulta_precios/index.php';
include "../../includes/aplication_top.php";

include_idioma_mod( $ses_idioma, "sp" );

/****************************************************************
 *
 * Despliega Listado diseñador
 *
 ****************************************************************/
function DetalleDisenador($ARR){
	global $ses_usr_id ;
	$MiTemplate = new Template();
	$MiTemplate->debug = 0;
	$MiTemplate->set_root(DIRTEMPLATES);
	$MiTemplate->set_unknowns("remove");
	$MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
	$MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);
	$MiTemplate->set_var("SUBTITULO1",TEXT_2);
	$MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));

	// Agregamos el header
	$MiTemplate->set_file("header","header_ident.html");

	// Agregamos el main
	$MiTemplate->set_file("main","consulta_precios/detalle_disenador.htm");
	/*Saca el origen del usuario*/
	$Ori_Gen=get_login_origen($ses_usr_id);
	if ($Ori_Gen){
		$aux_ori= "and os.USR_ORIGEN=".$Ori_Gen;
		$aux_ori_det= "and o.USR_ORIGEN=".$Ori_Gen;
	}else{
		$aux_ori= " ";
	}

	if ($ARR['accion']!='Detalle'){
		$MiTemplate->set_var("iniTable",'<!--');
		$MiTemplate->set_var("finTable",'--!>');
	}
	$MiTemplate->set_var("fecha_inicio",$ARR['fechaIni']);
	$fechaIni=$ARR['fechaIni'];
	$fechaTer=$ARR['fechaTer'];
	$MiTemplate->set_var("fechaIni",$fechaIni);
	$MiTemplate->set_var("fechaTer",$fechaTer);

	if($ARR['fechaIni']){
		$aux=$ARR['fechaIni'];
		$a = split("/", $aux);
		$fecha_ini_qry=$a[2]."-".$a[1]."-".$a[0]." "."00:00:00";
		$aux_fi=" and os_fechacotizacion >='".$fecha_ini_qry."'";
	}

	$MiTemplate->set_var("fecha_termino",$ARR['fechaTer']);
	if($ARR['fechaTer']){
		$auxT=$ARR['fechaTer'];
		$aT = split("/", $auxT);
		$fecha_ter_qry=$aT[2]."-".$aT[1]."-".$aT[0]." "."00:00:00";
		$aux_ft=" and os_fechacotizacion <='".$fecha_ter_qry."'";
	}
	$MiTemplate->set_var("fecha_inicio",$ARR['fechaIni']);
	$MiTemplate->set_var("fecha_termino",$ARR['fechaTer']);

	if($ARR['local'] && !$ARR['numero_local']){
		$aux_local=" and l.id_local=".$ARR['local'];
		$auxLocalD=" and o.id_local=".$ARR['local'];
		$MiTemplate->set_var("numero_local",$ARR['local']);
	}
	if($ARR['numero_local']){
		$aux_local=" and l.id_local=".$ARR['numero_local'];
		$auxLocalD=" and o.id_local=".$ARR['numero_local'];
		$MiTemplate->set_var("numero_local",$ARR['numero_local']);
	}

	if($ARR['usuario']){
		$aux_usuario=" and u.USR_ID=".$ARR['usuario'];
		$aux_usrD="  and o.USR_ID=".$ARR['usuario'];
	}
	$MiTemplate->set_var("fecha_inicio",$ARR['fechaIni']);
	$MiTemplate->set_var("fecha_termino",$ARR['fechaTer']);
	$MiTemplate->set_var("usuario",$ARR['usuario']);

	$query_diproy="select l.nom_local local,
	concat(u.USR_NOMBRES, ' ', u.USR_APELLIDOS) disenador,u.USR_ID IDUSER,
	sum(if(os.id_estado in ('SE', 'SP', 'SM'), 1, 0)) proycotizados,
	sum(if(os.id_estado in ('SP', 'SM'), 1, 0)) proyvendidos,
	sum(if(os.id_estado in ('SM'), 1, 0)) proyterminados,l.id_local
	from      os os
	join locales l on l.id_local = os.id_local
	join usuarios u on u.USR_ID = os.USR_ID
	where   os.id_estado in ('SE', 'SP', 'SM')
	$aux_ori
	$aux_local
	$aux_usuario
	$aux_fi
	$aux_ft
	group by 1, 2
	order by 1, 2";
	$rq1 = tep_db_query($query_diproy); 

	$query_diproyDet="select l.nom_local local,
	concat(u.USR_NOMBRES, ' ', u.USR_APELLIDOS) disenador,
	sum(round(if(os.id_estado in ('SP', 'SM') and osd.osde_tipoprod = 'PS', osde_cantidad, 0))) cantPS,
	sum(round((if(osde_precio is not null, osde_precio, 0)-if(osde_descuento is not null, osde_descuento, 0))*if(os.id_estado in ('SP', 'SM') and osd.osde_tipoprod = 'PS', osde_cantidad, 0))) montoPS,
	sum(round(if(os.id_estado in ('SP', 'SM') and osd.osde_tipoprod = 'PE', osde_cantidad, 0))) cantPE,
	sum(round((if(osde_precio is not null, osde_precio, 0)-if(osde_descuento is not null, osde_descuento, 0))*if(os.id_estado in ('SP', 'SM') and osd.osde_tipoprod = 'PE', osde_cantidad, 0))) montoPE,
	sum(round(if(os.id_estado in ('SP', 'SM') and osd.osde_subtipoprod = 'VI', osde_cantidad, 0))) cantVI,
	sum(round((if(osde_precio is not null, osde_precio, 0)-if(osde_descuento is not null, osde_descuento, 0))*if(os.id_estado in ('SP', 'SM') and osd.osde_subtipoprod = 'VI', osde_cantidad, 0))) montoVI,
	sum(round(if(os.id_estado in ('SP', 'SM') and (osd.osde_subtipoprod = 'IN' or osd.osde_subtipoprod = 'IR'), osde_cantidad, 0))) cantIN,
	sum(round((if(osde_precio is not null, osde_precio, 0)-if(osde_descuento is not null, osde_descuento, 0))*if(os.id_estado in ('SP', 'SM') and (osd.osde_subtipoprod = 'IN' or osd.osde_subtipoprod = 'IR'), osde_cantidad, 0))) montoIN,
	sum(round((if(osde_precio is not null, osde_precio, 0)-if(osde_descuento is not null, osde_descuento, 0))*if(os.id_estado in ('SP', 'SM') and osd.osde_tipoprod = 'PS', osde_cantidad, 0)))+sum(round((if(osde_precio is not null, osde_precio, 0)-if(osde_descuento is not null, osde_descuento, 0))*if(os.id_estado in ('SP', 'SM') and osd.osde_tipoprod = 'PE', osde_cantidad, 0))) + sum(round((if(osde_precio is not null, osde_precio, 0)-if(osde_descuento is not null, osde_descuento, 0))*if(os.id_estado in ('SP', 'SM') and osd.osde_subtipoprod = 'VI', osde_cantidad, 0))) + sum(round((if(osde_precio is not null, osde_precio, 0)-if(osde_descuento is not null, osde_descuento, 0))*if(os.id_estado in ('SP', 'SM') and (osd.osde_subtipoprod = 'IN' or osd.osde_subtipoprod = 'IR'), osde_cantidad, 0))) total,l.id_local
	from      os os
	join os_detalle osd on os.id_os = osd.id_os
	join locales l on l.id_local = os.id_local
	join usuarios u on u.USR_ID = os.USR_ID
	where   os.id_estado in ('SE', 'SP', 'SM')
	$aux_ori
	$aux_local
	$aux_usuario
	$aux_fi
	$aux_ft
	group by 1, 2
	order by 1, 2";

	$queryexcel=$query_diproyDet;
	$j=0;
	$rq = tep_db_query($query_diproyDet); 
	if ($ARR['local']>0){
		$query_lo="select nom_local from locales where id_local=".$ARR['local'];
		$rq1 = tep_db_query($query_lo); 
		$res1= tep_db_fetch_array( $rq1 );
		$MiTemplate->set_var("nom_local_titulo",$res1['nom_local']);
	}else{
		$MiTemplate->set_var("nom_local_titulo",'Cadena Easy');
	}

	if(($ARR['fechaIni'])&&($ARR['fechaTer'])){
		$MiTemplate->set_var("fecha_inicio_msg",' entre el '.$ARR['fechaIni']);
		$MiTemplate->set_var("fecha_termino_msg",' y el '.$ARR['fechaTer']);
	}
	if(($ARR['fechaIni'])&&(!$ARR['fechaTer'])){
		$MiTemplate->set_var("fecha_inicio_msg",' desde el '.$ARR['fechaIni']);
		$MiTemplate->set_var("fecha_termino_msg",'');
	}
	if((!$ARR['fechaIni'])&&($ARR['fechaTer'])){
		$MiTemplate->set_var("fecha_inicio_msg",'');
		$MiTemplate->set_var("fecha_termino_msg",' hasta el '.$ARR['fechaTer']);
	}
	if ($ARR['accion']!='Detalle'){
		$MiTemplate->set_var("tamano1",'500');
		$MiTemplate->set_var("tamano2",'500');
		$MiTemplate->set_var("alinear",'center');
		$MiTemplate->set_var("inicio",'');
		$MiTemplate->set_var("fin",'');
	}
	$r_proycotizados=0;
	$r_proyvendidos=0;
	$r_proyterminados=0;
	$r_cantPS=0;
	$r_montoPS=0;
	$r_cantPE=0;
	$r_montoPE=0;
	$r_cantVI=0;
	$r_montoVI=0;
	$r_cantIN=0;
	$r_montoIN=0;
	$r_total=0;
	$MiTemplate->set_block("main_", "detalle_dis", "blo_DI");
	if (( $rq1 = tep_db_query($query_diproy) )&& ( $rq = tep_db_query($query_diproyDet) )){
		while( $res1 = tep_db_fetch_array( $rq1 ) ) {
			$res = tep_db_fetch_array( $rq );	
			$MiTemplate->set_var("ini_normal",'');
			$MiTemplate->set_var("fin_normal",'');
			/*asignacion de variables*/
			$r_proycotizados+=$res1['proycotizados'];
			$r_proyvendidos+=$res1['proyvendidos'];
			$r_proyterminados+=$res1['proyterminados'];
			$r_cantPS+=$res['cantPS'];
			$r_montoPS+=$res['montoPS'];
			$r_cantPE+=$res['cantPE'];
			$r_montoPE+=$res['montoPE'];
			$r_cantVI+=$res['cantVI'];
			$r_montoVI+=$res['montoVI'];
			$r_cantIN+=$res['cantIN'];
			$r_montoIN+=$res['montoIN'];
			$r_total+=$res['total'];
			$i++;
			$nom_local=$res['local'];
			$MiTemplate->set_var("disenador_nombre",$res['disenador']);
			$MiTemplate->set_var("proycotizados",$res1['proycotizados']);
			$t_proycotizados+=$res1['proycotizados'];
			$MiTemplate->set_var("proyvendidos",$res1['proyvendidos']);
			$t_proyvendidos+=$res1['proyvendidos'];
			$MiTemplate->set_var("proyterminados",$res1['proyterminados']);
			$t_proyterminados+=$res['proyterminados'];
			$MiTemplate->set_var("cantPS",$res['cantPS']);
			$t_cantPS+=$res['cantPS'];
			$MiTemplate->set_var("montoPS",formato_precio($res['montoPS']));
			$t_montoPS+=$res['montoPS'];
			$MiTemplate->set_var("cantPE",$res['cantPE']);
			$t_cantPE+=$res['cantPE'];
			$MiTemplate->set_var("montoPE",formato_precio($res['montoPE']));
			$t_montoPE+=$res['montoPE'];
			$MiTemplate->set_var("cantVI",$res['cantVI']);
			$t_cantVI+=$res['cantVI'];
			$MiTemplate->set_var("montoVI",formato_precio($res['montoVI']));
			$t_montoVI+=$res['montoVI'];
			$MiTemplate->set_var("cantIN",$res['cantIN']);
			$t_cantIN+=$res['cantIN'];
			$MiTemplate->set_var("montoIN",formato_precio($res['montoIN']));
			$t_montoIN+=$res['montoIN'];
			$MiTemplate->set_var("total",formato_precio($res['total']));
			$t_total+=$res['total'];
			$MiTemplate->parse("blo_DI", "detalle_dis", true);
		}
	}

	/* segunda parte parte del detalle de las os */
	$MiTemplate1 = new Template();
	$MiTemplate1->debug = 0;
	$MiTemplate1->set_root(DIRTEMPLATES);
	$MiTemplate1->set_unknowns("remove");
	$MiTemplate1->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
	$MiTemplate1->set_var("TEXT_TITULO",TEXT_TITULO);
	$MiTemplate1->set_var("SUBTITULO1",TEXT_2);
	$MiTemplate1->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));
	if (($ARR['accion']=='VerReporte')||($ARR['accion']=='Imprimir')||($ARR['accion']=='Exportar')){
		$MiTemplate1->set_file("body","consulta_precios/detalle_cotizaciones_detalle.htm");	
		/*para link de accion*/
		$MiTemplate1->set_var("inilink",'');
		$MiTemplate1->set_var("finlink",'');
		if ($ARR['accion']=='Imprimir'){
			$MiTemplate1->set_var("tamano",'630');
		}else{
			$MiTemplate1->set_var("tamano",'750');
		}
		if($ARR['numUsr']){
			$aux_usrD="  and o.USR_ID=".$ARR['numUsr'];
		}
		if($ARR['numLoc']){
			$auxLocalD="  and o.id_local=".$ARR['numLoc'];
		}
		$MiTemplate1->set_var("inicio_titulo",'<!--');
		$MiTemplate1->set_var("fin_titulo",'!-->');
		if ($ARR['accion']=='Imprimir'){
			$MiTemplate1->set_var("inicio_titulo",'');
			$MiTemplate1->set_var("fin_titulo",'');
			$MiTemplate1->set_var("inilink",'<!--');
			$MiTemplate1->set_var("finlink",'-->');
		}
	/*lo del left lo puse pk habia algunas os sin detalle, las primeras que hicimos*/
		$MiTemplate1->set_var("disenador_nombre",$res['disenador']);
		$query_detalle="SELECT o.id_os,osd.ot_id, o.os_descripcion, date_format(os_fechacreacion, '%d/%m/%Y') os_fechacreacion, c.clie_rut, c.clie_nombre, c.clie_paterno, c.clie_telcontacto1, e.esta_nombre
		FROM os o
		left join os_detalle osd on o.id_os = osd.id_os
		join clientes c on c.clie_rut = o.clie_rut
		join estados e on e.id_estado = o.id_estado and o.id_os > 0
		WHERE o.id_estado in ('SE', 'SP', 'SM')
		$aux_ori_det
		$aux_usrD 
		$auxLocalD 
		$aux_fi 
		$aux_ft
		group by 1 
		order by 1 ";
		if ($ARR['accion']=='Exportar'){
			$query_excel=$query_detalle;
			$hola=exportar_excel ($query_excel);
		}
		$MiTemplate1->set_block("body", "Modulo_Detalle", "detalle");
			if ( $rq = tep_db_query($query_detalle) ){
				while( $res = tep_db_fetch_array( $rq ) ) {
					$MiTemplate1->set_var("id_os",$res['id_os']);
					$MiTemplate1->set_var("os_comentarios",$res['os_descripcion']);
					$MiTemplate1->set_var("clie_nombre",$res['clie_nombre']);
					$MiTemplate1->set_var("clie_paterno",$res['clie_paterno']);
					$MiTemplate1->set_var("os_fechacreacion",$res['os_fechacreacion']);
					$MiTemplate1->set_var("clie_rut",$res['clie_rut'].'-'.dv($res['clie_rut']));
					$MiTemplate1->set_var("clie_telcontacto1",$res['clie_telcontacto1']);
					$MiTemplate1->set_var("esta_nombre",$res['esta_nombre']);
					$id_os=$res['id_os'];
					$impre="javascript:popUpWindowModal('printframe_os.php?id_os=$id_os', 100, 100, 710, 500);";
					$MiTemplate1->set_var("impre",$impre);
					$MiTemplate1->parse("detalle", "Modulo_Detalle", true);
				}
			}
	}

	if ($ARR['accion']=='VerReporte'){
		// Agregamos el footer
		include "../../includes/footer_cproy.php";
		$MiTemplate->pparse("OUT_H", array("header"), false);
		$MiTemplate->parse("OUT_F", array("footer"), true);
		include "../../menu/menu.php";
		$MiTemplate->parse("OUT_M", array("main"), true);
		$MiTemplate1->parse("OUT_M", array("body"), true);
		$MiTemplate->p("OUT_M");
		$MiTemplate1->p("OUT_M");
		$MiTemplate->p("OUT_F");
		include "../../includes/application_bottom.php";
	}
	if ($ARR['accion']=='Imprimir'){
		// Agregamos el footer
		$MiTemplate1->parse("OUT_M", array("body","footer"), true);
		$MiTemplate1->p("OUT_M");
		include "../../includes/application_bottom.php";
	}

}#fin function

function exportar_excel($query) {
	$t_proycotizados=0;
	$t_proyvendidos=0;
	$t_proyterminados=0;
	$t_cantPS=0;
	$t_montoPS=0;
	$t_cantPE=0;
	$t_montoPE=0;
	$t_cantVI=0;
	$t_montoVI=0;
	$t_cantIN=0;
	$t_montoIN=0;
	$t_total=0;
	$vcstr = "Nº OS\tDetalle\tFecha creación\tRut cliente\tNombre\tFono\tEstado\n";

	$rq = tep_db_query($query);
	while( $res = tep_db_fetch_array( $rq ) ) {
		$vcstr .= $res['id_os'] . "\t";
		$vcstr .= $res['os_descripcion'] . "\t";
		$vcstr .= $res['os_fechacreacion'] . "\t";
		$vcstr .= $res['clie_rut'].'-'.dv($res['clie_rut']) . "\t";
		$vcstr .= $res['clie_nombre'].' '.$res['clie_paterno']. "\t";
		$vcstr .= $res['clie_telcontacto1'] . "\t";
		$vcstr .= $res['esta_nombre'] . "\t";
		$vcstr .= "\n";
	}
	$fname = 'VentaDisenador_'.date("YmdHis").'.xls';
	header("Content-Type: application/vnd.ms-excel");
	header("Content-Length: ".strlen($vcstr));
	header("Content-Disposition: attachment; filename=".$fname);
	echo $vcstr;

	tep_exit();
}
/****************************/

switch($accion){
	case "Detalle":
		DetalleDisenador(array_merge ($HTTP_GET_VARS, $HTTP_POST_VARS));
	break;
	case "Imprimir":
		DetalleDisenador(array_merge ($HTTP_GET_VARS, $HTTP_POST_VARS));
	break;
	case "VerReporte":
		DetalleDisenador(array_merge ($HTTP_GET_VARS, $HTTP_POST_VARS));
	break;
	case "Exportar":
	/*para la impresion en excel */
	DetalleDisenador(array_merge ($HTTP_GET_VARS, $HTTP_POST_VARS));
	break;
	default:
		DisplayListado();
	break;
}

?>