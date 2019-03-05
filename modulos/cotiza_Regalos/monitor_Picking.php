<?php
$pag_ini = '../cotiza_Regalos/monitor_Picking.php';
include "../../includes/aplication_top.php";

include_idioma_mod( $ses_idioma, "monitor_Picking" );

/****************************************************************
 *
 * Despliega Listado Monitor de Picking
 *
 ****************************************************************/
function DisplayListado() {
    global $ses_usr_id;
    global $texto_osot, $radioE, $select4, $orden, $checkedS, $checkedT, $select_pag,$limite;
	$local_id = get_local_usr( $ses_usr_id );

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

	$MiTemplate->set_var("TEXT_CAMPO_1", TEXT_CAMPO_1);
	$MiTemplate->set_var("TEXT_CAMPO_2", TEXT_CAMPO_2);
	$MiTemplate->set_var("TEXT_CAMPO_3", TEXT_CAMPO_3);
	$MiTemplate->set_var("TEXT_CAMPO_4", TEXT_CAMPO_4);
	$MiTemplate->set_var("TEXT_CAMPO_5", TEXT_CAMPO_5);
	$MiTemplate->set_var("TEXT_CAMPO_6", TEXT_CAMPO_6);
	$MiTemplate->set_var("TEXT_CAMPO_7", TEXT_CAMPO_7);
	$MiTemplate->set_var("TEXT_CAMPO_8", TEXT_CAMPO_8);
	$MiTemplate->set_var("TEXT_CAMPO_9", TEXT_CAMPO_9);
	$MiTemplate->set_var("TEXT_CAMPO_10", TEXT_CAMPO_10);
	$MiTemplate->set_var("TEXT_CAMPO_11", TEXT_CAMPO_11);
	$MiTemplate->set_var("TEXT_CAMPO_12", TEXT_CAMPO_12);
	$MiTemplate->set_var("TEXT_CAMPO_13", TEXT_CAMPO_13);
	$MiTemplate->set_var("TEXT_CAMPO_14", TEXT_CAMPO_14);
	$MiTemplate->set_var("TEXT_CAMPO_15", TEXT_CAMPO_15);
	$MiTemplate->set_var("TEXT_CAMPO_16", TEXT_CAMPO_16);
	$MiTemplate->set_var("TEXT_CAMPO_17", TEXT_CAMPO_17);
	$MiTemplate->set_var("TEXT_CAMPO_18", TEXT_CAMPO_18);
	$MiTemplate->set_var("TEXT_CAMPO_19", TEXT_CAMPO_19);

    $MiTemplate->set_var("BOTON_VER",BOTON_VER);
    $MiTemplate->set_var("TEXT_BOTON",TEXT_BOTON);
    $MiTemplate->set_var("TEXT_SELECT",TEXT_SELECT);

    // Agregamos el header
    $MiTemplate->set_file("header","header_ident.html");

    // Agregamos el main
    $MiTemplate->set_file("main","cotiza_Regalos/monitor_Picking.htm");

	/*////////////////////// Para la paginacion //////////////////////*/
			 $MiTemplate->set_block("main", "Paginas", "PBLPaginas");
	        //Largo de los resultados por pantalla
	        $limite =MONITOR_LI_DE;

		$query_count = "SELECT DISTINCT COUNT(*) AS cuenta
						FROM list_ot OT
						WHERE 1 AND OT.ot_idEstado NOT IN ('EC', 'ER') AND OT.ot_listTiendaPago='".$local_id."'
		 				$where0";

		$rq_count = tep_db_query($query_count);
	        $res_count = tep_db_fetch_array($rq_count);
	        $total = ceil($res_count['cuenta'] / $limite);
	        if ($select_pag == ""){
	            $select_pag = 1;
	        }
	        $desde = ( $select_pag -1 ) * $limite;
	        if ($total>0){
	            for ($i=1;$i<=$total;$i++){
	                if ($select_pag == $i)
	                    $MiTemplate->set_var('selected','selected');
	                else
	                    $MiTemplate->set_var('selected','');
	                $MiTemplate->set_var('pag',$i);
	                $MiTemplate->parse("PBLPaginas", "Paginas", true);
	            }
				if ($res_count['cuenta']<$limite){
					$MiTemplate->set_var('inicio','<!--');
					$MiTemplate->set_var('fin','-->');
				}
	        }
	        else{
	                $MiTemplate->set_var('pag',1);
					$MiTemplate->set_var('inicio','<!--');
					$MiTemplate->set_var('fin','-->');
	                $MiTemplate->parse("PBLPaginas", "Paginas", true);
	        }

	/*//////////////////////////////////////////////////////////////////////////////////////////*/
	//Valida los Filtros de Busqueda//

	//busqueda de os u ot, con número
	    $MiTemplate->set_var("texto_osot",$texto_osot);
	    $MiTemplate->set_var("radioE",$radioE);

	/* recupera los valores en la primera búsqueda*/
	if ($radioE=="os"){
		$checkedS="checked";
		$MiTemplate->set_var("checkedS",$checkedS);
	    $MiTemplate->set_var("radioE",$radioE);
		$MiTemplate->set_var("texto_osot",$texto_osot);
		$where0= " AND LO.idLista_OS_enc = ".$texto_osot;
	}
	if ($radioE=="ot"){
		$checkedT="checked";
		$MiTemplate->set_var("checkedT",$checkedT);
	    $MiTemplate->set_var("radioE",$radioE);
		$MiTemplate->set_var("texto_osot",$texto_osot);
		$where0= " AND OT.ot_idList = ".$texto_osot;
	}

	/* si viene vacio el radio*/	/* si viene vacio el texto*/
	if (($radioE=='')||($texto_osot=='')){
			$MiTemplate->set_var("checkedS","checked");
			$where0= " ";
	}
	$MiTemplate->set_var("select_estado",$select_estado);
	$MiTemplate->set_var("id_estado",$id_estado);

	// Recupera el SELECT del ordenamiento
	if ($select4 == TEXT_CAMPO_5){
		$order0= " ORDER BY ot_idList";
		$MiTemplate->set_var("selected1","selected");
	}
	if ($select4 == TEXT_CAMPO_6){
		$order0= " ORDER BY idLista_OS_enc";
		$MiTemplate->set_var("selected2","selected");
	}
	if ($select4 == TEXT_CAMPO_7){
		$order0= " ORDER BY ot_listTipo";
		$MiTemplate->set_var("selected3","selected");
	}
	if ($select4 == TEXT_CAMPO_8){
		$order0= " ORDER BY nombre";
		$MiTemplate->set_var("selected4","selected");
	}
	if ($select4 == TEXT_CAMPO_9){
		$order0= " ORDER BY ot_listFeccrea";
		$MiTemplate->set_var("selected5","selected");
	}
	if ($select4 == TEXT_CAMPO_10){
		$order0= " ORDER BY esta_nombre";
		$MiTemplate->set_var("selected6","selected");
	}
	// Muestra el listado de las OT
	$MiTemplate->set_block("main", "Lista_OT", "PBLLista_OT");

    $query = "SELECT DISTINCT OT.ot_idList, LO.idLista_OS_enc, OT.ot_listFeccrea, LR.fec_Evento, OT.ot_listTipo, ES.esta_nombre, OT.ot_listTiendaPago, LR.festejado, LR.descripcion,TD.nombre, DR.dire_direccion, DR.dire_telefono, DR.dire_localizacion, LE.idLista_enc, LR.clie_Rut, OT.ot_listNumImp, OT.ot_usuAutoriza, IF(OT.ot_idEstado='PT','#EE1B24','') AS color
				FROM list_ot OT
				LEFT JOIN list_os_det LO ON LO.OS_idOT = OT.ot_idList
				LEFT JOIN list_os_enc LE ON LE.idLista_OS_enc = LO.idLista_OS_enc
				LEFT JOIN list_regalos_det LD ON LD.idLista_det = LO.idLista_det
				LEFT JOIN list_regalos_enc LR ON LR.idLista = LD.idLista_enc
				LEFT JOIN direcciones DR ON DR.id_direccion = LR.id_Direccion
				LEFT JOIN tipos_despacho TD ON TD.id_tipodespacho = LD.list_idTipodespacho
				LEFT JOIN estados ES ON ES.id_estado = OT.ot_idEstado
                LEFT JOIN usuarios US ON USR_LOGIN = OT.ot_usuAutoriza
                WHERE 1 AND OT.ot_idEstado NOT IN ('EC', 'ER') AND OT.ot_listTipo IN ('PS') AND LE.OS_local='".$local_id."'
                $where0 $order0
				LIMIT ".($desde+0).",".($limite+0);
		if ( $rq = tep_db_query($query) ){

			while( $res = tep_db_fetch_array( $rq ) ) {
	            $MiTemplate->set_var('ot_idList', ($res['ot_idList']?$res['ot_idList']:"N/I"));
	            $MiTemplate->set_var('fec_creacion', ($res['ot_listFeccrea']?fecha_db2php($res['ot_listFeccrea']):"N/I"));
	            $MiTemplate->set_var('estado', ($res['esta_nombre']?$res['esta_nombre']:"N/I"));
	            $MiTemplate->set_var('nombre_despacho', ($res['nombre']?$res['nombre']:"N/I"));
	            $MiTemplate->set_var('id_OS', ($res['idLista_OS_enc']?$res['idLista_OS_enc']:"N/I"));
		     $MiTemplate->set_var("color", $res['color']);
	            $MiTemplate->set_var('NumImp', $res['ot_listNumImp']);

				$Ver = "<a href=\"detalleOT_Regalos.php?id_OT=".$res['ot_idList']."\" class=\"link1\">Ver</a>";
				$MiTemplate->set_var('ver', $Ver);

		        //Establece el Tipo de Orden de Trabajo
				if($res['ot_listTipo'] == 'PS')
					$tipoOT = 'Producto Stock';
				else if ($res['ot_listTipo'] == 'SV')
					$tipoOT = 'Servicio';
				else if ($res['ot_listTipo'] == 'PE')
					$tipoOT = 'Pedido Especial';
				$MiTemplate->set_var('OT_tipo', ($tipoOT ? $tipoOT : "N/I"));

	            $MiTemplate->parse("PBLLista_OT", "Lista_OT", true);
	        }
	    }

	// Agregamos el footer
	include "../../includes/footer_cproy.php";

    $MiTemplate->pparse("OUT_H", array("header"), false);
    include "../../menu/menu.php";
    $MiTemplate->parse("OUT_M", array("main","footer"), true);
    $MiTemplate->p("OUT_M");
}

/**********************************************************************************************/
switch($accion){
	default:
		DisplayListado();
		break;
}

/**********************************************************************************************/

include "../../includes/application_bottom.php";

?>
