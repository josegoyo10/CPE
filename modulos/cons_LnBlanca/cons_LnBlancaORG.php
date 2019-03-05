<?
$pag_ini = '../cons_LnBlanca/cons_LnBlanca.php';
include "../../includes/aplication_top.php";
include "mssql.php";
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
    $MiTemplate->set_file("main","cons_LnBlanca/cons_LnBlanca.htm");  

    // Envio el número de dias establecidos para el rango de consultas.
    $MiTemplate->set_var("rango_cons", MAX_DIAS_CONSUL);
    
    //Crea el arreglo de codigos de Productos correspondientes a linea Blanca.
    	$Arreglo = consulta_VistaMSSQL($cod_sap);
 
    	$cod_sap = '';
    	foreach ($Arreglo AS $value)
    	{
    		$cod_sap =$cod_sap . $value;
    	}
    	$cod_sap = substr($cod_sap,0, -1);
    	
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
	
    // Inicio. Recuperamos informacion de la tabla Proveedores   para llenar combo_box de Proveedores
    $proveedor = consulta_VistaMSSQL_Proveedor($proveedor);
    $rut_prov = '';
    	foreach ($proveedor AS $value_prov)
    	{
    		$rut_prov = $rut_prov . $value_prov;
    	}
    	$rut_prov = substr($rut_prov,0, -1);

    $MiTemplate->set_block("main", "Bloque_proveedores", "PBL_Bloque_proveedores");
    $query = "SELECT id_proveedor, nom_prov AS nom_prov FROM proveedores where cod_prov in ($rut_prov) order by nom_prov";
//echo $query;
	query_to_set_var( $query, $MiTemplate, 1, 'PBL_Bloque_proveedores', 'Bloque_proveedores' );
    //Fin de la recuperacion de Datos.
	
    // Inicio. Recuperamos informacion de la tabla Locales  para llenar el Encabezado de la tabla
	$query = "Select nom_local From locales Where id_local=".$idCentroDespacho;
	$rq = tep_db_query($query );
	$res = tep_db_fetch_array( $rq );
	$nom_local = $res['nom_local'];
	//Fin de la recuperacion de Datos.
	
    // Escribe el encabezado de la Tabla según los criterios de Consulta establecidos
    	if (!$fecha_desde && !$fecha_hasta && $idCentroDespacho)
					$glosa = "Pedidos de Linea Blanca  para el Centro de Despacho $nom_local";
				elseif($fecha_desde && !$fecha_hasta && $idCentroDespacho)
					$glosa = "Pedidos de Linea Blanca desde el $fecha_desde para el Centro de Despacho $nom_local";
				elseif(!$fecha_desde && $fecha_hasta && $idCentroDespacho)
					$glosa = "Pedidos de Linea Blanca hasta el $fecha_hasta para el Centro de Despacho $nom_local";
				elseif($fecha_desde && $fecha_hasta && $idCentroDespacho)
					$glosa = "Pedidos de Linea Blanca entre el $fecha_desde y el $fecha_hasta para el Centro de Despacho $nom_local";
				elseif(!$idCentroDespacho && !$fecha_hasta && $fecha_desde)
					$glosa = "Pedidos de Linea Blanca para todos los Centros de Despacho desde el $fecha_desde";
				elseif(!$idCentroDespacho && !$fecha_desde && $fecha_hasta)
					$glosa = "Pedidos de Linea Blanca para todos los Centros de Despacho hasta el $fecha_hasta";
				elseif(!$idCentroDespacho && $fecha_desde && $fecha_hasta)
					$glosa = "Pedidos de Linea Blanca para todos los Centros de Despacho desde el $fecha_desde hasta el $fecha_hasta";
				elseif(!$idCentroDespacho && !$fecha_desde && !$fecha_hasta)
					$glosa = "Pedidos de Linea Blanca para todos los Centros de Despacho";
    	$MiTemplate->set_var("Encabezado", $glosa);  
    

    //	Se inicializa el bloque en el que se presentaran los resultados
	$MiTemplate->set_block("main", "Bloque_Resultados", "PBLResultados");

	
	// Se realizan las consultas a la Base de Datos segun los criterios de Consulta establecidos.
	// Se realiza consulta de Conteo de registros y consulta de informacion.
 
	//$fecha_desde!='' && $fecha_hasta!='' && $idCentroDespacho=='' && $idproveedores!='0'){
			$queryCount="SELECT DISTINCTROW COUNT(*) AS Cont
						   FROM os_detalle OD
						   	LEFT JOIN os O ON O.id_os= OD.id_os
						   	LEFT JOIN ot OT ON OT.id_os = O.id_os
						   	LEFT JOIN productos P ON P.cod_prod1 = OD.cod_sap
							LEFT JOIN prodxprov PP ON PP.id_producto = OD.id_producto
						   	LEFT JOIN clientes CL ON CL.clie_rut= O.clie_rut
						   	LEFT JOIN direcciones DR ON DR.clie_rut= CL.clie_rut
						   	LEFT JOIN locales L ON L.id_local=O.id_local
						    where 1 AND OD.cod_sap in(".$cod_sap.")
							  AND OT.ot_tipo='PE' AND OT.id_estado='EC'
							  ".(($fecha_desde)?" AND OT.ot_fechacreacion >="."'".invierte_fechaGuion($fecha_desde)." 00:00:00'":"AND OT.ot_fechacreacion >="."'".$fecha_hoy." 00:00:00'")."
							  ".(($fecha_hasta)?" AND OT.ot_fechacreacion <="."'".invierte_fechaGuion($fecha_hasta)." 23:59:59'":"AND OT.ot_fechacreacion <="."'".$fecha_hoy." 23:59:59'")."
							  ".(($idCentroDespacho)?" AND O.id_local ="."'".$idCentroDespacho."'":"")."
							  ".(($idproveedores)?" AND PP.id_proveedor ="."'".$idproveedores."'":"")."
							  GROUP BY OD.id_os_detalle";
			
			$queryRES = "SELECT DISTINCTROW OT.ot_tipo, OT.id_estado, OT.ot_id, OT.noc_sap, OD.cod_sap, OD.osde_precio, OD.osde_cantidad, OD.cod_barra, P.des_larga, OD.osde_fecha_entrega, OD.observacion,O.os_fechacreacion, L.nom_local,
								CL.clie_rut, CONCAT(CL.clie_nombre,' ',CL.clie_paterno,' ',CL.clie_materno) AS nom_cliente, CL.clie_telefonocasa, CL.clie_telcontacto1,DR.dire_direccion,DR.dire_localizacion
						   FROM os_detalle OD
						   	LEFT JOIN os O ON O.id_os= OD.id_os
						   	LEFT JOIN ot OT ON OT.id_os = O.id_os
						   	LEFT JOIN productos P ON P.cod_prod1 = OD.cod_sap
							LEFT JOIN prodxprov PP ON PP.id_producto = OD.id_producto
						   	LEFT JOIN clientes CL ON CL.clie_rut= O.clie_rut
						   	LEFT JOIN direcciones DR ON DR.clie_rut= CL.clie_rut
						   	LEFT JOIN locales L ON L.id_local=O.id_local
						   where 1 AND OD.cod_sap in(".$cod_sap.")
							  AND OT.ot_tipo='PE' AND OT.id_estado='EC'
							  ".(($fecha_desde)?" AND OT.ot_fechacreacion >="."'".invierte_fechaGuion($fecha_desde)." 00:00:00'":"AND OT.ot_fechacreacion >="."'".$fecha_hoy." 00:00:00'")."
							  ".(($fecha_hasta)?" AND OT.ot_fechacreacion <="."'".invierte_fechaGuion($fecha_hasta)." 23:59:59'":"AND OT.ot_fechacreacion <="."'".$fecha_hoy." 23:59:59'")."
							  ".(($idCentroDespacho)?" AND O.id_local ="."'".$idCentroDespacho."'":"")."
							  ".(($idproveedores)?" AND PP.id_proveedor ="."'".$idproveedores."'":"")."
							  GROUP BY OD.id_os_detalle";
	//echo $queryRES;
	//exit();
				$result = tep_db_query($queryCount);
				$res = tep_db_fetch_array( $result );
				$tot_reg = $res['Cont'];
					if($tot_reg == 0)
			      			$MiTemplate->set_var("alert","<script language=\"JavaScript\">alert(\"No existen pedidos de Linea Blanca para la consulta solicitada. \");</script>");		
		
				$result = tep_db_query($queryRES);
				while( $res = tep_db_fetch_array( $result ) ) {
					
				$MiTemplate->set_var("ot_id", ($res['ot_id'])?$res['ot_id']:"-N/A-");
				$MiTemplate->set_var("noc_sap", ($res['noc_sap'])?$res['noc_sap']:"-N/A-");
				$MiTemplate->set_var("fecha_crea", $res['os_fechacreacion']);
				$MiTemplate->set_var("tienda", $res['nom_local']);
				$MiTemplate->set_var("cedula", $res['clie_rut']);
				$MiTemplate->set_var("clie_nombre", substr($res['nom_cliente'],0,135));
				$MiTemplate->set_var("clie_tel", $res['clie_telefonocasa']);
				$MiTemplate->set_var("clie_cel", ($res['clie_telcontacto1'])?$res['clie_telcontacto1']:"<br>");
				$MiTemplate->set_var("clie_dire", substr($res['dire_direccion'],0,255));
				
				$dirServ = consulta_localizacion($res['clie_rut'],1);
				$dirServicio = getlocalizacion($dirServ);
				getlocalizacion($dirServicio);
				$MiTemplate->set_var("ciudad", ($dirServicio['ciudad'])?$dirServicio['ciudad']:"<br>");
				$MiTemplate->set_var("barrio", ($dirServicio['barrio']."-".$dirServicio['localidad'])?$dirServicio['barrio']."-".$dirServicio['localidad']:"<br>");
				$MiTemplate->set_var("cod_EAN", $res['cod_barra']);
				$MiTemplate->set_var("des_prod", substr($res['des_larga'],0,500));
				$MiTemplate->set_var("cant_prod", $res['osde_cantidad']);
				$MiTemplate->set_var("val_prod", $res['osde_precio']);
				$MiTemplate->set_var("obs_prod", ($res['observacion'])?$res['observacion']:"-N/A-");
				$MiTemplate->set_var("fec_entrega", ($res['osde_fecha_entrega'])?$res['osde_fecha_entrega']:"<br>");
				
				
				$MiTemplate->parse("PBLResultados", "Bloque_Resultados", true);
				$MiTemplate->set_var("Export","<INPUT type=\"button\" value=\"Exportar a Excel\" id=\"button1\" name=\"button1\" onCLick=\"exportToXL(viaje.all('idTable'))\"/>");
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