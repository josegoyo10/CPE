<?
$pag_ini = '../consulta_precios/reporte_instalador.php';
include "../../includes/aplication_top.php";
include_idioma_mod( $ses_idioma, "sp" );

/****************************************************************/
/****************************************************************
 *
 * Despliega Listado
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
    
    $var = explode("|",$variables);
    $emp_inst=$var[0];
    $fecha_desde=$var[1];
    $fecha_hasta=$var[2];

    // Agregamos el main
    $MiTemplate->set_file("main","consulta_precios/detalleVentasInstalador.htm");   

      //Se inicializa el bloque en el que se presentaran los resultados
	$MiTemplate->set_block("main", "Bloque_Resultados", "PBLResultados");

	// Se realizan las consultas a la Base de Datos segun los criterios de Consulta establecidos.
	// Se realiza consulta de Conteo de registros y consulta de informacion.
		$queryCount="Select count(*)AS Cont 
						FROM ot ot
						join os os on ot.id_os = os.id_os
						join os_detalle od on od.id_os = ot.id_os
						join instaladores ins on ins.id_instalador = ot.id_instalador
						where 1 and os.id_estado = 'SP' and od.osde_subtipoprod in ('VI','IN')
						".(($fecha_desde)?" AND os.os_fechaboleta >="."'".$fecha_desde." 00:00:00'":"AND os.os_fechaboleta >='".$fecha_hoy." 00:00:00'")."
						".(($fecha_hasta)?" AND os.os_fechaboleta <="."'".$fecha_hasta." 23:59:59'":"AND os.os_fechaboleta <='".$fecha_hoy." 23:59:59'")."
						".(($emp_inst)?" AND ins.inst_rut="."'".$emp_inst."'":"")."
						";
		$queryRES = "SELECT ot.ot_id, ot.ot_fechacreacion, od.id_producto, od.osde_precio, od.osde_descripcion, ot.fecha_visita_inst,CONCAT(inst_nombre,' ',inst_paterno,' ',inst_materno) AS instalador
					FROM ot ot
					join os os on ot.id_os = os.id_os
					join os_detalle od on od.id_os = ot.id_os
					join instaladores ins on ins.id_instalador = ot.id_instalador
					where 1 and os.id_estado = 'SP' and od.osde_subtipoprod in ('VI','IN')
					".(($fecha_desde)?" AND os.os_fechaboleta >="."'".$fecha_desde." 00:00:00'":"AND os.os_fechaboleta >='".$fecha_hoy." 00:00:00'")."
					".(($fecha_hasta)?" AND os.os_fechaboleta <="."'".$fecha_hasta." 23:59:59'":"AND os.os_fechaboleta <='".$fecha_hoy." 23:59:59'")."
					".(($emp_inst)?" AND ins.inst_rut="."'".$emp_inst."'":"")."
					";

				$result = tep_db_query($queryCount);
				$res = tep_db_fetch_array( $result );
				$tot_reg = $res['Cont'];
					if($tot_reg == 0)
			      			$MiTemplate->set_var("alert","<script language=\"JavaScript\">alert(\"No existen Visitas de Instalador para los Filtros Seleccionados. \");</script>");		
	
					
				$result = tep_db_query($queryRES);
				while( $res = tep_db_fetch_array( $result ) ) {
					$cont++;	
					$MiTemplate->set_var("num", $cont);
					$MiTemplate->set_var("id", ($res['ot_id'])?$res['ot_id']:"&nbsp;");
					$MiTemplate->set_var("fechacreacion", ($res['ot_fechacreacion'])?$res['ot_fechacreacion']:"&nbsp;");
					$MiTemplate->set_var("producto", ($res['id_producto'])?$res['id_producto']:"&nbsp;");
					$MiTemplate->set_var("precio", ($res['osde_precio'])?formato_precio($res['osde_precio']):"&nbsp;");
					$MiTemplate->set_var("descripcion", ($res['osde_descripcion'])?$res['osde_descripcion']:"&nbsp;");
					$MiTemplate->set_var("fecha_visita", ($res['fecha_visita_inst'])?$res['fecha_visita_inst']:"&nbsp;");
								
					$MiTemplate->parse("PBLResultados", "Bloque_Resultados", true);
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