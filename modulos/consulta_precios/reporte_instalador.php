<?
$pag_ini = '../consulta_precios/reporte_instalador.php';
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
    $MiTemplate->set_file("main","consulta_precios/ReporteVentasInstalador.htm");   
    
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
	
    // Inicio. Recuperamos informacion de la tabla Instaladores   para llenar combo_box Empresa Instaladora
    $MiTemplate->set_block("main", "Bloque_instalador", "PBL_Bloque_instalador");
	$query = "Select id_instalador, CONCAT(inst_nombre,'', inst_paterno,'', inst_materno) AS nom_instalador From instaladores  Order By nom_instalador";
    query_to_set_var( $query, $MiTemplate, 1, 'PBL_Bloque_instalador', 'Bloque_instalador' );
	//Fin de la recuperacion de Datos.
	
    // Inicio. Recuperamos informacion de la tabla Locales  para llenar el Encabezado de la tabla
	$query = "Select nom_local From locales Where id_local=".$idCentroDespacho;
	$rq = tep_db_query($query );
	$res = tep_db_fetch_array( $rq );
	$nom_local = $res['nom_local'];
	//Fin de la recuperacion de Datos.

    // Recuperamos el Origen del Usuario.
	$Ori_Gen=get_login_origen($ses_usr_id);
	if ($Ori_Gen){
		$aux_ori= "and os.USR_ORIGEN=".$Ori_Gen;
	}else{
		$aux_ori= "";
	}
	
	// Inicio. Recuperamos la Categoria de los Clientes
	$MiTemplate->set_block("main", "cat_cliente", "BLO_cat_cliente");
	if($cat_cliente == '0' || $cat_cliente == '')		
		$query = "SELECT id_categoria, nombre_categoria  FROM  tipo_categoria_cliente";	
	else		
		$query = "SELECT id_categoria, nombre_categoria, case WHEN id_categoria = '$cat_cliente' then 'selected' else '' end selected  FROM  tipo_categoria_cliente";
	
	query_to_set_var($query, $MiTemplate, 1, 'BLO_cat_cliente', 'cat_cliente');
	// Fin de la recuperacion
	
if($accion){
    // Escribe el encabezado de la Tabla según los criterios de Consulta establecidos
    	if (!$fecha_desde && !$fecha_hasta && $idCentroDespacho)
					$glosa = "Ventas de Instalador para el Centro de Despacho $nom_local";
				elseif($fecha_desde && !$fecha_hasta && $idCentroDespacho)
					$glosa = "Ventas de Instalador desde el $fecha_desde para el Centro de Despacho $nom_local";
				elseif(!$fecha_desde && $fecha_hasta && $idCentroDespacho)
					$glosa = "Ventas de Instalador hasta el $fecha_hasta para el Centro de Despacho $nom_local";
				elseif($fecha_desde && $fecha_hasta && $idCentroDespacho)
					$glosa = "Ventas de Instalador entre el $fecha_desde y el $fecha_hasta para el Centro de Despacho $nom_local";
				elseif(!$idCentroDespacho && !$fecha_hasta && $fecha_desde)
					$glosa = "Ventas de Instalador para todos los Centros de Despacho desde el $fecha_desde";
				elseif(!$idCentroDespacho && !$fecha_desde && $fecha_hasta)
					$glosa = "Ventas de Instalador para todos los Centros de Despacho hasta el $fecha_hasta";
				elseif(!$idCentroDespacho && $fecha_desde && $fecha_hasta)
					$glosa = "Ventas de Instalador para todos los Centros de Despacho desde el $fecha_desde hasta el $fecha_hasta";
				elseif(!$idCentroDespacho && !$fecha_desde && !$fecha_hasta)
					$glosa = "Ventas de Instalador para todos los Centros de Despacho";
    	$MiTemplate->set_var("Encabezado", $glosa);  
    

    //	Se inicializa el bloque en el que se presentaran los resultados
	$MiTemplate->set_block("main", "Bloque_Resultados", "PBLResultados");
	
	// Se realizan las consultas a la Base de Datos segun los criterios de Consulta establecidos.
	// Se realiza consulta de Conteo de registros y consulta de informacion.
	// Query por defecto con Valores: Fecha Hoy.
		$queryCount="Select count(*)AS Cont 
						from ot ot
					join os os on ot.id_os = os.id_os
					join clientes c on c.clie_rut = os.clie_rut
					join os_detalle od on od.id_os = os.id_os
					join instaladores ins on ins.id_instalador = ot.id_instalador
					where os.id_estado = 'SP' and od.osde_subtipoprod in ('VI','IN')
					$aux_ori
					".(($fecha_desde)?" AND os.os_fechaboleta >="."'".invierte_fechaGuion($fecha_desde)." 00:00:00'":"AND os.os_fechaboleta >='".$fecha_hoy." 00:00:00'")."
					".(($fecha_hasta)?" AND os.os_fechaboleta <="."'".invierte_fechaGuion($fecha_hasta)." 23:59:59'":"AND os.os_fechaboleta <='".$fecha_hoy." 23:59:59'")."
					".(($idCentroDespacho)?" AND os.id_local ="."'".$idCentroDespacho."'":"AND os.id_local = '".$idlocal_act."'")." 
					".(($emp_inst)?" AND ot.id_instalador ="."'".$emp_inst."'":"")."
					".(($cat_cliente)?" AND c.clie_categoria_cliente ="."'".$cat_cliente."'":"")."
					";
		$queryRES = "select ins.inst_rut AS nit_instalador, CONCAT(inst_nombre,' ',inst_paterno,' ',inst_materno) AS instalador,
       				SUM(od.osde_precio) AS costo, sum(cast(od.osde_precio*1/od.osde_precio as signed)) AS venta, od.osde_descripcion, os.id_local
					from ot ot
					join os os on ot.id_os = os.id_os
					join clientes c on c.clie_rut = os.clie_rut
					join os_detalle od on od.id_os = os.id_os
					join instaladores ins on ins.id_instalador = ot.id_instalador
					where os.id_estado = 'SP' and od.osde_subtipoprod in ('VI','IN')
					$aux_ori
					".(($fecha_desde)?" AND os.os_fechaboleta >="."'".invierte_fechaGuion($fecha_desde)." 00:00:00'":"AND os.os_fechaboleta >='".$fecha_hoy." 00:00:00'")."
					".(($fecha_hasta)?" AND os.os_fechaboleta <="."'".invierte_fechaGuion($fecha_hasta)." 23:59:59'":"AND os.os_fechaboleta <='".$fecha_hoy." 23:59:59'")."
					".(($idCentroDespacho)?" AND os.id_local ="."'".$idCentroDespacho."'":"AND os.id_local = '".$idlocal_act."'")." 
					".(($emp_inst)?" AND ot.id_instalador ="."'".$emp_inst."'":"")."
					".(($cat_cliente)?" AND c.clie_categoria_cliente ="."'".$cat_cliente."'":"")."
					group by ot.id_instalador order by venta";

					//where ot.id_estado = 'VM' and od.osde_subtipoprod='VI'

				$result = tep_db_query($queryCount);
				$res = tep_db_fetch_array( $result );
				$tot_reg = $res['Cont'];
					if($tot_reg == 0)
			      			$MiTemplate->set_var("alert","<script language=\"JavaScript\">alert(\"No existen Visitas de Instalador para los Filtros Seleccionados. \");</script>");		
	
					
				$result = tep_db_query($queryRES);
				while( $res = tep_db_fetch_array( $result ) ) {
					$cont++;	
					$MiTemplate->set_var("num", $cont);
					$MiTemplate->set_var("rut_inst", ($res['nit_instalador'])?$res['nit_instalador']:"&nbsp;");
					$MiTemplate->set_var("nom_inst", ($res['instalador'])?$res['instalador']:"&nbsp;");
					$MiTemplate->set_var("costo", ($res['costo'])?formato_precio($res['costo']):"&nbsp;");

					$venta=$res['venta'];		
					$variables=$res['nit_instalador']."|".invierte_fechaGuion($fecha_desde).'|'.invierte_fechaGuion($fecha_hasta);
					$MiTemplate->set_var("num_inst", "<a href=\"../consulta_precios/detalleVentaInstalador.php?variables=".$variables."\">".$venta."</a>");
								
					$MiTemplate->parse("PBLResultados", "Bloque_Resultados", true);
					$MiTemplate->set_var("Export","<INPUT type=\"button\" value=\"Exportar a Excel\" id=\"button1\" name=\"button1\" onCLick=\"exportToXL(viaje.all('idTable'))\"/>");
				}
	}

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