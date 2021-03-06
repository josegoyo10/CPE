<?
$pag_ini = '../consulta_precios/index.php';
include "../../includes/aplication_top.php";

include_idioma_mod( $ses_idioma, "sp" );

/**********************************************************************************************/
/****************************************************************
 *
 * Despliega Listado productos
 *
 ****************************************************************/
    global $ses_usr_id ,$queryexcel;
	$MiTemplate = new Template();
	$MiTemplate->set_var("accion",$accion);
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
    
	/*variables recibidas*/
	$MiTemplate->set_var("select_local",$select_local);
	/* si trae local se filtra , si no son Todos*/
	if ($select_local>0){
		$aux_local=" and id_local =".($select_local+0);
	}else{
		$aux_local="";	
	}
	/*para el tipo 'PS'*/
	$MiTemplate->set_var("select_tipo",$select_tipo);
	if($select_tipo){
	    $aux_tipoprod=" and osde_tipoprod ='".($select_tipo)."'";
	}
	/*separa el subtipo 'PS - Stock'*/
	$MiTemplate->set_var("select_subtipo",$select_subtipo);	
	if($select_subtipo){
		$aux=$select_subtipo;
		$b = split("-", $aux);
		$subtipo=trim($b[0]);
	}
	if($subtipo!='TODOS'){
		$aux_subtipoprod =" and osde_subtipoprod = '".($subtipo)."'";
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
		$fecha_ter_qry=$aT[2]."-".$aT[1]."-".$aT[0]." "."23:59:59";
		$aux_ft=" and os_fechacotizacion <='".$fecha_ter_qry."'";
	}
	
	$MiTemplate->set_var("select_top",$select_top);
	$MiTemplate->set_var("va",$va);
	$MiTemplate->set_var("t_top",$t_top);
	
	/*por defecto el valor de t _top*/
	$MiTemplate->set_var("checked_m",'checked');
	$valor='venta';
	$MiTemplate->set_var("checked_UPC",'checked');
	
	/*para valores de Top seleccionados*/
	if ($select_top=='10'){
		$MiTemplate->set_var("selected10",'selected');
	}
	if ($select_top=='50'){
		$MiTemplate->set_var("selected50",'selected');
	}
	if ($select_top=='200'){
		$MiTemplate->set_var("selected200",'selected');
	}
	if ($select_top=='1000'){
		$MiTemplate->set_var("selected1000",'selected');
	}

	/*para valores de select de top, van en el Roder de la consulta*/
	if ($t_top=='venta'){
		$MiTemplate->set_var("checked_m",'checked');
		$valor='venta';
	}
	if ($t_top=='numvendido'){
		$MiTemplate->set_var("checked_c",'checked');
		$valor='numvendido';
	}
	/*para valores de tipo seleccionados*/
	if ($select_tipo=='PS'){
		$MiTemplate->set_var("selected_ps",'selected');
	}
	if ($select_tipo=='PE'){
		$MiTemplate->set_var("selected_pe",'selected');
	}
	if ($select_tipo=='SV'){
		$MiTemplate->set_var("selected_sv",'selected');
	}
	if ($select_tipo=='0'){
		$MiTemplate->set_var("selected_0",'selected');
	}

	/*valor para el l�mite de la consulta*/	
	$limite="limit 0,".$select_top;

	/*para valores de radio seleccionados*/	
	if (($va=='UPC')&&($texto_dato)){
		$MiTemplate->set_var("checked_UPC",'checked');
        $aux_barra=' and cod_barra = '.$texto_dato;
		$limite=" ";
		}
	if (($va=='SAP')&&($texto_dato)){
		$MiTemplate->set_var("checked_SAP",'checked');
        $aux_sap=' and cod_sap = '.$texto_dato;
		$limite=" ";
		}
	if (($va=='DESC')&&($texto_dato)){
		$MiTemplate->set_var("checked_DESC",'checked');
        $aux_des=" and osde_descripcion like '%".$texto_dato."%'";
		}
	if ($texto_dato){
		$MiTemplate->set_var("texto_dato",$texto_dato);
	}
    // Agregamos el header
    $MiTemplate->set_file("header","header_ident.html");

   // Agregamos el main
    $MiTemplate->set_file("main","consulta_precios/ReporteVentasProducto.htm");
	//Recuperamos los Locales
    $MiTemplate->set_block("main", "Locales", "BLO_Loc");
	$queryL="SELECT os.id_local as id_local, os.nom_local as nom_local, 
			if('$select_local'=os.id_local, 'selected', '') 'selected' 
			FROM locales os
			ORDER BY nom_local";
	query_to_set_var( $queryL, $MiTemplate, 1, 'BLO_Loc', 'Locales' );   

  
	/*Saca el origen del usuario*/
	$Ori_Gen=get_login_origen($ses_usr_id);
	if ($Ori_Gen){
		$aux_ori= "and USR_ORIGEN=".$Ori_Gen;
	}else{
		$aux_ori= " ";
	}
/*accion ver o imrpimir el  reporte*/
if (($accion=='VerReporte')||($accion=='Imprimir')||($accion=='Exportar')){
$queryPro="select osd.osde_descripcion as producto, osd.cod_sap, osd.osde_tipoprod, osd.osde_subtipoprod,
            sum(round(if(os.id_estado in ('SE', 'SP', 'SM'), osd.osde_cantidad, 0) ) ) numcotizada,
            sum(round(if(os.id_estado in ('SP', 'SM'), osd.osde_cantidad, 0))) numvendido,
            sum(round((if(osd.osde_precio is not null, osd.osde_precio, 0)-if(osd.osde_descuento is not null, osd.osde_descuento, 0))*if(os.id_estado in ('SP', 'SM'), osd.osde_cantidad, 0))) 'venta'
	from os os 
       join os_detalle osd on os.id_os = osd.id_os
	where osde_cantidad>0 and osde_precio is not null and os.id_estado in ('SE', 'SP', 'SM')
		$aux_ori
		$aux_local
		$aux_tipoprod
		$aux_subtipoprod
		$aux_fi
		$aux_ft
		$aux_sap
		$aux_barra
		$aux_des
	group by 1, 2, 3, 4
	order by $valor desc
	$limite";

	$queryexcel=$queryPro;
	$rq = tep_db_query($queryPro); 
	if ( tep_db_fetch_array( $rq ) ) {
	    $MiTemplate->set_file("body","consulta_precios/ReporteVentasProducto_Detalle.htm");	
	}else{
		$MiTemplate->set_file("body","consulta_precios/ReporteVentasProducto_Msg.htm");	
	}
	$i=0;
	$MiTemplate->set_var("accion",$accion);
	$MiTemplate->set_block("body", "titulo_pro", "blo_titulo");
		$MiTemplate->set_var("top",$select_top);
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
			$MiTemplate->set_var("tamano",'720');
			$MiTemplate->set_var("inicio",'<!--');
			$MiTemplate->set_var("fin",'-->');
		}else{
			$MiTemplate->set_var("tamano",'600');
			$MiTemplate->set_var("inicio",'');
			$MiTemplate->set_var("fin",'');
		}
	$MiTemplate->parse("blo_titulo", "titulo_pro", true);

	$MiTemplate->set_block("body", "detalle_pro", "blo_DE");
		if ( $rq = tep_db_query($queryPro) ){
			while( $res = tep_db_fetch_array( $rq ) ) {
				$i++;
				$MiTemplate->set_var("i",$i);
				$MiTemplate->set_var("osde_descripcion",$res['producto']);
				$MiTemplate->set_var("cod_sap",$res['cod_sap']);
				$MiTemplate->set_var("osde_tipoprod",$res['osde_tipoprod']);
				$MiTemplate->set_var("osde_subtipoprod",$res['osde_subtipoprod']);
				$MiTemplate->set_var("numcotizada",formato_precio($res['numcotizada']));
				$MiTemplate->set_var("numvendido",formato_precio($res['numvendido']));
				$MiTemplate->set_var("venta",formato_precio($res['venta']));
				$numcotizada+=$res['numcotizada'];
				$numvendido+=$res['numvendido'];
				$ventas=$res['venta']+$ventas;
				$MiTemplate->parse("blo_DE", "detalle_pro", true);
			}
		}
	$MiTemplate->set_var("sum_venta",formato_precio($ventas));

			$MiTemplate->set_var("snumcotizada",formato_precio($numcotizada));
			$MiTemplate->set_var("snumvendido",formato_precio($numvendido));
}
/*****************************************para exportar a excel***********************/
if ($accion=='Exportar'){
    exportar_excel($queryexcel );
}
function exportar_excel($query) {

	$vcstr = "Rank\tProducto\tUPC\tTipo\tSubTipo\tUnidades Cotizadas\tUnidades Vendidas\tMonto Vendido\n";
	$j=0;
	$rq = tep_db_query($query);
	while( $res = tep_db_fetch_array( $rq ) ) {
	$j++;
		$vcstr .= $j . "\t";
		$vcstr .= $res['producto']."\t";
		$vcstr .= $res['cod_sap'] . "\t";
		$vcstr .= $res['osde_tipoprod'] . "\t";
		$vcstr .= $res['osde_subtipoprod'] . "\t";
		$vcstr .= $res['numcotizada']."\t";
		$vcstr .= $res['numvendido'] ."\t";
		$vcstr .= $res['venta']."\t";
		$ventas=$res['venta']+$ventas;
		$numcotizada+=$res['numcotizada'];
		$numvendido+=$res['numvendido'];
		$vcstr .= "\n";
	}
$vacio=" ";
	$vcstr .= $vacio. "\t".$vacio. "\t".$vacio. "\t".$vacio."\tTotales".$vacio. "\t".$numcotizada."\t".$numvendido."\t".$ventas."\n";

	$fname = 'VentaProducto_'.date("YmdHis").'.xls';

	header("Content-Type: application/vnd.ms-excel");
	header("Content-Length: ".strlen($vcstr));
	header("Content-Disposition: attachment; filename=".$fname);
	echo $vcstr;
	tep_exit();

}

/*************************************************************************************/
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