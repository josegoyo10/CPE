<? 
session_start();
$pag_ini = '../monitor_despacho/monitor_despacho.php';
include "../../includes/aplication_top.php";


/****************************************************************
 *
 * IMPRIME Listado Monitor Despacho
 *
 ****************************************************************/
function DisplayPrint() {
    global $ses_usr_id;
    global $texto_osot,$radioE,$busqueda,$select_tipo,$select_tipo,$select_des,$select_estado,
	$select4,$orden,$select_fecha,$NUM,$RAD_IO,$ORDEN,$TO,$TD,$OTF,$EST;

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

    $MiTemplate->set_var("BOTON_VER",BOTON_VER);
    $MiTemplate->set_var("TEXT_BOTON",TEXT_BOTON);
    $MiTemplate->set_var("TEXT_SELECT",TEXT_SELECT);

    // Agregamos el header
    $MiTemplate->set_file("header","header_ident.html");

    // Agregamos el main
    $MiTemplate->set_file("main","monitor_despacho/print1.htm");
	$MiTemplate->set_var('fechaActual',DATE('d/m/Y'));

	//ordenamiento
	$orden1="";
	if($ORDEN != ""){
		$order1 = ($ORDEN)?" order by $ORDEN":" order by 1";
		$MiTemplate->set_var("selected4".$select4,"selected");
	}
 	
	/* recupera los valores en la primera búsqueda*/	
	if ($RAD_IO=="os"){
		$where0= " and ot.id_os=".$NUM;
	}	
	if ($RAD_IO=="ot"){
		$where0= " and ot.ot_id= ".$NUM;
	}

	$aux=$EST;
	$a = split(",", $aux);
	count($a);
	if ($a[0] && $a[1]){
		$uno="'".$a[0]."'";
		$dos="'".$a[1]."'";
		$select_es="(".$uno.",".$dos.")";
		$valor=true;
	}
	if ($a[0] || $a[1]){
		$uno="'".$a[0]."'";
		$dos="'".$a[1]."'";
		$select_es="".$uno.",".$dos."";
		$valor=true;
	}

	/* si viene vacio el radio*/	/* si viene vacio el texto*/
	if (($RAD_IO=='')||($NUM=='')){
			$MiTemplate->set_var("checkedT","checked");
			$where0= " ";
	}
	//Recuperamos el permiso por local
	if ( $mylocal = get_local_usr( $ses_usr_id ))
		$where_aux_local = " and os1.id_local = $mylocal ";
	$select_ti="'".$TO."'";
	$where1 = ($TO)?" and ot.ot_tipo = " . ($select_ti) :"";
	$where2 = ($TD)?" and ot.id_tipodespacho = " . ($TD) :"";

	if ($select_es){
		$select_es="(".$select_es.")";
		$where3 = ($select_es)?" and ot.id_estado in " . ($select_es) :" ";
	}
	$where4 = ($OTF)?" and ot_fechacreacion >= '$OTF 00:00:00' and ot_fechacreacion <= '$OTF 23:59:59'":"";
	$where5= " and ot.id_estado not in('EC', 'EP', 'ET')";

	$id_ot = $_SESSION['id_ot2'];
	// Query de Detalles
	$queryOTdet="select os.ot_id, os.ot_id,os.cod_barra, os.cod_sap, os.osde_descripcion, os.osde_precio, os.osde_cantidad, os.osde_tipoprod,os.osde_especificacion,os.cant_pickeada 
	from os_detalle os 
	join ot on os.ot_id = ot.ot_id 
	join os os1 on os1.id_os = ot.id_os 
	join locales l on l.id_local = os1.id_local 
	where 1  
	and ot.ot_tipo='PS'  
	and ot.id_estado ='PC' 
	and os1.origen in ('C','S','V')$where_aux_local and ot.ot_id in ($id_ot+0)
	$where0	$where1 $where2  $where4 $where5 
	$order1";
	
	$dbOTdet =	tep_db_query($queryOTdet);

	$MiTemplate->set_block("main", "LISTADO_OT", "BLO_listado");
	while ($resOTdet = tep_db_fetch_array($dbOTdet)) {
		if ($ot_id != $resOTdet['ot_id']) {
			$ot_id = $resOTdet['ot_id'];
			$MiTemplate->set_var('ot_id',"	<tr><tD colspan=5>&nbsp;</th></tr>
											<tr><th colspan=5>OT # $ot_id</th></tr>
											<tr>
												<th>UPC/(SKU)</th>
												<th>Descripci&oacute;n</th>
												<th>Precio Unitario </th>
												<th>Cantidad Solicitada </th>
												<th>Cantidad Pickeada</th>
											</tr>");
		}
		else {
			$MiTemplate->set_var('ot_id',"");
		}

		$MiTemplate->set_var('id_os_detalle',$resOTdet["id_os_detalle"]);
		$MiTemplate->set_var('cod_barra',$resOTdet["cod_barra"]);
		$MiTemplate->set_var('cod_sap',$resOTdet["cod_sap"]);
		$MiTemplate->set_var('osde_descripcion',$resOTdet["osde_descripcion"]);
		$MiTemplate->set_var('osde_precio',$resOTdet["osde_precio"]);
		$MiTemplate->set_var('osde_cantidad',$resOTdet["osde_cantidad"]);
			if ($resOTdet["cant_pickeada"]==''){
					$MiTemplate->set_var('cant_pickeada','0');
			}else{
			$MiTemplate->set_var('cant_pickeada',$resOTdet["cant_pickeada"]);
			}
		$MiTemplate->parse("BLO_listado", "LISTADO_OT", true);
	}

$MiTemplate->parse("OUT_M", array("main"), true);
$MiTemplate->p("OUT_M");
	
}

/**********************************************************************************************/
switch($req){
	default:
		DisplayPrint();
		break;
}

/**********************************************************************************************/

include "../../includes/application_bottom.php";

?>
