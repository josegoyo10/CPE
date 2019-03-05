<?
$pag_ini = '../consulta_precios/index.php';
include_once('../../includes/aplication_top.php');

//include_idioma_mod( $ses_idioma, "sp" );

/****************************************************************
 *
 * Despliega Listado Reporte Pagos Manuales
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
	

	$MiTemplate->set_var("fecha_inicio",$fecha_inicio);
	if($fecha_inicio){
		$aux=$fecha_inicio;
		$a = split("/", $aux);
		$fecha_ini_qry=$a[2]."-".$a[1]."-".$a[0]." "."00:00:00";
		$aux_fi="'".$fecha_ini_qry."'";
	}
	
	$MiTemplate->set_var("fecha_termino",$fecha_termino);
	if($fecha_termino){
		$auxT=$fecha_termino;
		$aT = split("/", $auxT);
		$fecha_ter_qry=$aT[2]."-".$aT[1]."-".$aT[0]." "."23:59:59";
		$aux_ft="'".$fecha_ter_qry."'";
	}
	
	

    // Agregamos el header
    $MiTemplate->set_file("header","header_ident.html");

   // Agregamos el main
    $MiTemplate->set_file("main","consulta_precios/ReportePagosManuales.htm");
	//Recuperamos los Locales
      
  
	/*Saca el origen del usuario*/
	$Ori_Gen=get_login_origen($ses_usr_id);
	if ($Ori_Gen){
		$aux_ori= "and USR_ORIGEN=".$Ori_Gen;
	}else{
		$aux_ori= " ";
	}

file_put_contents('accionReporte.txt', $accion);

/*accion ver o imrpimir el  reporte*/
if (($accion=='Exportar')){

file_put_contents('accionReporte01.txt', "entro");


    $queryPro ="SELECT o.id_os, 
                  o.id_proyecto, 
                  o.id_local, 
                  o.clie_rut, 
                  o.os_fechacreacion, 
                  o.os_comentarios, 
                  o.usuario, 
                  o.USR_ID, 
                  o.USR_ORIGEN,
				  o.os_fechaboleta, 
				  o.os_numboleta, 
				  o.os_codbarras, 
				  o.os_terminal_pos, 
				  h.id_historial, 
				  h.id_os as id_os2, 
				  h.hist_usuario, 
				  h.hist_descripcion,
				  l.cod_local, 
				  l.nom_local, 
				  l.dir_local, 
				  l.ciu_local, 
				  osde.osde_tipoprod, 
				  osde.osde_subtipoprod, 
				  osde.osde_precio, 
				  osde.osde_cantidad,
                  osde.osde_descuento, 
                  osde.osde_preciocosto, 
                  osde.cod_sap, 
                  osde.osde_descripcion, 
                  osde.id_producto, 
                  osde.cod_barra, 
                  osde.cant_pickeada,
				 osde.osde_fecha_entrega 
		FROM os o JOIN historial h ON o.id_os = h.id_os
		JOIN locales l ON l.id_local = o.id_local
		JOIN os_detalle osde ON o.id_os = osde.id_os
		WHERE hist_descripcion LIKE '%Pagada%'
		and os_fechacreacion between $aux_fi and $aux_ft";

}
/*****************************************para exportar a excel***********************/
if ($accion=='Exportar'){
	file_put_contents('accionexporarExcel.txt', "exportar");
    exportar_excel($queryPro);
}
function exportar_excel($query) {

  file_put_contents('accionqueryExcel.txt',$query);


	$vcstr = "Nro\tID OS\tID PROYECTO\tID LOCAL\tRUT CLIENTE\tFecha Creación OS\tComentarios OS\tUsuario\tFECHA BOLETA OS\tNUM BOLETA OS\tCODIGO BARRAS OS\tHISTORIAL USUARIO\tHISTORIAL DESCRIPCION\tCODIGO LOCAL\tNOMBRE LOCAL\tCIUDAD LOCAL\tPRECIO OS\tCANTIDAD OS\tDESCUENTO OS\tPRECIO COSTO OS\tCODIGO SAP\tDESCRIPCION OS\tCOD BARRA\t CANTIDAD PICKEADA\tFECHA ENTREGA OS\n";
	$j=0;
	$rq = tep_db_query($query);
     file_put_contents('queryExcel.txt',print_r($rq,true));



	while($res = tep_db_fetch_array($rq)) {

       file_put_contents('resExcel.txt',print_r($res,true));

	$j++;
		$vcstr .= $j . "\t";
		$vcstr .= $res['id_os']."\t";
		$vcstr .= $res['id_proyecto'] . "\t";
		$vcstr .= $res['id_local'] . "\t";
		$vcstr .= $res['clie_rut'] . "\t";
		$vcstr .= $res['os_fechacreacion']."\t";
		$vcstr .= $res['os_comentarios'] ."\t";
		$vcstr .= $res['usuario']."\t";
		$vcstr .= $res['os_fechaboleta']."\t";
        $vcstr .= $res['os_numboleta']."\t";
        $vcstr .= $res['os_codbarras']."\t";
        $vcstr .= $res['hist_usuario']."\t";
        $vcstr .= $res['hist_descripcion']."\t";
        $vcstr .= $res['cod_local']."\t";
        $vcstr .= $res['nom_local']."\t";
        $vcstr .= $res['ciu_local']."\t";
        $vcstr .= $res['osde_precio']."\t";
        $vcstr .= $res['osde_cantidad']."\t";
        $vcstr .= $res['osde_descuento']."\t";
        $vcstr .= $res['osde_preciocosto']."\t";
        $vcstr .= $res['cod_sap']."\t";
        $vcstr .= $res['osde_descripcion']."\t";
        $vcstr .= $res['cod_barra']."\t";
        $vcstr .= $res['cant_pickeada']."\t";
        $vcstr .= $res['osde_fecha_entrega']."\t";
		$vcstr .= "\n";
	}
 $vacio=" ";
 	$vcstr .= $vacio. "\t".$vacio. "\t".$vacio. "\t".$vacio."\n";

	$fname = 'PagosManuales'.date("YmdHis").'.xls';

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