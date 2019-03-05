<?
$pag_ini = '../consulta_precios/index.php';
include "../../includes/aplication_top.php";

include_idioma_mod( $ses_idioma, "sp" );

/****************************************************************
 *
 * Despliega Listado diseñador
 *
 ****************************************************************/
    global $ses_usr_id ,$queryexcel,$arr_local;
    $MiTemplate = new Template();
    // asignamos degug maximo
    $MiTemplate->debug = 0;
    // root directory de los templates
    $MiTemplate->set_root(DIRTEMPLATES);
    // variables perdidas
    $MiTemplate->set_unknowns("remove");

    $MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
    $MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);
    $MiTemplate->set_var("SUBTITULO1",TEXT_2);
    $MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));
    // Agregamos el header
    $MiTemplate->set_file("header","header_ident.html");

    // Agregamos el main
    $MiTemplate->set_file("main","consulta_precios/ReporteVentasDisenador.htm");

	/*Saca el origen del usuario*/
	$Ori_Gen=get_login_origen($ses_usr_id);
	if ($Ori_Gen){
		$aux_ori= "and os.USR_ORIGEN=".$Ori_Gen;
	}else{
		$aux_ori= " ";
	}
	
	$MiTemplate->set_var("select_local",$select_local);
	/* si trae local se filtra , si no son Todos*/
	if ($select_local>0){
		$aux_local=" and l.id_local =".($select_local+0);
	}else{
		$aux_local="";	
	}
	$MiTemplate->set_var("fecha_inicio",$fecha_inicio);
	if($fecha_inicio){
		$aux=$fecha_inicio;
		$a = split("/", $aux);
		$fecha_ini_qry=$a[2]."-".$a[1]."-".$a[0]." "."00:00:00";
		$aux_fi=" and os_fechacotizacion >='".$fecha_ini_qry."'";
	}
	
	$MiTemplate->set_var("fecha_termino",$fecha_termino);
	if($fecha_termino){
		$auxT=$fecha_termino;
		$aT = split("/", $auxT);
		$fecha_ter_qry=$aT[2]."-".$aT[1]."-".$aT[0]." "."00:00:00";
		$aux_ft=" and os_fechacotizacion <='".$fecha_ter_qry."'";
	}
	$MiTemplate->set_var("fecha_inicio",$fecha_inicio);
	$MiTemplate->set_var("fecha_termino",$fecha_termino);

	$MiTemplate->set_var("cat_cliente",$cat_cliente);
	if($cat_cliente){
		$auxCcli = "and c.clie_categoria_cliente='".$cat_cliente."'";
	}
	
	//Recuperamos los Locales
    $MiTemplate->set_block("main", "Locales", "BLO_Loc");
	$queryL="SELECT os.id_local as id_local, os.nom_local as nom_local, if('$select_local'=os.id_local, 'selected', '') 'selected' 
			FROM locales os WHERE 1
			ORDER BY nom_local";
	query_to_set_var( $queryL, $MiTemplate, 1, 'BLO_Loc', 'Locales' );   
	
	// Recuperamos la Categoria de los Clientes
	$MiTemplate->set_block("main", "cat_cliente", "BLO_cat_cliente");
	if($cat_cliente == '0' || $cat_cliente == '')		
		$query = "SELECT id_categoria, nombre_categoria  FROM  tipo_categoria_cliente";	
	else		
		$query = "SELECT id_categoria, nombre_categoria, case WHEN id_categoria = '$cat_cliente' then 'selected' else '' end selected  FROM  tipo_categoria_cliente";
	
	query_to_set_var($query, $MiTemplate, 1, 'BLO_cat_cliente', 'cat_cliente');
	// Fin de la recuperacion

if (($accion=='VerReporte')||($accion=='Imprimir')||($accion=='Exportar')){
	$query_diproy="select l.nom_local local,
            concat(u.USR_NOMBRES, ' ', u.USR_APELLIDOS) disenador,u.USR_ID IDUSER,
            sum(if(os.id_estado in ('SE', 'SP', 'SM'), 1, 0)) proycotizados,
            sum(if(os.id_estado in ('SP', 'SM'), 1, 0)) proyvendidos,
            sum(if(os.id_estado in ('SM'), 1, 0)) proyterminados,l.id_local
			from os os
			join clientes c on c.clie_rut = os.clie_rut
            join locales l on l.id_local = os.id_local
            join usuarios u on u.USR_ID = os.USR_ID
			where   os.id_estado in ('SE', 'SP', 'SM')
			$aux_ori
			$aux_local
			$aux_fi
			$aux_ft
			$auxCcli
			group by 1, 2
			order by 1, 2";
			/*primera parte para excel*/
			$queryexcel_ini=$query_diproy;

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
			from os os
			join clientes c on c.clie_rut = os.clie_rut
            join os_detalle osd on os.id_os = osd.id_os
            join locales l on l.id_local = os.id_local
            join usuarios u on u.USR_ID = os.USR_ID
			where   os.id_estado in ('SE', 'SP', 'SM')
			$aux_ori
			$aux_local
			$aux_fi
			$aux_ft
			$auxCcli
			group by 1, 2
			order by 1, 2";
			/*segunda parte para excel*/
			$queryexcel_det=$query_diproyDet;
	
			$j=0;
			$rq = tep_db_query($query_diproy); 
			if ( tep_db_fetch_array( $rq ) ) {
				$MiTemplate->set_file("body","consulta_precios/ReporteVentasDisenador_Detalle.htm");	
			}else{
				$MiTemplate->set_file("body","consulta_precios/ReporteVentasProducto_Msg.htm");	
			}
		$MiTemplate->set_block("body", "titulo_pro", "blo_titulo");
		if ($select_local>0){
			$query_lo="select nom_local from locales where id_local=".$select_local;
			$rq1 = tep_db_query($query_lo); 
			$res1= tep_db_fetch_array( $rq1 );
			$MiTemplate->set_var("nom_local_titulo",$res1['nom_local']);
		}else{
			$MiTemplate->set_var("nom_local_titulo",'Cadena Easy');
		}
	
		if(($fecha_inicio)&&($fecha_termino)){
			$MiTemplate->set_var("fecha_inicio_msg",' entre el '.$fecha_inicio);
			$MiTemplate->set_var("fecha_termino_msg",' y el '.$fecha_termino);
		}
		if(($fecha_inicio)&&(!$fecha_termino)){
			$MiTemplate->set_var("fecha_inicio_msg",' desde el '.$fecha_inicio);
			$MiTemplate->set_var("fecha_termino_msg",'');
		}
		if((!$fecha_inicio)&&($fecha_termino)){
			$MiTemplate->set_var("fecha_inicio_msg",'');
			$MiTemplate->set_var("fecha_termino_msg",' hasta el '.$fecha_termino);
		}
		if ($accion=='VerReporte'){
			$MiTemplate->set_var("tamano1",'700');
			$MiTemplate->set_var("tamano2",'780');
			$MiTemplate->set_var("alinear",'center');
			$MiTemplate->set_var("inicio",'<!--');
			$MiTemplate->set_var("fin",'-->');

		}else{
			$MiTemplate->set_var("tamano1",'500');
			$MiTemplate->set_var("tamano2",'500');
			$MiTemplate->set_var("alinear",'center');
			$MiTemplate->set_var("inicio",'');
			$MiTemplate->set_var("fin",'');
		}
	$MiTemplate->parse("blo_titulo", "titulo_pro", true);
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

	$id_local=0;
	$primero=true;

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
	$MiTemplate->set_block("body", "detalle_dis", "blo_DI");
	$MiTemplate->set_var("resumen_local",'<!--');
	$MiTemplate->set_var("fin_resumen",'-->');
		if (( $rq = tep_db_query($query_diproy) )&& ( $rq1 = tep_db_query($query_diproyDet) )){
			while( $res = tep_db_fetch_array( $rq ) ) {
			$res1 = tep_db_fetch_array( $rq1 );	
				$MiTemplate->set_var("ini_normal",'');
				$MiTemplate->set_var("fin_normal",'');
				$MiTemplate->set_var("resumen_local",'<!--');
				$MiTemplate->set_var("fin_resumen",'-->');
				$local=$res['id_local'];
				if( $id_local!=$res['id_local'] && !$primero){
				/*	desplegar_resumen($id_local);*/
					$MiTemplate->set_var("resumen_local",'');
					$MiTemplate->set_var("fin_resumen",'');
					$MiTemplate->set_var("nom_local",$nom_local);
					$MiTemplate->set_var("r_proycotizados",formato_precio($r_proycotizados));
					$MiTemplate->set_var("r_proyvendidos",formato_precio($r_proyvendidos));
					$MiTemplate->set_var("r_proyterminados",formato_precio($r_proyterminados));
					/*parte de los productos*/
					$MiTemplate->set_var("r_cantPS",formato_precio($r_cantPS));
					$MiTemplate->set_var("r_montoPS",formato_precio($r_montoPS));
					$MiTemplate->set_var("r_cantPE",formato_precio($r_cantPE));
					$MiTemplate->set_var("r_montoPE",formato_precio($r_montoPE));
					$MiTemplate->set_var("r_cantVI",formato_precio($r_cantVI));
					$MiTemplate->set_var("r_montoVI",formato_precio($r_montoVI));
					$MiTemplate->set_var("r_cantIN",formato_precio($r_cantIN));
					$MiTemplate->set_var("r_montoIN",formato_precio($r_montoIN));
					$MiTemplate->set_var("r_total",formato_precio($r_total));
					/* fin parte productos*/
				/*volver a cero las variables*/
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
				}


				$id_local=$res['id_local'];
				$primero=false;								

				/*asignacion de variables*/
				$r_proycotizados+=$res['proycotizados'];
				$r_proyvendidos+=$res['proyvendidos'];
				$r_proyterminados+=$res['proyterminados'];
				$r_cantPS+=$res1['cantPS'];
				$r_montoPS+=$res1['montoPS'];
				$r_cantPE+=$res1['cantPE'];
				$r_montoPE+=$res1['montoPE'];

				$r_cantVI+=$res1['cantVI'];
				$r_montoVI+=$res1['montoVI'];

				$r_cantIN+=$res1['cantIN'];
				$r_montoIN+=$res1['montoIN'];
				$r_total+=$res1['total'];
			
				$i++;
				$MiTemplate->set_var("i",$i);
				$MiTemplate->set_var("local",$res['local']);
				$nom_local=$res['local'];
				$local_num=$res['id_local'];
				$USR=$res['IDUSER'];
				$img="<a href='#' onClick='DetalleDiseno($local_num,$USR)'><img src='../img/info.gif' width='16' height='16' border='0'></a>";
				if ($accion!='Imprimir'){
					$MiTemplate->set_var("disenador",$res['disenador'].' '.$img);
				}else{
					$MiTemplate->set_var("disenador",$res['disenador']);
				}
				$MiTemplate->set_var("numero_local",$local_num);
				$MiTemplate->set_var("id_local",$res['id_local']);
				$MiTemplate->set_var("proycotizados",$res['proycotizados']);
				$t_proycotizados+=$res['proycotizados'];
				$MiTemplate->set_var("proyvendidos",$res['proyvendidos']);
				$t_proyvendidos+=$res['proyvendidos'];
				$MiTemplate->set_var("proyterminados",$res['proyterminados']);
				$t_proyterminados+=$res['proyterminados'];
				$MiTemplate->set_var("cantPS",$res1['cantPS']);
				$t_cantPS+=$res1['cantPS'];
				$MiTemplate->set_var("montoPS",formato_precio($res1['montoPS']));
				$t_montoPS+=$res1['montoPS'];
				$MiTemplate->set_var("cantPE",$res1['cantPE']);
				$t_cantPE+=$res1['cantPE'];
				$MiTemplate->set_var("montoPE",formato_precio($res1['montoPE']));
				$t_montoPE+=$res1['montoPE'];
				$MiTemplate->set_var("cantVI",$res1['cantVI']);
				$t_cantVI+=$res1['cantVI'];
				$MiTemplate->set_var("montoVI",formato_precio($res1['montoVI']));
				$t_montoVI+=$res1['montoVI'];
				$MiTemplate->set_var("cantIN",$res1['cantIN']);
				$t_cantIN+=$res1['cantIN'];
				$MiTemplate->set_var("montoIN",formato_precio($res1['montoIN']));
				$t_montoIN+=$res1['montoIN'];
				$MiTemplate->set_var("total",formato_precio($res1['total']));
				$t_total+=$res1['total'];
				$MiTemplate->parse("blo_DI", "detalle_dis", true);
			}
			if( $id_local!=$res['id_local'] && !$primero){
			/*	desplegar_resumen($id_local);*/
				$MiTemplate->set_var("resumen_local",'');
				$MiTemplate->set_var("fin_resumen",'');
				$MiTemplate->set_var("nom_local",$nom_local);
				$MiTemplate->set_var("r_proycotizados",formato_precio($r_proycotizados));
				$MiTemplate->set_var("r_proyvendidos",formato_precio($r_proyvendidos));
				$MiTemplate->set_var("r_proyterminados",formato_precio($r_proyterminados));
				$MiTemplate->set_var("r_cantPS",formato_precio($r_cantPS));
				$MiTemplate->set_var("r_montoPS",formato_precio($r_montoPS));
				$MiTemplate->set_var("r_cantPE",formato_precio($r_cantPE));
				$MiTemplate->set_var("r_montoPE",formato_precio($r_montoPE));
				$MiTemplate->set_var("r_cantVI",formato_precio($r_cantVI));
				$MiTemplate->set_var("r_montoVI",formato_precio($r_montoVI));
				$MiTemplate->set_var("r_cantIN",formato_precio($r_cantIN));
				$MiTemplate->set_var("r_montoIN",formato_precio($r_montoIN));
				$MiTemplate->set_var("r_total",formato_precio($r_total));
			/*volver a cero las variables*/
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
				$MiTemplate->set_var("ini_normal",'<!--');
				$MiTemplate->set_var("fin_normal",'-->');
				$MiTemplate->parse("blo_DI", "detalle_dis", true);
			}
		}

				$MiTemplate->set_var("t_proycotizados",formato_precio($t_proycotizados));
				$MiTemplate->set_var("t_proyvendidos",formato_precio($t_proyvendidos));
				$MiTemplate->set_var("t_proyterminados",formato_precio($t_proyterminados));
				$MiTemplate->set_var("t_cantPS",formato_precio($t_cantPS));
				$MiTemplate->set_var("t_montoPS",formato_precio($t_montoPS));
				$MiTemplate->set_var("t_cantPE",formato_precio($t_cantPE));
				$MiTemplate->set_var("t_montoPE",formato_precio($t_montoPE));
				$MiTemplate->set_var("t_cantVI",formato_precio($t_cantVI));
				$MiTemplate->set_var("t_montoVI",formato_precio($t_montoVI));
				$MiTemplate->set_var("t_cantIN",formato_precio($t_cantIN));
				$MiTemplate->set_var("t_montoIN",formato_precio($t_montoIN));
				$MiTemplate->set_var("t_total",formato_precio($t_total));
}
/*para la impresion en excel */
if ($accion=='Exportar'){
    exportar_excel($queryexcel_ini ,$queryexcel_det);
}
function exportar_excel($queryini,$querydet) {
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

	$vcstr = "Local\tDiseñador\tProyectos Cotizados\tProyectos Vendidos\tProyectos\tPS(Cant/Monto) Terminados\tPE(Cant/Monto)\tVI(Cant/Monto)\tIN(Cant/Monto)\tTotal PS+PE+VI+IN\n";
		
	$rq = tep_db_query($queryini);
	$rq1 = tep_db_query($querydet);
	while( $res = tep_db_fetch_array( $rq ) ) {
		$res1 = tep_db_fetch_array( $rq1 );
		$vcstr .= $res['local'] . "\t";
		$vcstr .= $res['disenador'] . "\t";
		$t_proycotizados+=$res['proycotizados'];
		$vcstr .= $res['proycotizados'] . "\t";
		$t_proyvendidos+=$res['proyvendidos'];
		$vcstr .= $res['proyvendidos'] . "\t";
		$t_proyterminados+=$res['proyterminados'];
		$vcstr .= $res['proyterminados'] . "\t";

		$t_cantPS+=$res1['cantPS'];
		$t_montoPS+=$res1['montoPS'];
		$vcstr .= formato_precio($res1['cantPS']) . " / ". formato_precio($res1['montoPS']) . "\t";
		$t_cantPE+=$res1['cantPE'];
		$t_montoPE+=$res1['montoPE'];
		$vcstr .= formato_precio($res1['cantPE']) . " / ". formato_precio($res1['montoPE']) . "\t";
		$t_cantVI+=$res1['cantVI'];
		$t_montoVI+=+$res1['montoVI'];
		$vcstr .= formato_precio($res1['cantVI']) . " / ". formato_precio($res1['montoVI']) . "\t";
		$t_cantIN+=+$res1['cantIN'];
		$t_montoIN+=$res1['montoIN'];
		$vcstr .= formato_precio($res1['cantIN']) . " / ". formato_precio($res1['montoIN']) . "\t";
		$t_total+=$res1['total'];
		$vcstr .= formato_precio($res1['total']) . "\t";
		$vcstr .= "\n";
	}
$vacio=" ";
	$vcstr .= $vacio."\tTotales\t".$t_proycotizados."\t".$t_proyvendidos."\t".$t_proyterminados."\t".formato_precio($t_cantPS)."/".formato_precio($t_montoPS)."\t".formato_precio($t_cantPE)."/".formato_precio($t_montoPE)."\t".formato_precio($t_cantVI)."/".formato_precio($t_montoVI)."\t".formato_precio($t_cantIN)."/".formato_precio($t_montoIN)."\t".formato_precio($t_total)."\n";

	$fname = 'VentaDisenador_'.date("YmdHis").'.xls';
	header("Content-Type: application/vnd.ms-excel");
	header("Content-Length: ".strlen($vcstr));
	header("Content-Disposition: attachment; filename=".$fname);
	echo $vcstr;
	tep_exit();
}

/****************************/
if ($accion!='Imprimir'){
    // Agregamos el footer
    include "../../includes/footer_cproy.php";

    $MiTemplate->pparse("OUT_H", array("header"), false);
    include "../../menu/menu.php";
    $MiTemplate->parse("OUT_M", array("main","footer"), true);
    $MiTemplate->p("OUT_M");
	include "../../includes/application_bottom.php";
}else{    
	$MiTemplate->parse("OUT_M", array("body"), true);
    $MiTemplate->p("OUT_M");
	include "../../includes/application_bottom.php";
}
?>