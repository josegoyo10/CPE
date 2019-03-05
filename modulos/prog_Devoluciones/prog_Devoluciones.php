<?
$pag_ini = '../prog_Devoluciones/prog_Devoluciones.php';
include "../../includes/aplication_top.php";
include_idioma_mod( $ses_idioma, "sp" );

/****************************************************************/
/****************************************************************
 *
 * Despliega Listado Búsqueda
 *
 ****************************************************************/
global $ses_usr_id;   
	   $fecha_hoy = date('Y-m-d');
	   
	$MiTemplate = new Template();
    // asignamos degug maximo
    $MiTemplate->debug = 0;
    // root directory de los templates
    $MiTemplate->set_root(DIRTEMPLATES);
    // variables perdidas
    $MiTemplate->set_unknowns("remove");
    
    
    // Agregamos el main
    $MiTemplate->set_file("main","prog_Devoluciones/prog_Devoluciones.htm");   
    
    // Envio el número de dias establecidos para el rango de consultas.
    $MiTemplate->set_var("rango_cons", MAX_DIAS_CONSUL);

    // Inicio. Recuperamos informacion del local asociado al usuario logeado en el Sistema
	$query = "select ls.nom_local, ls.id_local from local_usr lu join locales ls on ls.id_local=lu.id_local where USR_ID =".$ses_usr_id;
	$result = tep_db_query($query);
	$res = tep_db_fetch_array( $result );
	$MiTemplate->set_var("idlocal_act", $res['id_local']);
	$MiTemplate->set_var("nomlocal_act", $res['nom_local']);
	$idlocal_act = $res['id_local'];
	//Fin de la recuperacion de Datos.

    // Inicio. Recuperamos informacion de la tabla CentroDespacho   para llenar combo_box Centro de Despacho
    $MiTemplate->set_block("main", "Bloque_centro_despacho", "PBL_Bloque_centro_despacho");
	$query = "Select  id_local, if(id_local= $MiTemplate->usr_id_cd+0, 'selected', '') As selected_local,  nom_local AS nombre_local From locales Order By nom_local";
    query_to_set_var( $query, $MiTemplate, 1, 'PBL_Bloque_centro_despacho', 'Bloque_centro_despacho' );
	//Fin de la recuperacion de Datos.
	
    // Inicio. Recuperamos informacion de la tabla Locales  para llenar el Encabezado de la tabla
	$query = "Select nom_local From locales Where id_local=".$idCentroDespacho;
	$rq = tep_db_query($query );
	$res = tep_db_fetch_array( $rq );
	$nom_local = $res['nom_local'];
	//Fin de la recuperacion de Datos.
	
if($accion == 'update'){
    // Escribe el encabezado de la Tabla según los criterios de Consulta establecidos
    	if (!$fecha_desde && !$fecha_hasta && $idCentroDespacho)
					$glosa = "Viajes a Despachar  para el Centro de Despacho $nom_local";
				elseif($fecha_desde && !$fecha_hasta && $idCentroDespacho)
					$glosa = "Viajes a Despachar desde el $fecha_desde para el Centro de Despacho $nom_local";
				elseif(!$fecha_desde && $fecha_hasta && $idCentroDespacho)
					$glosa = "Viajes a Despachar hasta el $fecha_hasta para el Centro de Despacho $nom_local";
				elseif($fecha_desde && $fecha_hasta && $idCentroDespacho)
					$glosa = "Viajes a Despachar entre el $fecha_desde y el $fecha_hasta para el Centro de Despacho $nom_local";
				elseif(!$idCentroDespacho && !$fecha_hasta && $fecha_desde)
					$glosa = "Viajes a Despachar para todos los Centros de Despacho desde el $fecha_desde";
				elseif(!$idCentroDespacho && !$fecha_desde && $fecha_hasta)
					$glosa = "Viajes a Despachar para todos los Centros de Despacho hasta el $fecha_hasta";
				elseif(!$idCentroDespacho && $fecha_desde && $fecha_hasta)
					$glosa = "Viajes a Despachar para todos los Centros de Despacho desde el $fecha_desde hasta el $fecha_hasta";
				elseif(!$idCentroDespacho && !$fecha_desde && !$fecha_hasta)
					$glosa = "Viajes a Despachar para todos los Centros de Despacho";
    	$MiTemplate->set_var("Encabezado", $glosa);  
    

    //	Se inicializa el bloque en el que se presentaran los resultados
	$MiTemplate->set_block("main", "Bloque_Resultados", "PBLResultados");

	
	// Se realizan las consultas a la Base de Datos segun los criterios de Consulta establecidos.
	// Se realiza consulta de Conteo de registros y consulta de informacion.
	// Query por defecto con Valores: Fecha Hoy.
		$queryCount="Select count(*)AS Cont 
						FROM ot OT
						JOIN os OS ON OS.id_os=OT.id_os
						JOIN clientes CL ON CL.clie_rut=OS.clie_rut
						JOIN os_detalle OD ON OD.id_os=OT.id_os
						JOIN tipos_despacho TP ON TP.id_tipodespacho=OD.id_tipodespacho
						WHERE OT.id_estado in ('EN','PN','VN')
						".(($fecha_desde)?" AND OT.ot_fechacreacion >="."'".invierte_fechaGuion($fecha_desde)." 00:00:00'":"AND OT.ot_fechacreacion >='".$fecha_hoy." 00:00:00'")."
						".(($fecha_hasta)?" AND OT.ot_fechacreacion <="."'".invierte_fechaGuion($fecha_hasta)." 23:59:59'":"AND OT.ot_fechacreacion <='".$fecha_hoy." 23:59:59'")."
						".(($idCentroDespacho)?" AND OS.id_local ="."'".$idCentroDespacho."'":"AND OS.id_local = '".$idlocal_act."'")." 
						".(($id_OS)?" AND OS.id_os ="."'".$id_OS."'":"")."
						".(($rut)?" AND OS.clie_rut ="."'".$rut."'":"")."
						";								 
		$queryRES = "SELECT DISTINCT OT.id_os, OT.ot_id, OT.ot_tipo, ES.esta_nombre, OS.os_fechaboleta, UPPER(CONCAT(CL.clie_nombre,' ',CL.clie_paterno,' ',CL.clie_materno)) AS cliente,
						OD.osde_descripcion, TP.nombre, OD.osde_cantidad
						FROM ot OT
						JOIN os OS ON OS.id_os=OT.id_os
						JOIN clientes CL ON CL.clie_rut=OS.clie_rut
						JOIN os_detalle OD ON OD.id_os=OT.id_os
						JOIN tipos_despacho TP ON TP.id_tipodespacho=OD.id_tipodespacho
						JOIN estados ES ON ES.id_estado=OT.id_estado
						WHERE OT.id_estado in ('EN','PN','VN')
						".(($fecha_desde)?" AND OT.ot_fechacreacion >="."'".invierte_fechaGuion($fecha_desde)." 00:00:00'":"AND OT.ot_fechacreacion >='".$fecha_hoy." 00:00:00'")."
						".(($fecha_hasta)?" AND OT.ot_fechacreacion <="."'".invierte_fechaGuion($fecha_hasta)." 23:59:59'":"AND OT.ot_fechacreacion <='".$fecha_hoy." 23:59:59'")."
						".(($idCentroDespacho)?" AND OS.id_local ="."'".$idCentroDespacho."'":"AND OS.id_local = '".$idlocal_act."'")." 
						".(($id_OS)?" AND OS.id_os ="."'".$id_OS."'":"")."
						".(($rut)?" AND OS.clie_rut ="."'".$rut."'":"")."
						ORDER BY OS.os_fechaboleta";

				$result = tep_db_query($queryCount);
				$res = tep_db_fetch_array( $result );
				$tot_reg = $res['Cont'];
					if($tot_reg == 0)
			      			$MiTemplate->set_var("alert","<script language=\"JavaScript\">alert(\"No existen Devoluciones para los Filtros Seleccionados. \");</script>");		
	
					
				$result = tep_db_query($queryRES);
				while( $res = tep_db_fetch_array( $result ) ) {
				$cont++;
				$MiTemplate->set_var("cont", $cont);
				$MiTemplate->set_var("id_os", ($res['id_os'])?$res['id_os']:"&nbsp;");
				$MiTemplate->set_var("id_ot", ($res['ot_id'])?$res['ot_id']:"&nbsp;");
				$MiTemplate->set_var("estado", ($res['esta_nombre'])?$res['esta_nombre']:"&nbsp;");
				$MiTemplate->set_var("fecha_Pago", ($res['os_fechaboleta'])?$res['os_fechaboleta']:"No Paga");
				$MiTemplate->set_var("cliente", ($res['cliente'])?$res['cliente']:"&nbsp;");
				$MiTemplate->set_var("producto", ($res['osde_descripcion'])?$res['osde_descripcion']:"&nbsp;");
				$MiTemplate->set_var("tip_producto", ($res['nombre'])?$res['nombre']:"&nbsp;");
				$MiTemplate->set_var("tip_compra", ($res['ot_tipo'])?$res['ot_tipo']:"&nbsp;");
				$MiTemplate->set_var("cantidad", ($res['osde_cantidad'])?$res['osde_cantidad']:"&nbsp;");

				$MiTemplate->parse("PBLResultados", "Bloque_Resultados", true);
				$MiTemplate->set_var("Export","<INPUT type=\"button\" value=\"Exportar a Excel\" id=\"button1\" name=\"button1\" onCLick=\"exportToXL(viaje.all('idTable'))\"/>");
		}
}
	//$MiTemplate->set_var("prueba",$queryRES);
		
	// Agregamos el header
    $MiTemplate->set_file("header","header_ident.html");  

    // Agregamos el footer
	include "../../includes/footer_cproy.php";

    $MiTemplate->pparse("OUT_H", array("header"), false);
    include "../../menu/menu.php";
    $MiTemplate->parse("OUT_M", array("main","footer"), true);
    $MiTemplate->p("OUT_M");


/**********************************************************************************************/

include "../../includes/application_bottom.php";

?>