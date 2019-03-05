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
    $MiTemplate->set_file("main","consulta_precios/ReporteBoleta.htm");

	/*Saca el origen del usuario*/
	$Ori_Gen=get_login_origen($ses_usr_id);
	if ($Ori_Gen){
		$aux_ori= "and os.USR_ORIGEN=".$Ori_Gen;
	}else{
		$aux_ori= " ";
	}
	if ($numero_boleta){
		$aux_boleta=" and os_numboleta=".$numero_boleta;
		$MiTemplate->set_var("numero_boleta",$numero_boleta);
	}else{
		$aux_boleta=" ";
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

	//Recuperamos los Locales
    $MiTemplate->set_block("main", "Locales", "BLO_Loc");
	$queryL="SELECT os.id_local as id_local, os.nom_local as nom_local, if('$select_local'=os.id_local, 'selected', '') 'selected' 
			FROM locales os WHERE 1
			ORDER BY nom_local";
	query_to_set_var( $queryL, $MiTemplate, 1, 'BLO_Loc', 'Locales' );   

if (($accion=='VerReporte')||($accion=='Imprimir')||($accion=='Exportar')){
	$query_diproy="select l.nom_local local,concat(u.USR_NOMBRES, ' ', u.USR_APELLIDOS) disenador,  os.id_os as NumCotizacion,os.os_descripcion as Descripcion,os.os_numboleta as NumeroBoleta, date_format(os.os_fechaboleta, '%d/%m/%Y') as FechaBoleta,l.id_local,
		sum(round((if(osde_precio is not null, osde_precio, 0)-if(osde_descuento is not null, osde_descuento, 0))*if(os.id_estado in ('SP', 'SM'), osde_cantidad, 0))) 'MontoCotizacion'
		from os os join os_detalle osd on (os.id_os = osd.id_os)
		join locales l on l.id_local = os.id_local
		join usuarios u on u.USR_ID = os.USR_ID
		where osde_cantidad>0
		$aux_local
		$aux_fi
		$aux_ft
		$aux_ori
		$aux_boleta
		and osde_precio is not null
		and os.id_estado in ('SP', 'SM')
		group by 1,2,3,4,5
		order by 1 asc";
			/*primera parte para excel*/
			$queryexcel_ini=$query_diproy;

			$j=0;
			$rq = tep_db_query($query_diproy); 
			if ( tep_db_fetch_array( $rq ) ) {
				$MiTemplate->set_file("body","consulta_precios/ReporteBoleta_Detalle.htm");	
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
	$t_MontoCotizacion=0;
	$t_total=0;

	$id_local=0;
	$primero=true;

	$r_MontoCotizacion=0;
	$r_total=0;
	$MiTemplate->set_block("body", "detalle_dis", "blo_DI");
	$MiTemplate->set_var("resumen_local",'<!--');
	$MiTemplate->set_var("fin_resumen",'-->');
		if ($rq = tep_db_query($query_diproy) ){
			while( $res = tep_db_fetch_array( $rq ) ) {
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
					$MiTemplate->set_var("r_MontoCotizacion",formato_precio($r_MontoCotizacion));
					$MiTemplate->set_var("r_total",formato_precio($r_total));
					/* fin parte productos*/
				/*volver a cero las variables*/
					$r_MontoCotizacion=0;
					$r_total=0;
				}


				$id_local=$res['id_local'];
				$primero=false;								

				/*asignacion de variables*/
				$r_MontoCotizacion+=$res['MontoCotizacion'];
				$r_total+=$res1['total'];
				$MiTemplate->set_var("local",$res['local']);
				$nom_local=$res['local'];
				$i++;
				$MiTemplate->set_var("i",$i);
				$nom_local=$res['local'];
				//$img="<a href='#' onClick='DetalleDiseno('opc')'><img src='../img/info.gif' width='16' height='16' border='0'></a>";
				if ($accion!='Imprimir'){
					$MiTemplate->set_var("disenador",$res['disenador'].' '.$img);
				}else{
					$MiTemplate->set_var("disenador",$res['disenador']);
				}
				$MiTemplate->set_var("id_local",$res['id_local']);
				$MiTemplate->set_var("NumCotizacion",$res['NumCotizacion']);
				$MiTemplate->set_var("Descripcion",$res['Descripcion']);
				$MiTemplate->set_var("NumeroBoleta",$res['NumeroBoleta']);
				$MiTemplate->set_var("FechaBoleta",$res['FechaBoleta']);
				$MiTemplate->set_var("MontoCotizacion",formato_precio($res['MontoCotizacion']));
				$t_MontoCotizacion+=$res['MontoCotizacion'];
				$MiTemplate->parse("blo_DI", "detalle_dis", true);
			}
			if( $id_local!=$res['id_local'] && !$primero){
			/*	desplegar_resumen($id_local);*/
				$MiTemplate->set_var("resumen_local",'');
				$MiTemplate->set_var("fin_resumen",'');
				$MiTemplate->set_var("nom_local",$nom_local);
				$MiTemplate->set_var("r_MontoCotizacion",formato_precio($r_MontoCotizacion));
			/*volver a cero las variables*/
				$r_MontoCotizacion=0;
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

				$MiTemplate->set_var("t_MontoCotizacion",formato_precio($t_MontoCotizacion));
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
    exportar_excel($queryexcel_ini);
}
function exportar_excel($queryini) {
	$t_MontoCotizacion=0;

	$vcstr = "Local\tDiseñador\tNumCotizacion\tDescripcion\tNumeroBoleta\tFechaBoleta\tMontoCotizacion\n";
	$rq = tep_db_query($queryini);
	$rq1 = tep_db_query($querydet);
	while( $res = tep_db_fetch_array( $rq ) ) {
		$res1 = tep_db_fetch_array( $rq1 );
		$vcstr .= $res['local'] . "\t";
		$vcstr .= $res['disenador'] . "\t";
		$vcstr .= $res['NumCotizacion'] . "\t";
		$vcstr .= $res['Descripcion'] . "\t";
		$vcstr .= $res['NumeroBoleta'] . "\t";
		$vcstr .= $res['FechaBoleta'] . "\t";
		$vcstr .= formato_precio($res['MontoCotizacion']) . "\t";
		$t_MontoCotizacion+=$res['MontoCotizacion'];
		$vcstr .= "\n";
	}
$vacio=" ";
	$vcstr .= $vacio."\tTotales\t".$vacio."\t".$vacio."\t".$vacio."\t".$vacio."\t".formato_precio($t_MontoCotizacion)."\n";

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