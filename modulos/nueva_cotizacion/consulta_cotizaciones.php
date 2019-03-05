<?php
$pag_ini = '../nueva_cotizacion/consulta_cotizaciones.php';
include ("../../includes/aplication_top.php");
include_idioma_mod($ses_idioma, "nueva_cotizacion_00");
global $ses_usr_id;
//***Insertar nueva fecha de entrega en la Tabla fecha_entrega ***

$MiTemplate = new Template();
// asignamos debug maximo
$MiTemplate->debug = 0;
// root directory de los templates
$MiTemplate->set_root(DIRTEMPLATES);
// variables perdidas
$MiTemplate->set_unknowns("remove");

$MiTemplate->set_file("main","nueva_cotizacion/consulta_cotizaciones.htm");


$MiTemplate->set_var("fecha_inicio", $fecha_inicio);
$MiTemplate->set_var("fecha_fin", $fecha_fin);

$MiTemplate->set_var("fecha_inicio2", $fecha_inicio2);
$MiTemplate->set_var("fecha_fin2", $fecha_fin2);


	// Inicio la recuperacion de los estados
	$MiTemplate->set_block("main", "estado", "BLO_estado");			
	if($estado == '')	
	$query = "SELECT id_estado,  esta_nombre  FROM estados WHERE esta_tipo = 'TP' ";
	else
	$query = "SELECT id_estado,  esta_nombre,  case WHEN id_estado = '$estado' then 'selected' else  '' end selected  FROM estados WHERE esta_tipo = 'TP' ";			
		
	query_to_set_var($query, $MiTemplate, 1, 'BLO_estado', 'estado');
	//Fin de la recuperacion
	
	// inicio la recuperacion de los Tipos tipos de Orden de Trabajo
	$MiTemplate->set_block("main", "tipo_ot", "BLO_tipo_ot");
	if($tipo_ot == '')			
	$query = "SELECT  DISTINCT  prod_tipo  FROM  tipo_subtipo ";
	else
	$query = "SELECT  DISTINCT prod_tipo,  case WHEN prod_tipo = '$tipo_ot'  then 'selected' else '' end selected   FROM tipo_subtipo ";
					
	query_to_set_var($query, $MiTemplate, 1, 'BLO_tipo_ot', 'tipo_ot');
	// Fin de la recuperacion
	
	// inicio la recuperacion de la Categoria de los Clientes
	$MiTemplate->set_block("main", "cat_cliente", "BLO_cat_cliente");
	if($cat_cliente == '')		
	$query = "SELECT id_categoria, nombre_categoria  FROM  tipo_categoria_cliente";
	else
	$query = "SELECT id_categoria, nombre_categoria,  case WHEN id_categoria = $cat_cliente then 'selected' else '' end selected FROM  tipo_categoria_cliente ;";
					
	query_to_set_var($query, $MiTemplate, 1, 'BLO_cat_cliente', 'cat_cliente');
	// Fin de la recuperacion
	
	// Inicio la recuperacion de los Tipos de Despacho
	$MiTemplate->set_block("main", "tipo_entrega", "BLO_entrega");
	if($tipo_entrega == '')			
	$query = "SELECT id_tipodespacho, nombre  FROM tipos_despacho ";
	else
	$query = "SELECT id_tipodespacho, nombre, case WHEN id_tipodespacho = $tipo_entrega  then 'selected' else '' end selected   FROM tipos_despacho ";
					
	query_to_set_var($query, $MiTemplate, 1, 'BLO_entrega', 'tipo_entrega');
	// Fin de la  recuperacion
	
	// Inicio. Recuperamos informacion de la tabla CentroDespacho   para llenar combo_box Centro de Despacho
    	$MiTemplate->set_block("main", "Bloque_centro_despacho", "PBL_Bloque_centro_despacho");
	$query = "Select  id_local, if(id_local= $MiTemplate->usr_id_cd+0, 'selected', '') As selected_local,  nom_local AS nombre_local From locales Order By nom_local";
    	query_to_set_var( $query, $MiTemplate, 1, 'PBL_Bloque_centro_despacho', 'Bloque_centro_despacho' );
	//Fin de la recuperacion de Datos.
	
	
	$estado_ot = " ";
	if($estado != 'TT')
		if($estado == 'PD')
			$estado_ot = " AND  OT.id_estado IN ('$estado','ED') ";	
		else
			$estado_ot = " AND  OT.id_estado = '$estado' ";	
	
	$entrega_od = " ";
	if($tipo_entrega)
	$entrega_od = " AND  OD.id_tipodespacho = '$tipo_entrega' ";	
	
	$fecha_entrega = " ";
	if($fecha_inicio && $fecha_fin)
	{			
		$fecha_entrega = " AND OD.osde_fecha_entrega>='".$fecha_inicio."' AND OD.osde_fecha_entrega<='".$fecha_fin."' ";		
	}
	
	$fecha_pago = " ";
	if($fecha_inicio2 && $fecha_fin2)
	{		
		$fecha_inicio2 = invierte_fechaGuion($fecha_inicio2);
		$fecha_fin2 = invierte_fechaGuion($fecha_fin2);		
		$fecha_pago = " AND O.os_fechaboleta>='".$fecha_inicio2." 00:00:00' AND O.os_fechaboleta<='".$fecha_fin2." 23:59:59' ";		
	}
	
	$tipo_despacho = " ";
	if($tipo_ot != 'PP')
	$tipo_despacho = " AND  OT.ot_tipo = '$tipo_ot' ";	
	
	
	$filtro_os = "";
	if($os != '')
	{
	$filtro_os = " AND  OS.id_os = '$os' ";	
	$MiTemplate->set_var("os", $os);
	}
	
	$filtro_ot = "";
	if($ot != '')
	{
	$filtro_ot = " AND  OT.ot_id = '$ot' ";	
	$MiTemplate->set_var("ot", $ot);
	}
	
	$filtro_catCliente = "";
	if($cat_cliente != '')
	{
	$filtro_catCliente = " AND  CL.clie_categoria_cliente = '$cat_cliente' ";	
	$MiTemplate->set_var("cat_cliente", $cat_cliente);
	}
	$MiTemplate->set_block("main", "consulta", "BLO_consulta");
	
	$query = "SELECT DISTINCT OT.ot_id, OT.ot_tipo,  US.USR_NOMBRES,  CL.clie_telefonocasa,  US.USR_APELLIDOS,   CL.clie_nombre, CL.clie_paterno, CL.clie_materno,  TD.nombre,  O.id_os,  E.esta_nombre,  OD.osde_fecha_entrega,  DATE_FORMAT( O.os_fechaboleta, '%d/%m/%Y') AS os_fechaboleta,   
			(SELECT SUM( if (osde_descuento<>0,ROUND((osde_precio-osde_descuento)*osde_cantidad), ROUND(osde_precio*osde_cantidad))) from os_detalle where id_os = O.id_os ) AS Total   
			FROM   os O 
			 JOIN os_detalle OD  ON O.id_os = OD.id_os
			 JOIN ot OT  ON OT.ot_id = OD.ot_id
			 JOIN tipos_despacho TD ON OD.id_tipodespacho = TD.id_tipodespacho
			 JOIN estados E ON E.id_estado = OT.id_estado
			 JOIN os OS ON OS.id_os = OD.id_os
			 JOIN  clientes CL ON  CL.clie_rut = OS.clie_rut 
			 JOIN usuarios US ON US.USR_ID = OS.USR_ID
			 
			WHERE 1
			AND  osde_tipoprod IN ('PS', 'PE')  
			$estado_ot 
			$entrega_od
			$fecha_entrega	
			$fecha_pago
			$tipo_despacho	
			$filtro_os 	
			$filtro_ot 	 
			$filtro_catCliente
			".(($idCentroDespacho)?" AND O.id_local ="."'".$idCentroDespacho."'":"AND O.id_local = '".$idlocal_act."'")." 
			ORDER BY OD.osde_fecha_entrega, id_os ";	
				
	$result = tep_db_query($query);
	while( $res = tep_db_fetch_array( $result ) ) {
		$MiTemplate->set_var("clie_nombre", ($res['clie_nombre'])?$res['clie_nombre']:"&nbsp;");
		$MiTemplate->set_var("clie_paterno", ($res['clie_paterno'])?$res['clie_paterno']:"&nbsp;");
		$MiTemplate->set_var("clie_materno", ($res['clie_materno'])?$res['clie_materno']:"&nbsp;");
		$MiTemplate->set_var("clie_telefonocasa", ($res['clie_telefonocasa'])?$res['clie_telefonocasa']:"&nbsp;");
		$MiTemplate->set_var("nombre", ($res['nombre'])?$res['nombre']:"&nbsp;");
		$MiTemplate->set_var("USR_NOMBRES", ($res['USR_NOMBRES'])?$res['USR_NOMBRES']:"&nbsp;");
		$MiTemplate->set_var("USR_APELLIDOS", ($res['USR_APELLIDOS'])?$res['USR_APELLIDOS']:"&nbsp;");
		$MiTemplate->set_var("ot_tipo", ($res['ot_tipo'])?$res['ot_tipo']:"&nbsp;");
		$MiTemplate->set_var("id_os", ($res['id_os'])?$res['id_os']:"&nbsp;");
		$MiTemplate->set_var("ot_id", ($res['ot_id'])?$res['ot_id']:"&nbsp;");
		$MiTemplate->set_var("esta_nombre", ($res['esta_nombre'])?$res['esta_nombre']:"&nbsp;");
		$MiTemplate->set_var("osde_fecha_entrega", ($res['osde_fecha_entrega'])?$res['osde_fecha_entrega']:"&nbsp;");
		$MiTemplate->set_var("os_fechaboleta", ($res['os_fechaboleta'])?$res['os_fechaboleta']:"&nbsp;");
		$MiTemplate->set_var("Total", ($res['Total'])?formato_precio($res['Total']):"&nbsp;");
		$MiTemplate->parse('BLO_consulta', 'consulta', true );
	}
	

		
/*para la impresion en excel */
if ($accion=='Exportar'){
    exportar_excel($query);
}
	
function exportar_excel($queryini) {
	$t_MontoCotizacion = 0;	
	
	$vcstr = "Nombre Cliente\tTelefono\tTipo Despacho\tNombre Vendedor\tID_OT\tOTtipo\tID_OS\tEstado\tFecha_Entrega\tFecha_Pago\tTotal\n";
	$rq = tep_db_query($queryini);
	$rq1 = tep_db_query($querydet);	
	
	while( $res = tep_db_fetch_array( $rq ) ) {
		$res1 = tep_db_fetch_array( $rq1 );		
		$vcstr .= $res['clie_nombre'] . " " .  $res['clie_paterno'] . " " . $res['clie_materno'] . "\t";
		$vcstr .= $res['clie_telefonocasa'] . "\t";
		$vcstr .= $res['nombre'] . "\t";		
		$vcstr .= $res['USR_NOMBRES'] . " " . $res['USR_APELLIDOS'] .  "\t";		
		$vcstr .= $res['ot_id'] . "\t";
		$vcstr .= $res['ot_tipo'] . "\t";		
		$vcstr .= $res['id_os'] . "\t";				
		$vcstr .= $res['esta_nombre'] . "\t";
		$vcstr .= " " . $res['osde_fecha_entrega'] . " " . "\t";		
		$vcstr .= " " . $res['os_fechaboleta'] . " " . "\t";				
		$vcstr .= " " . $res['Total'] . " " . "\n";
	}

	$fname = 'Consuta_cotizaciones_'.date("YmdHis").'.xls';
	header("Content-Type: application/vnd.ms-excel");
	header("Content-Length: ".strlen($vcstr));
	header("Content-Disposition: attachment; filename=".$fname);
	echo $vcstr;
	tep_exit();
}
    
$MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . PAGETITLE);
$MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);
$MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));

$MiTemplate->set_file("header","header_ident.html");

// Agregamos el header
$MiTemplate->set_file("header","header_ident.html");

// Agregamos el main
$MiTemplate->set_file("main","../templates/nueva_cotizacion/consulta_cotizaciones.htm");

// Agregamos el footer
include "../../includes/footer_cproy.php";

$MiTemplate->pparse("OUT_H", array("header"), true);
include "../../menu/menu.php";
$MiTemplate->parse("OUT_M", array("main","footer"), true);
$MiTemplate->p("OUT_M");
include "../../includes/application_bottom.php";

?>

