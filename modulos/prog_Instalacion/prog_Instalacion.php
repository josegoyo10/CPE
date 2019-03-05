<?
$pag_ini = '../prog_instalacion/prog_Instalacion.php';
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
    $MiTemplate->set_file("main","prog_Instalacion/prog_Instalacion.htm");   
    
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
	
    // Inicio. Recuperamos informacion de la tabla Estados   para llenar combo_box Estados
    $MiTemplate->set_block("main", "Bloque_estados", "PBL_Bloque_estados");
	$query = "Select id_estado, esta_nombre From estados Where esta_tipo='SS' Order By esta_nombre";
    query_to_set_var( $query, $MiTemplate, 1, 'PBL_Bloque_estados', 'Bloque_estados' );
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
	//if($fecha_desde=='' && $fecha_hasta=='' && $idCentroDespacho=='' && $estd=='' && $id_OS=='' &&$rut=='' && $cod_prod=='' && $depto=='' && $emp_inst=='')
		$queryCount="Select count(*)AS Cont 
									From os O
									 LEFT Join clientes CL ON CL.clie_rut=O.clie_rut
									 LEFT Join direcciones D ON D.id_direccion=O.id_direccion
									 LEFT Join os_detalle OD ON OD.id_os=O.id_os 
								        LEFT Join ot OT ON OT.ot_id=OD.ot_id
								        LEFT Join instaladores I ON I.id_instalador=OT.id_instalador 
									 LEFT Join empresa_instaladora EM ON  EM.id_empresa_instaladora = I.id_empresa_instaladora
								        LEFT Join estados E ON E.id_estado = OT.id_estado
									 where 1
									AND OD.osde_tipoProd = 'SV' 
									AND OD.osde_subtipoprod IN ('VI','IN')
									 ".(($fecha_desde)?" AND O.os_fechaboleta >="."'".invierte_fechaGuion($fecha_desde)." 00:00:00'":"AND O.os_fechaboleta >='".$fecha_hoy." 00:00:00'")."
									 ".(($fecha_hasta)?" AND O.os_fechaboleta <="."'".invierte_fechaGuion($fecha_hasta)." 23:59:59'":"AND O.os_fechaboleta <='".$fecha_hoy." 23:59:59'")."
									 ".(($idCentroDespacho)?" AND O.id_local ="."'".$idCentroDespacho."'":"AND O.id_local = '".$idlocal_act."'")." 
									 ".(($estd)?" AND O.id_estado ="."'".$estd."'":"AND O.id_estado='SP'")."
									 ".(($id_OS)?" AND O.id_os ="."'".$id_OS."'":"")."
									 ".(($rut)?" AND O.clie_rut ="."'".$rut."'":"")."
									 ".(($cod_prod)?" AND OD.id_producto ="."'".$cod_prod."'":"")."
									 ".(($emp_inst)?" AND I.id_instalador ="."'".$emp_inst."'":"")."
									 GROUP BY O.id_os";
		$queryRES = "Select O.id_os, E.esta_nombre AS est_visita, O.os_fechaboleta, CONCAT(CL.clie_nombre,' ',CL.clie_materno,' ',CL.clie_paterno) AS nom_cliente, CL.clie_telefonocasa, CL.clie_telcontacto2, D.dire_direccion,  O.id_direccion, OD.cod_sap, (OD.osde_precio * OD.osde_cantidad) AS osde_precio, OD.osde_descripcion, OT.fecha_visita_inst, I.inst_nombre, OT.doc_instalacion, EM.descripcion, OT.os_asoInstala, OT.os_asoMaterial, OT.os_motDevolucion, OT.os_valDevolucion 
									 From os O
									 LEFT Join clientes CL ON CL.clie_rut=O.clie_rut
									 LEFT Join direcciones D ON D.id_direccion=O.id_direccion
									 LEFT Join os_detalle OD ON OD.id_os=O.id_os 
								        LEFT Join ot OT ON OT.ot_id=OD.ot_id
								        LEFT Join instaladores I ON I.id_instalador=OT.id_instalador 
									 LEFT Join empresa_instaladora EM ON  EM.id_empresa_instaladora = I.id_empresa_instaladora
								        LEFT Join estados E ON E.id_estado = OT.id_estado
									 where 1
									AND OD.osde_tipoProd = 'SV' 
									AND OD.osde_subtipoprod IN ('IN','VI')
									 ".(($fecha_desde)?" AND O.os_fechaboleta >="."'".invierte_fechaGuion($fecha_desde)." 00:00:00'":"AND O.os_fechaboleta >='".$fecha_hoy." 00:00:00'")."
									 ".(($fecha_hasta)?" AND O.os_fechaboleta <="."'".invierte_fechaGuion($fecha_hasta)." 23:59:59'":"AND O.os_fechaboleta <='".$fecha_hoy." 23:59:59'")."
									 ".(($idCentroDespacho)?" AND O.id_local ="."'".$idCentroDespacho."'":"AND O.id_local = '".$idlocal_act."'")." 
									 ".(($estd)?" AND O.id_estado ="."'".$estd."'":"AND O.id_estado='SP'")."
									 ".(($id_OS)?" AND O.id_os ="."'".$id_OS."'":"")."
									 ".(($rut)?" AND O.clie_rut ="."'".$rut."'":"")."
									 ".(($cod_prod)?" AND OD.id_producto ="."'".$cod_prod."'":"")."
									 ".(($emp_inst)?" AND I.id_instalador ="."'".$emp_inst."'":"")."
									 ";

// Se suprime de la busqueda OD.osde_subtipoprod 'VI'
				$result = tep_db_query($queryCount);
				$res = tep_db_fetch_array( $result );
				$tot_reg = $res['Cont'];
					if($tot_reg == 0)
			      			$MiTemplate->set_var("alert","<script language=\"JavaScript\">alert(\"No existen Visitas de Instalación para los Filtros Seleccionados. \");</script>");		
	
					
				$result = tep_db_query($queryRES);
				while( $res = tep_db_fetch_array( $result ) ) {
				$cont++;
				$MiTemplate->set_var("No", $cont);	
				$MiTemplate->set_var("codigo", ($res['id_os'])?$res['id_os']:"&nbsp;");
				$MiTemplate->set_var("est_visita", ($res['est_visita'])?$res['est_visita']:"&nbsp;");		
				$MiTemplate->set_var("os_asoInstala", ($res['os_asoInstala'])?$res['os_asoInstala']:"N/A");
				$MiTemplate->set_var("os_asoMaterial", ($res['os_asoMaterial'])?$res['os_asoMaterial']:"N/A");
				$MiTemplate->set_var("os_motDevolucion", ($res['os_motDevolucion'])?$res['os_motDevolucion']:"N/A");
				$MiTemplate->set_var("os_valDevolucion", ($res['os_valDevolucion'])?$res['os_valDevolucion']:"N/A");	
				$MiTemplate->set_var("fecha_Pago", ($res['os_fechaboleta'])?$res['os_fechaboleta']:"&nbsp;");
				$MiTemplate->set_var("cliente", ($res['nom_cliente'])?$res['nom_cliente']:"&nbsp;");
				$MiTemplate->set_var("direccion", ($res['dire_direccion'])?$res['dire_direccion']:"&nbsp;");
				$MiTemplate->set_var("telefono", ($res['clie_telefonocasa'])?$res['clie_telefonocasa']:"&nbsp;");
				$MiTemplate->set_var("celular", ($res['clie_telcontacto2'])?$res['clie_telcontacto2']:"&nbsp;");
				$MiTemplate->set_var("fecha_inst", ($res['fecha_visita_inst'])?$res['fecha_visita_inst']:"N/A");
				
				$dirServ = consulta_localizacion($res['id_direccion'],2);
				$dirServicio = getlocalizacion($dirServ);
				getlocalizacion($dirServicio);
				$MiTemplate->set_var("barrio", $dirServicio['barrio']."-".$dirServicio['localidad']);
				$MiTemplate->set_var("proCodigo", ($res['cod_sap'])?$res['cod_sap']:"&nbsp;");
				$MiTemplate->set_var("precio", ($res['osde_precio'])?$res['osde_precio']:"&nbsp;");
				$MiTemplate->set_var("proDescrip", ($res['osde_descripcion'])?$res['osde_descripcion']:"&nbsp;");
				$MiTemplate->set_var("emp_instaladora", ($res['descripcion'])?$res['descripcion']:"&nbsp;");
				$MiTemplate->set_var("instalador", ($res['inst_nombre'])?$res['inst_nombre']:"&nbsp;");
				$MiTemplate->set_var("doc_instalacion", ($res['doc_instalacion'])?$res['doc_instalacion']:"N/A");
				
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