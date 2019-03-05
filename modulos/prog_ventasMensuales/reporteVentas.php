<?
$pag_ini = '../prog_ventasMensuales/reporteVentas.php';
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
    $MiTemplate->set_file("main","prog_ventasMensuales/reporteVentas.htm");
    
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

	// Inicializa los valores de la plantilla
	$MiTemplate->set_var("visible", "hidden;");
	$MiTemplate->set_var("fecha_desde", $fecha_desde);
	$MiTemplate->set_var("fecha_hasta", $fecha_hasta);
	$MiTemplate->set_var("idLocal", $idCentroDespacho);

if($accion == 'update'){
    // Escribe el encabezado de la Tabla según los criterios de Consulta establecidos
    	if (!$fecha_desde && !$fecha_hasta && $idCentroDespacho)
					$glosa = "Ventas Totales para la Tienda $nom_local";
				elseif($fecha_desde && !$fecha_hasta && $idCentroDespacho)
					$glosa = "Ventas Totales desde $fecha_desde para la Tienda $nom_local";
				elseif(!$fecha_desde && $fecha_hasta && $idCentroDespacho)
					$glosa = "Ventas Totales hasta el $fecha_hasta para la Tienda $nom_local";
				elseif($fecha_desde && $fecha_hasta && $idCentroDespacho)
					$glosa = "Ventas Totales entre el $fecha_desde y el $fecha_hasta para la Tienda $nom_local";
				elseif(!$idCentroDespacho && !$fecha_hasta && $fecha_desde)
					$glosa = "Ventas Totales para todas las Tiendas desde el $fecha_desde";
				elseif(!$idCentroDespacho && !$fecha_desde && $fecha_hasta)
					$glosa = "Ventas Totales para todas las Tiendas hasta el $fecha_hasta";
				elseif(!$idCentroDespacho && $fecha_desde && $fecha_hasta)
					$glosa = "Ventas Totales para todas las Tiendas desde el $fecha_desde hasta el $fecha_hasta";
				elseif(!$idCentroDespacho && !$fecha_desde && !$fecha_hasta)
					$glosa = "Ventas Totales para todas las Tiendas";
    	$MiTemplate->set_var("Encabezado", $glosa);

    //	Se inicializa el bloque en el que se presentaran los resultados
	$MiTemplate->set_block("main", "Bloque_Resultados", "PBLResultados");

	// Se realizan las consultas a la Base de Datos segun los criterios de Consulta establecidos.
	// Se realiza consulta de Conteo de registros y consulta de informacion.
	// Query por defecto con Valores: Fecha Hoy.
		$queryCount="Select count(*)AS Cont
						FROM os OS
						JOIN os_detalle OD on OD.id_os=OS.id_os
						WHERE OS.id_estado='SP' AND OS.os_numboleta is not null
						".(($fecha_desde)?" AND OS.os_fechaboleta >="."'".invierte_fechaGuion($fecha_desde)." 00:00:00'":"AND OS.os_fechaboleta >='".$fecha_hoy." 00:00:00'")."
						".(($fecha_hasta)?" AND OS.os_fechaboleta <="."'".invierte_fechaGuion($fecha_hasta)." 23:59:59'":"AND OS.os_fechaboleta <='".$fecha_hoy." 23:59:59'")."
						".(($idCentroDespacho)?" AND OS.id_local ="."'".$idCentroDespacho."'" : "").";";

		$queryRES = "Select (sum((OD.osde_precio) * (OD.osde_cantidad))) AS Total, MONTH(OS.os_fechaboleta) as mes,
				        CASE MONTH(OS.os_fechaboleta)
				                  WHEN 1 THEN 'Enero'
				                  WHEN 2 THEN 'Febrero'
				                  WHEN 3 THEN 'Marzo'
				                  WHEN 4 THEN 'Abril'
				                  WHEN 5 THEN 'Mayo'
				                  WHEN 6 THEN 'Junio'
				                  WHEN 7 THEN 'Julio'
				                  WHEN 8 THEN 'Agosto'
				                  WHEN 9 THEN 'Septiembre'
				                  WHEN 10 THEN 'Octubre'
				                  WHEN 11 THEN 'Noviembre'
				                  WHEN 12 THEN 'Diciembre'
				                  END AS Mes,
				        YEAR(OS.os_fechaboleta) AS Ano, OS.id_local
						FROM os OS
						JOIN os_detalle OD on OD.id_os=OS.id_os
						WHERE OS.id_estado='SP' AND OS.os_numboleta is not null
						".(($fecha_desde)?" AND OS.os_fechaboleta >="."'".invierte_fechaGuion($fecha_desde)." 00:00:00'":"AND OS.os_fechaboleta >='".$fecha_hoy." 00:00:00'")."
						".(($fecha_hasta)?" AND OS.os_fechaboleta <="."'".invierte_fechaGuion($fecha_hasta)." 23:59:59'":"AND OS.os_fechaboleta <='".$fecha_hoy." 23:59:59'")."
						".(($idCentroDespacho)?" AND OS.id_local ="."'".$idCentroDespacho."'" : "")."
						GROUP BY Ano, Mes
						ORDER BY Ano, mes  ASC;";

				$result = tep_db_query($queryCount);
				$res = tep_db_fetch_array( $result );
				$tot_reg = $res['Cont'];
					if($tot_reg == 0){
			      		$MiTemplate->set_var("alert","<script language=\"JavaScript\">alert(\"No existen Ventas para los Filtros Seleccionados. \");</script>");
					}
					else{
						$MiTemplate->set_var("visible", "visible;");
					}
			    $result = tep_db_query($queryRES);
				while( $res = tep_db_fetch_array( $result ) ) {
						$cont++;
						$MiTemplate->set_var("No", $cont);
						$MiTemplate->set_var("Ano", ($res['Ano'])? $res['Ano'] :"&nbsp;");
						$MiTemplate->set_var("Mes", ($res['Mes'])? $res['Mes'] :"&nbsp;");
						$MiTemplate->set_var("Total", ($res['Total'])? formato_precio($res['Total']) :"N/A");

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