<? 
session_start();
$id_ot = $_SESSION['id_ot2'];
$pag_ini = '../monitor_despacho/monitor_despacho.php';
include "../../includes/aplication_top.php";


/****************************************************************
 *
 * IMPRIME Listado Monitor Despacho
 *
 ****************************************************************/
function DisplayPrint() {
    global $ses_usr_id, $id_ot;
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

    // Agregamos el main
    $MiTemplate->set_file("main","monitor_despacho/print1_GuiaDes.htm");
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

	// Query de Detalles
	$queryOTdet="SELECT distinct os.id_os, od.ot_id, cl.clie_nombre,cl.clie_paterno,cl.clie_materno,cl.clie_rut,dire_direccion,l.nom_local, d.id_direccion FROM os
	JOIN direcciones	d on d.id_direccion	= os.id_direccion
	JOIN clientes		cl on cl.clie_rut	= os.clie_rut
	JOIN os_detalle		od on od.id_os	= os.id_os
	JOIN locales		l  on os.id_local=l.id_local 
	WHERE od.ot_id in ($id_ot)";
	
	$dbOTdet =	tep_db_query($queryOTdet);

	$MiTemplate->set_block("main", "Bloque_guias", "PBL_Modulos");
	while ($resOTdet = tep_db_fetch_array($dbOTdet)) {
		if ($ot_id != $resOTdet['ot_id']) {
			$ot_id = $resOTdet['ot_id'];
			$MiTemplate->set_var('ot_id',"	<tr><tD colspan=5>&nbsp;</th></tr>
											<tr><th colspan=5>OT # $ot_id</th></tr>
											<tr>
												<th>Código OT(SKU)</th>
												<th>Cédula</th>
												<th>Cliente</th>
												<th>Dirección</th>
												<th>Barrio - Localidad</th>
											</tr>");
		}
		else {
			$MiTemplate->set_var('ot_id',"");
		}
		$dirServ = consulta_localizacion($resOTdet['id_direccion'],2);
    	$dirServicio = getlocalizacion($dirServ);
		$MiTemplate->set_var('ot_id',$resOTdet["ot_id"]);
		$MiTemplate->set_var('clie_rut',$resOTdet["clie_rut"]);
		$MiTemplate->set_var('clie_nombre',$resOTdet['clie_nombre'].' '.$resOTdet['clie_paterno'].' '.$resOTdet['clie_materno']);
		$MiTemplate->set_var('dire_direccion',$resOTdet["dire_direccion"]);
		$MiTemplate->set_var('comu_nombre',$dirServicio["barrio"]." - ".$dirServicio["localidad"]);
			
		$MiTemplate->parse("PBL_Modulos", "Bloque_guias", true); 
	}
	$MiTemplate->pparse("OUT_H", array("header"), true);
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
