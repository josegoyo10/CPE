<?
$pag_ini = '../monitor_ot_sv/index.php';
include "../../includes/aplication_top.php";

/****************************************************************
 *
 * Despliega Listado Monitor Despacho
 *
 ****************************************************************/
function DisplayListado() {
    global $ses_usr_id,$select_pag,$limite ;
    global $texto_osot,$busqueda,$select_tipo,$select_tipo,$select_des,$select_estado,$select_local,$select4,$orden,$select_fecha,$select_fechar,$impresion,$where0,$radioE,$checkedS,$checkedT,$anoselect,$messelect,$radioR;

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
    $MiTemplate->set_file("main","monitor_ot_sv/monitor_ot_sv.htm");
	$MiTemplate->set_var("texto_osot",$texto_osot);
	$MiTemplate->set_var("radioE",$radioE);

	//Recuperamos el permiso por local
	if ( $mylocal = get_local_usr( $ses_usr_id ))
		$where_aux_local = " AND os.id_local = $mylocal ";
	else
	    $MiTemplate->set_var("ALL","<option value=''>TODAS</option>"); 
	//Recuperamos los Locales
    $MiTemplate->set_block("main", "Locales", "BLO_Loc");
	$queryL="SELECT id_local as id_local, nom_local as nom_local, if('$select_local'=id_local, 'selected', '') 'selected' 
			FROM locales os WHERE 1 
			$where_aux_local
			ORDER BY nom_local";
    query_to_set_var( $queryL, $MiTemplate, 1, 'BLO_Loc', 'Locales' );   

	//Para Rango Fechas, Recuperamos los años disponibles
    $MiTemplate->set_block("main", "AnoDisp", "BLO_AnoDisp");
	$query_A="SELECT distinct date_format(ot_fechacreacion, '%Y') as fecha, if (DATE_FORMAT( ot_fechacreacion, '%Y')='$anoselect', 'selected', '') selected 
			FROM ot join os on os.id_os = ot.id_os 
			WHERE ot_fechacreacion is not null and ot_tipo in ('SV') $where_aux_local 
			ORDER BY 1";
	query_to_set_var( $query_A, $MiTemplate, 1, 'BLO_AnoDisp', 'AnoDisp' );

	$MiTemplate->set_var("selectedM" . $messelect, "selected"); 
	$MiTemplate->set_var("messelect", $messelect); 
	$MiTemplate->set_var("anoselect", $anoselect); 

	//Para el Rango Monitor OT
	$radioR = ($radioR)?$radioR:"T";
	$MiTemplate->set_var("radioR", $radioR); 
	$MiTemplate->set_var("checkedR" . $radioR, "checked"); 

	/* recupera los valores en la primera búsqueda*/	
	if ($radioE=="os"){
		$checkedS="checked";
		$MiTemplate->set_var("checkedS",$checkedS);
	    $MiTemplate->set_var("radioE",$radioE);
		$MiTemplate->set_var("texto_osot",$texto_osot);
		$where0= " and ot.id_os=".$texto_osot;
	}	
	if ($radioE=="ot"){
		$checkedT="checked";
		$MiTemplate->set_var("checkedT",$checkedT);
	    $MiTemplate->set_var("radioE",$radioE);
		$MiTemplate->set_var("texto_osot",$texto_osot);
		$where0= " and ot.ot_id= ".$texto_osot;
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
	$MiTemplate->set_var("select_estado",$select_estado);
	$MiTemplate->set_var("id_estado",$id_estado);
	$MiTemplate->set_var("num_bus1",$texto_osot);
    $MiTemplate->set_var("radio_bus1",$radioE);
    $MiTemplate->set_var("select4",$select4);
    $MiTemplate->set_var("selected4".$select4,"selected");
	$MiTemplate->set_var("select_des",$select_des);
    $MiTemplate->set_var("select_fecha",$select_fecha);
    $MiTemplate->set_var("id_tipodespacho",$id_tipodespacho);
    $MiTemplate->set_var("select_tipo",$select_tipo);

	//Recuperamos los estados
    $MiTemplate->set_block("main", "Estados", "BLO_estados");
	$query_E="	SELECT id_estado, esta_nombre, if ('$select_estado' = id_estado, 'selected', '') selected 
				FROM estados
				WHERE esta_tipo = 'TV'
				ORDER BY orden";
	query_to_set_var( $query_E, $MiTemplate, 1, 'BLO_estados', 'Estados' );

	//Para el orden de la query ppal
	$select4 = ($select4)?$select4:"5";
	$MiTemplate->set_var("selected4".$select4,"selected");

	$select_ti="'".$select_tipo."'";
	$where1 = ($select_tipo)?" and ot_tipo = " . ($select_ti) :"";
	$where2 = ($select_des)?" and ot.id_tipodespacho = " . ($select_des) :" ";
	$where3 = ($select_estado)?" and ot.id_estado = '" . ($select_estado) ."'":" ";
	$where4 = ($select_fecha)?" and ot_fechacreacion >= '$select_fecha 00:00:00' and ot_fechacreacion <= '$select_fecha 23:59:59'":" ";
	$where6 = ($select_fechar)?" and ot_freactivacion >= '$select_fechar 00:00:00' and ot_freactivacion <= '$select_fechar 23:59:59'":" ";
    $where7 = ($select_local)?" and os.id_local = ".( $select_local + 0 ):" ";
	$where8 = ($messelect)?" and date_format(ot_fechacreacion, '%m') = $messelect":" ";
	$where9 = ($anoselect)?" and date_format(ot_fechacreacion, '%Y') = $anoselect":" ";
	$where10 = ($radioR == 'T')?" and ot_freactivacion < adddate(now(), -1) AND e.estadoterminal=0":$where10; //ALERTAS
	$where10 = ($radioR == 'A')?" and ot_freactivacion >= adddate(now(), -1) AND ot_freactivacion <= now() AND e.estadoterminal=0":$where10; //ACTIVAS
	$where10 = ($radioR == 'I')?" and (ot_freactivacion > now() OR e.estadoterminal = 1)":$where10; //INACTIVAS
	$order1 = " order by $select4";

	////paginacion/////////////////////////////////////////////////////////////////////////////////////////////////////////

		 $MiTemplate->set_block("main", "Paginas", "PBLPaginas");
        //Largo de los resultados por pantalla
        $limite = MONITOR_LI_SV;

	$query_count = "SELECT count(*) as cuenta 
			FROM ot JOIN os on os.id_os = ot.id_os 
					JOIN estados e on e.id_estado = ot.id_estado 
					JOIN locales l on l.id_local = os.id_local 
					JOIN tipos_despacho td on td.id_tipodespacho = ot.id_tipodespacho 
			WHERE ot_tipo in ('SV') $where0 $where1 $where2 $where3 $where4 $where6 $where7 $where8 $where9 $where10 $where_aux_local";
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

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    $MiTemplate->set_block("main", "lista", "PBLlistado");
	$query="SELECT ot.ot_id, ot.id_os as id_os, nom_local,date_format(ot_fechacreacion, '%d/%m/%Y  %H:%m:%s') ot_fechacreacion, date_format(ot_freactivacion, '%d/%m/%Y') ot_freactivacion, esta_nombre, if (e.estadoterminal = 1, 'green', if (ot_freactivacion < adddate( now(), -1), 'red', 'black')) color 
			FROM ot JOIN os on os.id_os = ot.id_os 
					JOIN estados e on e.id_estado = ot.id_estado 
					JOIN locales l on l.id_local = os.id_local 
					JOIN tipos_despacho td on td.id_tipodespacho = ot.id_tipodespacho 
			WHERE ot_tipo in ('SV') $where0 $where1 $where2 $where3 $where4 $where6 $where7 $where8 $where9 $where10 $where_aux_local $order1  LIMIT ".($desde+0).",".($limite+0);
	query_to_set_var( $query, $MiTemplate, 1, 'PBLlistado', 'lista' );

   //Recuperamos las fechas de creacion
	$MiTemplate->set_block("main", "Fecha_Creacion", "BLO_cre");
	$queryf="SELECT distinct date_format(ot_fechacreacion, '%Y-%m-%d') as fechat, DATE_FORMAT( ot_fechacreacion, '%d/%m/%Y') as fecha , if (DATE_FORMAT( ot_fechacreacion, '%Y-%m-%d')='$select_fecha', 'selected', '') selected 
			FROM ot JOIN os on os.id_os = ot.id_os 
					JOIN estados e on e.id_estado = ot.id_estado 
			WHERE ot_fechacreacion is not null and ot_tipo in ('SV') $where0 $where1 $where2 $where3 $where7 $where8 $where9 $where10 $where_aux_local 
			ORDER BY 1";
	query_to_set_var( $queryf, $MiTemplate, 1, 'BLO_cre', 'Fecha_Creacion' );

   //Recuperamos las fechas de reactivacion
	$MiTemplate->set_block("main", "Fecha_Reactivacion", "BLO_reac");
	$queryf="SELECT distinct date_format(ot_freactivacion, '%Y-%m-%d') as fechat, DATE_FORMAT( ot_freactivacion, '%d/%m/%Y') as fecha , if (DATE_FORMAT( ot_freactivacion, '%Y-%m-%d')='$select_fechar', 'selected', '') selected 
			FROM ot JOIN os on os.id_os = ot.id_os 
					JOIN estados e on e.id_estado = ot.id_estado 
			WHERE ot_freactivacion is not null and ot_tipo in ('SV') $where0 $where1 $where2 $where3 $where7 $where8 $where9 $where10 $where_aux_local 
			ORDER BY 1";
	query_to_set_var( $queryf, $MiTemplate, 1, 'BLO_reac', 'Fecha_Reactivacion' );
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