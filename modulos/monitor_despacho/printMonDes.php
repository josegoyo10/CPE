<?
$pag_ini = '../monitor_despacho/monitor_despacho.php';
include "../../includes/aplication_top.php";

/****************************************************************
 *
 * Despliega Listado Monitor Despacho
 *
 ****************************************************************/
function DisplayListado() {
    global $ses_usr_id;
    global $texto_osot,$radioE,$busqueda,$select_tipo,$select_tipo,$select_des,$select_estado,
	$select4,$orden,$select_fecha;

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
    $MiTemplate->set_file("main","monitor_despacho/monitor_ot_ps_print.htm");
	//busqueda de os u ot, con número
	$MiTemplate->set_var("num_bus1",$texto_osot);
    $MiTemplate->set_var("radio_bus1",$radioE);
    $MiTemplate->set_var("busqueda",$busqueda);
    $MiTemplate->set_var("select_tipo",$select_tipo);
    $MiTemplate->set_var("id_estado",$id_estado);

	//Recuperamos los estados
    $MiTemplate->set_block("main", "Estados", "BLO_estados");

		$query_E="select if(e2.esta_nombre is null, concat('''', e1.id_estado, ''''), concat('''',e1.id_estado, ''',''', e2.id_estado, '''')) id_estado, e1.esta_nombre
		from estados e1 left join estados e2 on e1.esta_nombre=e2.esta_nombre and
		  e2.esta_tipo = 'TE' and e2.id_estado not in ('EC', 'EP', 'ET')
		where e1.esta_tipo = 'TP'
		UNION
		select if(e1.esta_nombre is null, concat('''', e2.id_estado, ''''), concat('''', e1.id_estado, ''',''', e2.id_estado, '''')) id_estado, e2.esta_nombre
		from estados e1 right join estados e2 on e1.esta_nombre=e2.esta_nombre and
		  e1.esta_tipo = 'TP'
		where
		  e2.esta_tipo = 'TE' and e2.id_estado not in ('EC', 'EP', 'ET')";

	query_to_set_var( $query_E, $MiTemplate, 1, 'BLO_estados', 'Estados' );
   //Recuperamos los despachos
    $MiTemplate->set_block("main", "Despachos", "BLO_des");
	  $query_Des = "select id_tipodespacho, nombre , if('$select_des'=id_tipodespacho, 'selected', '') 'selected' from tipos_despacho order by nombre";
    query_to_set_var( $query_Des, $MiTemplate, 1, 'BLO_des', 'Despachos' );   

	//primera busqueda
	$auxosot="";
	if($busqueda != "")
		if ($radioE== "ot"){
			$auxosot= " and ot.ot_id=".$texto_osot."";
		}else {
			$auxosot= " and ot.id_os=".$texto_osot."";
		}

	//ordenamiento
	$orden1="";
	if($orden != ""){
		$order1 = ($select4)?" order by $select4":" order by 1";
		$MiTemplate->set_var("selected4".$select4,"selected");
	}

//$MiTemplate->set_block("main", "Lista", "BLO_lista");
$select_ti="'".$select_tipo."'";
$select_es="(".$select_estado.")";
$where1 = ($select_tipo)?" and ot_tipo = " . ($select_ti) :"";
$where2 = ($select_des)?" and ot.id_tipodespacho = " . ($select_des) :"";
$where3 = ($select_estado)?" and e.id_estado in " . ($select_es) :" ";
$where4 = ($select_fecha)?" and ot_fechacreacion >= '$select_fecha 00:00:00' and ot_fechacreacion <= '$select_fecha 23:59:59'":"";
 
	 $query="SELECT ot.ot_id, ot.id_os as id_os, ot_tipo, td.nombre as nombre_despacho , ot_fechacreacion,esta_nombre,ot.id_estado,date_format(ot_fechacreacion, '%d/%m/%Y  %H:%m:%s')OTF, os.id_local,  nom_local,ot.id_tipodespacho as tipoDes,ot.id_estado,esta_tipo
	FROM ot JOIN os on os.id_os = ot.id_os 
	JOIN estados e on e.id_estado = ot.id_estado 
	JOIN locales l on l.id_local = os.id_local 
	JOIN tipos_despacho td on td.id_tipodespacho = ot.id_tipodespacho 
	where ot_tipo in ('PE','PS') $where1 $where2 $where3 $where4 $auxosot $order1";

   //Recuperamos las fechas
	$MiTemplate->set_block("main", "Fecha_Creacion", "BLO_cre");
	$queryf = "select distinct date_format(ot_fechacreacion, '%Y-%m-%d') as fechat , DATE_FORMAT( ot_fechacreacion, '%d/%m/%Y') as fecha , if (DATE_FORMAT( ot_fechacreacion, '%Y-%m-%d')='$select_fecha', 'selected', '') selected from ot  where ot_fechacreacion is not null $where1 $where2 $where3 order by 1";
	query_to_set_var( $queryf, $MiTemplate, 1, 'BLO_cre', 'Fecha_Creacion' );

    $MiTemplate->set_block("main", "lista", "PBLlistado");

	if ( $rq = tep_db_query($query) ){
        while( $res = tep_db_fetch_array( $rq ) ) {
            $MiTemplate->set_var('id_os',tohtml( $res['id_os']));
            $MiTemplate->set_var('id_ot',tohtml( $res['ot_id']));
			$ot_tipo="'".$res['ot_tipo']."'";
            $MiTemplate->set_var('ot_tipo',tohtml($ot_tipo));
            $MiTemplate->set_var('tipoDes',tohtml( $res['tipoDes']));
			$esta_tipo="'".$res['esta_tipo']."'";
            $MiTemplate->set_var('esta_tipo',tohtml( $esta_tipo));
            $MiTemplate->set_var('nombre_despacho',tohtml( $res['nombre_despacho']));
            $MiTemplate->set_var('local',tohtml( $res['nom_local']));
            $MiTemplate->set_var('OTF',tohtml( $res['OTF']));
            $MiTemplate->set_var('ot_fechacreacion',tohtml( $res['ot_fechacreacion']));
            $MiTemplate->set_var('desc_prod',tohtml( $res['osde_descripcion']));
            $MiTemplate->set_var('esta_nombre',tohtml( $res['esta_nombre']));
			$id_estado="'". $res['id_estado']."'";
            $MiTemplate->set_var('id_estado',tohtml($id_estado));
            $MiTemplate->parse("PBLlistado", "lista", true);   
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
switch($req){
	default:
		DisplayListado();
		break;
}

/**********************************************************************************************/

include "../../includes/application_bottom.php";

?>