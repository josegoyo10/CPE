<?
$pag_ini = '../facturacion/facturacion_05.php';
include "../../includes/aplication_top.php";

/****************************************************************
 *
 * Despliega Listado Monitor Despacho
 *
 ****************************************************************/
function DisplayListado() {
    global $ses_usr_id;
    global $rut,$texto_osot,$busqueda,$select_tipo,$select_tipo,$select_des,$select_estado,$select_local,$select4,$orden,$impresion,$where0,$radioE,$checkedS,$checkedT,$anoselect,$messelect,$radioR;

    $MiTemplate = new Template();
    $MiTemplate->debug = 0;
    $MiTemplate->set_root(DIRTEMPLATES);
    $MiTemplate->set_unknowns("remove");

    $MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO);
    $MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));

    // Agregamos el header
    $MiTemplate->set_file("header","header_ident.html");

    // Agregamos el main
    $MiTemplate->set_file("main","facturacion/facturacion_05.htm");

	$MiTemplate->set_var("select_estado",$select_estado);
	$MiTemplate->set_var("id_estado",$id_estado);
	$MiTemplate->set_var("texto_osot",$texto_osot);
	$MiTemplate->set_var("radioE",$radioE);
	$MiTemplate->set_var("rut",($rut)?$rut . "-" . dv($rut):"");
	$MiTemplate->set_var("selectedM" . $messelect, "selected"); 
	$MiTemplate->set_var("messelect", $messelect); 
	$MiTemplate->set_var("anoselect", $anoselect); 

	//Para el orden de la query ppal
	$select4 = ($select4)?$select4:"2";
	$MiTemplate->set_var("selected4".$select4,"selected");

	if ($select4==10){
		//Para el orden de la query ppal
		$select4 = ($select4)?$select4:"2";
		$MiTemplate->set_var("selected50","selected");
	}

	//Para Rango Fechas, Recuperamos los a�os disponibles
    $MiTemplate->set_block("main", "AnoDisp", "BLO_AnoDisp");
	$query_A="SELECT distinct date_format(ot_fechacreacion, '%Y') as fecha, if (DATE_FORMAT( ot_fechacreacion, '%Y')='$anoselect', 'selected', '') selected 
			FROM ot join os on os.id_os = ot.id_os 
			WHERE ot_fechacreacion is not null and ot_tipo in ('SV')  
			ORDER BY 1";
	query_to_set_var( $query_A, $MiTemplate, 1, 'BLO_AnoDisp', 'AnoDisp' );

	//Recuperamos los estados
    $MiTemplate->set_block("main", "Estados", "BLO_estados");
	$query_E="	SELECT id_estado, esta_nombre, if ('$select_estado' = id_estado, 'selected', '') selected 
				FROM estados
				WHERE esta_tipo = 'TV' and id_estado in ('VM', 'VP', 'VF') 
				ORDER BY orden";
	query_to_set_var( $query_E, $MiTemplate, 1, 'BLO_estados', 'Estados' );
	
	if ($radioE=="ot" && $texto_osot){
		$MiTemplate->set_var("checkedT","checked");
		$where4 = " and ot.ot_id= ".$texto_osot;
	}
	if ($radioE=="lo" && $texto_osot){
		$MiTemplate->set_var("checkedL","checked");
		$where4 = " and ot.id_lote= ".$texto_osot;
	}
	if ($radioE=="fa" && $texto_osot){
		$MiTemplate->set_var("checkedF","checked");
		$where4 = " and li.num_factura= ".$texto_osot;
	}

	$where1 = ($rut)?" and i.inst_rut = " . ($rut) :"";
	$where2 = ($messelect)?" and date_format(ot_ftermino, '%m') = $messelect":" ";
	$where3 = ($anoselect)?" and date_format(ot_ftermino, '%Y') = $anoselect":" ";
	$where5 = ($select_estado)?" and ot.id_estado = '" . ($select_estado) ."'":" ";
	$order1 = " order by $select4";

    $MiTemplate->set_block("main", "lista", "PBLlistado");
	$query="
			SELECT ot.ot_id,concat(inst_nombre, ' ', inst_paterno, ' ', inst_materno) instalador, osde_descripcion detalle, date_format(ot_ftermino, '%d/%m/%Y') ot_ftermino,osde_precio precio, ot.id_estado as estado,ot.id_lote lote, li.num_factura factura,li.numero1,li.numero2,esta_nombre, 
			if (li.num_factura is null,'nf',li.num_factura) reqf,
			if (li.id_lote is null,'nl',li.id_lote) lotef,
			if (li.num_factura=0,'-',li.num_factura) factura ,
			e.id_estado, ot.id_instalador,ot.id_os as id_os
			FROM ot JOIN os on os.id_os = ot.id_os
					JOIN estados e on e.id_estado = ot.id_estado
					JOIN os_detalle osd on osd.ot_id = ot.ot_id
					JOIN instaladores i on i.id_instalador = ot.id_instalador
					LEFT JOIN lote_instalador li on li.id_lote = ot.id_lote
			WHERE e.id_estado in ('VM', 'VP', 'VF') $where1 $where2 $where3 $where4 $where5 $order1 ";
	query_to_set_var( $query, $MiTemplate, 1, 'PBLlistado', 'lista' );

   //Recuperamos las fechas de creacion
	$MiTemplate->set_block("main", "Fecha_Creacion", "BLO_cre");
	$queryf="
			SELECT DISTINCT date_format(ot_ftermino, '%d/%m/%Y') ot_ftermino, date_format(ot_ftermino, '%d/%m/%Y') fechat 
			FROM ot JOIN os on os.id_os = ot.id_os
					JOIN estados e on e.id_estado = ot.id_estado
					JOIN os_detalle osd on osd.ot_id = ot.ot_id
					JOIN instaladores i on i.id_instalador = ot.id_instalador
					LEFT JOIN lote_instalador li on li.id_lote = ot.id_lote
			WHERE e.id_estado in ('VM', 'VP', 'VF') and ot_ftermino is not null $where1 $where2 $where3 $where4 $where5 order by ot_ftermino 
	";
	query_to_set_var( $queryf, $MiTemplate, 1, 'BLO_cre', 'Fecha_Creacion' );

	include "../../includes/footer_cproy.php";

    $MiTemplate->pparse("OUT_H", array("header"), false);
    include "../../menu/menu.php";
    $MiTemplate->parse("OUT_M", array("main","footer"), true);
    $MiTemplate->p("OUT_M");
}

/**********************************************************************************************/
switch($req){
	default:
		DisplayListado();
		break;
}

/**********************************************************************************************/

include "../../includes/application_bottom.php";

?>