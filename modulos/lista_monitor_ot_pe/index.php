<?
$pag_ini = '../monitor_ot_pe/index.php';
include "../../includes/aplication_top.php";

/****************************************************************
 *
 * Despliega Listado Monitor Despacho
 *
 ****************************************************************/
function DisplayListado() {
    global $ses_usr_id, $select_pag,$limite;
    global $texto_osot,$busqueda,$select_tipo,$select_tipo,$select_des,$select_estado,$select_local,$select4,$orden,$select_fecha,$select_fechar,$impresion,$where0,$radioE,$checkedS,$checkedT,$anoselect,$messelect,$radioR,$texto_nocsap;
	$local_id = get_local_usr( $ses_usr_id );

    $MiTemplate = new Template();
    $MiTemplate->debug = 0;
    $MiTemplate->set_root(DIRTEMPLATES);
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
    $MiTemplate->set_file("main","lista_monitor_ot_pe/monitor_ot_pe.htm");
	$MiTemplate->set_var("texto_osot",$texto_osot);
	$MiTemplate->set_var("radioE",$radioE);

	//Recuperamos el permiso por local
	if ( $mylocal = get_local_usr( $ses_usr_id ))
		$where_aux_local = " AND LE.OS_local = $mylocal ";
	else
	    $MiTemplate->set_var("ALL","<option value=''>TODAS</option>"); 
	//Recuperamos los Locales
    $MiTemplate->set_block("main", "Locales", "BLO_Loc");
	$queryL="SELECT id_local as id_local, nom_local as nom_local, if('$select_local'=id_local, 'selected', '') 'selected' 
			FROM locales os WHERE 1 
			$where_aux_local
			ORDER BY nom_local";
    query_to_set_var( $queryL, $MiTemplate, 1, 'BLO_Loc', 'Locales' );   

	/* recupera los valores en la primera búsqueda*/	
	if ($radioE=="os"){
		$checkedS="checked";
		$MiTemplate->set_var("checkedS",$checkedS);
	    $MiTemplate->set_var("radioE",$radioE);
		$MiTemplate->set_var("texto_osot",$texto_osot);
		$where0= " AND OT.ot_idList=".$texto_osot;
	}	
	if ($radioE=="ot"){
		$checkedT="checked";
		$MiTemplate->set_var("checkedT",$checkedT);
	    $MiTemplate->set_var("radioE",$radioE);
		$MiTemplate->set_var("texto_osot",$texto_osot);
		$where0= " AND OT.ot_idList= ".$texto_osot;
	}

	/* si viene vacio el radio*/
	if ($radioE==''){
			$MiTemplate->set_var("checkedT","checked");
			$where0= " ";
	}
	/* si viene vacio el texto*/
	if ($texto_osot==''){
			$MiTemplate->set_var("checkedT","checked");
			$where0= " ";
	}

/*///////////////////////////////////////////////////para la paginacion////////////////////////////////////////*/    
		 $MiTemplate->set_block("main", "Paginas", "PBLPaginas");
        //Largo de los resultados por pantalla
        $limite = MONITOR_LI_PE;

	$query_count = "SELECT DISTINCT COUNT(*) as cuenta
			  FROM list_ot OT 
			  LEFT JOIN list_os_det LD ON LD.OS_idOT=OT.ot_idList 
			  LEFT JOIN list_os_enc LE ON LE.idLista_OS_enc=LD.idLista_OS_enc 
		         WHERE OT.ot_listTipo in ('PE') AND OT.ot_idEstado IN ('EC', 'PT') AND LE.OS_local='".$local_id."'
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
    $MiTemplate->set_block("main", "Lista", "PBLlistado");
	$query="SELECT DISTINCT OT.ot_idList, LE.idLista_OS_enc, if(OT.no_OC_SAP<>0, OT.no_OC_SAP, '&nbsp;') AS no_OC_SAP, if(OT.no_TR_SAP<>0, OT.no_TR_SAP, '&nbsp;') AS no_TR_SAP, L.nom_local, date_format(LE.OS_fecCrea, '%Y-%m-%d') AS OS_fecCrea, E.esta_nombre, OT.ot_idEstado, IF(OT.ot_idEstado='PT','#EE1B24','') AS color 
			FROM list_ot OT 
			LEFT JOIN list_os_det LD ON LD.OS_idOT=OT.ot_idList 
			LEFT JOIN list_os_enc LE ON LE.idLista_OS_enc=LD.idLista_OS_enc 
			LEFT JOIN locales L ON L.id_local=LE.OS_local 
			LEFT JOIN estados E ON E.id_estado=OT.ot_idEstado 
			WHERE OT.ot_listTipo IN ('PE') AND LE.OS_local='".$local_id."' AND OT.ot_idEstado IN ('EC', 'PT', 'ER') 
			$where0 LIMIT ".($desde+0).",".($limite+0);	
			$res = tep_db_query($query);
			while($result = tep_db_fetch_array( $res )){
				$MiTemplate->set_var("ot_idList", $result['ot_idList']);
				$MiTemplate->set_var("idLista_OS_enc", $result['idLista_OS_enc']);
				$MiTemplate->set_var("no_OC_SAP", $result['no_OC_SAP']);
				$MiTemplate->set_var("no_TR_SAP", $result['no_TR_SAP']);
				$MiTemplate->set_var("nom_local", $result['nom_local']);
				$MiTemplate->set_var("OS_fecCrea", $result['OS_fecCrea']);
				$MiTemplate->set_var("esta_nombre", $result['esta_nombre']);
				$MiTemplate->set_var("ot_idEstado", $result['ot_idEstado']);
				$MiTemplate->set_var("color", $result['color']);
			}
	query_to_set_var( $query, $MiTemplate, 1, 'PBLlistado', 'Lista' );
	// Agregamos el footer
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