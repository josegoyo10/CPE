<?
$pag_ini = '../picking_tienda/index.php';
include "../../includes/aplication_top.php";

include_idioma_mod( $ses_idioma, "picking_tienda" );

/**********************************************************************************************/

function digiVer($r){
    $s=1;for($m=0;$r!=0;$r/=10)
    $s=($s+$r%10*(9-$m++%6))%11;
	return chr($s?$s+47:75);
}

/****************************************************************
 *
 * Despliega Listado Monitor OT Picking Tienda
 *
 ****************************************************************/
function DisplayListado() {
    global $ses_usr_id;
    global $select_estado, $select_local, $select_tipo, $chec_ot_os, $bus_os, $accion, $filtro, $orden;

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

    $MiTemplate->set_var("select_estado",$select_estado);
    $MiTemplate->set_var("select_local",$select_local);
    $MiTemplate->set_var("select_tipo",$select_tipo);
    $MiTemplate->set_var("bus_os",$bus_os);
    $MiTemplate->set_var("chec_ot_os",$chec_ot_os);
	
    
    $MiTemplate->set_var("BOTON_VER",BOTON_VER);
    $MiTemplate->set_var("TEXT_BOTON",TEXT_BOTON);
    $MiTemplate->set_var("TEXT_SELECT",TEXT_SELECT);
    
    // Agregamos el header
    $MiTemplate->set_file("header","header_ident.html");

    // Agregamos el main
    $MiTemplate->set_file("main","picking_tienda/listado.html");

    
	//Recuperamos los estados
    $MiTemplate->set_block("main", "Despacho", "BLO_Desp");
    $query_D = "SELECT id_tipodespacho, nombre, if('$select_tipo'=id_tipodespacho, 'selected', '') 'selected' FROM tipos_despacho";
    query_to_set_var( $query_D, $MiTemplate, 1, 'BLO_Desp', 'Despacho' );   
    
	//Estado Creada por default
	if ($select_estado == "")
		$select_estado = 'PC';

	//Recuperamos los estados
    $MiTemplate->set_block("main", "Estados", "BLO_esta");
    $query_E = "select id_estado, esta_nombre as estado, if('$select_estado'=id_estado, 'selected', '') 'selected' from estados where esta_tipo = 'TP' order by esta_nombre";
    query_to_set_var( $query_E, $MiTemplate, 1, 'BLO_esta', 'Estados' );   
    
	//Recuperamos el permiso por local
	if ( $mylocal = get_local_usr( $ses_usr_id ))
		$where_aux_local = " AND os.id_local = $mylocal ";
	else
	    $MiTemplate->set_var("ALL","<option value=''>TODAS</option>"); 

	//Recuperamos los Locales
    $MiTemplate->set_block("main", "Locales", "BLO_Loc");
	$queryL="SELECT os.id_local as id_local, os.nom_local as nom_local, if('$select_local'=os.id_local, 'selected', '') 'selected' 
			FROM locales os WHERE 1 
			$where_aux_local
			ORDER BY nom_local";
	query_to_set_var( $queryL, $MiTemplate, 1, 'BLO_Loc', 'Locales' );   
    //echo $queryL;
    
    if ($chec_ot_os == 2){
            $MiTemplate->set_var("checked1",'');
            $MiTemplate->set_var("checked2",'checked');
    }
    else{
            $chec_ot_os = 1;
            $MiTemplate->set_var("checked1",'checked');
            $MiTemplate->set_var("checked2",'');
    }
    
    //Filtros
    $where_aux = " and ot.id_estado = 'PC'";
    if ($select_tipo)
        $where_aux = " and td.id_tipodespacho = ".( $select_tipo + 0 );
    else if($select_local)
        $where_aux = " and l.id_local = ".( $select_local + 0 );
    else if ($bus_os){
        if ($chec_ot_os == 1)
                $where_aux = " and ot_id = ".( $bus_os + 0 );
        else if ($chec_ot_os == 2)
                    $where_aux = " and os.id_os = ".( $bus_os + 0 );
    }
    else if ($select_estado) 
        $where_aux = " and ot.id_estado = '".$select_estado."'";
	
    //Orden
    if ( $filtro == "" || $filtro == "ot" )
        $order_aux = "order by ot_id $orden";        
    else if( $filtro == "os" )
        $order_aux = "order by os.id_os $orden";        
    else if( $filtro == "fc" )
        $order_aux = "order by os_fechacreacion $orden";        
                
    if ( $orden == "" || $orden == "asc"){
        $MiTemplate->set_var("orden",'desc');
        $MiTemplate->set_var("BOTON_ARROW",BOTON_DOWN);
    }
    else if ( $orden == "desc" ){
        $MiTemplate->set_var("orden",'asc');
        $MiTemplate->set_var("BOTON_ARROW",BOTON_UP);
    }
    
    $MiTemplate->set_block("main", "listado", "PBLlistado");
    
    $query="SELECT ot.ot_id, ot.id_estado, ot.id_os as id_os, ot_tipo,date_format(ot_fechacreacion, '%d/%m/%Y') ot_fechacreacion, os.id_local, esta_nombre, nom_local, td.nombre as nombre_despacho
            FROM ot JOIN os	on os.id_os	= ot.id_os
					JOIN estados e	on e.id_estado	= ot.id_estado
					JOIN locales l	on l.id_local	= os.id_local
					JOIN tipos_despacho td on td.id_tipodespacho = ot.id_tipodespacho
 			WHERE ot_tipo = 'PS'
			$where_aux_local $where_aux $order_aux";

	//echo $query;

    //query_to_set_var( $query, $MiTemplate, 1, 'PBLModulos', 'Modulos' );   
    if ( $rq = tep_db_query($query) ){
        while( $res = tep_db_fetch_array( $rq ) ) {
            $MiTemplate->set_var('id_os',tohtml( $res['id_os']));
            $MiTemplate->set_var('id_ot',tohtml( $res['ot_id']));
            $MiTemplate->set_var('despacho',tohtml( $res['nombre_despacho']));
            $MiTemplate->set_var('local',tohtml( $res['nom_local']));
            $MiTemplate->set_var('ot_fechacreacion',tohtml( $res['ot_fechacreacion']));
            $MiTemplate->set_var('desc_prod',tohtml( $res['osde_descripcion']));
            $MiTemplate->set_var('esta_nombre',tohtml( $res['esta_nombre']));
            
            $MiTemplate->parse("PBLlistado", "listado", true);   
        }           
    }
    
    // Agregamos el footer
	include "../../includes/footer_cproy.php";

    $MiTemplate->pparse("OUT_H", array("header"), false);
    include "../../menu/menu.php";
    $MiTemplate->parse("OUT_M", array("main","footer"), true);
    $MiTemplate->p("OUT_M");
}


/****************************************************************
 *
 * Despliega Sumario de la OT de Picking
 *
 ****************************************************************/
function DisplaySumario(){

    global $ses_usr_id;
    global $id_ot;

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

    // Agregamos el header
    $MiTemplate->set_file("header","header_ident.html");

    // Agregamos el main
    $MiTemplate->set_file("main","picking_tienda/sumario.html");

	//Recuperamos el permiso por local
	if ( $mylocal = get_local_usr( $ses_usr_id ))
		$where_aux_local = " AND os.id_local = $mylocal ";

	// Query de la OT
    $query = "	SELECT ot_id, id_os, ot_fechacreacion, e.esta_nombre, ot_tipo
				FROM ot JOIN estados e on e.id_estado = ot.id_estado
				WHERE ot.ot_id = $id_ot ";
	$rq = tep_db_query($query);
	$res = tep_db_fetch_array( $rq );

	$id_os = $res['id_os'];

	$MiTemplate->set_var('id_ot',tohtml( $res['ot_id']));
	$MiTemplate->set_var('despacho',tohtml( $res['nombre_despacho']));
	$MiTemplate->set_var('ot_fechacreacion',fecha_db2php(tohtml( $res['ot_fechacreacion'])));
	$MiTemplate->set_var('esta_nombre',tohtml( $res['esta_nombre']));

	// Query de la OS
    $query = "SELECT os.id_local, esta_nombre, os_fechacotizacion, USR_NOMBRES, USR_APELLIDOS,
					 nom_local, os_descripcion, os_comentarios, proy_nombre, 
					 dire_nombre, dire_direccion, dire_telefono, dire_observacion, comu_nombre,
					 cl.clie_rut as clie_rut, clie_nombre,clie_tipo, clie_razonsocial,clie_paterno, clie_materno, clie_telefonocasa,
					 clie_telcontacto1, clie_telcontacto2
            FROM os
				JOIN estados		e on e.id_estado	= os.id_estado
				JOIN usuarios		u on u.USR_ID		= os.USR_ID
				JOIN locales		l on l.id_local		= os.id_local
				JOIN proyectos		p on p.id_proyecto	= os.id_proyecto
				JOIN direcciones	d on d.id_direccion	= os.id_direccion
				JOIN comuna			c on c.id_comuna	= d.id_comuna
				JOIN clientes		cl on cl.clie_rut	= os.clie_rut
			WHERE os.id_os = $id_os
			$where_aux_local";

	$rq = tep_db_query($query);
	$res = tep_db_fetch_array( $rq );

	$clie_rut = $res['clie_rut'];
	$MiTemplate->set_var('id_os',tohtml( $id_os));
	$MiTemplate->set_var('fcotizacion',fecha_db2php(tohtml( $res['os_fechacotizacion'])));
	$MiTemplate->set_var('estado_os',tohtml( $res['esta_nombre']));
	$MiTemplate->set_var('usuario',tohtml( $res['USR_NOMBRES']." ".$res['USR_APELLIDOS']));
	$MiTemplate->set_var('tienda',tohtml( $res['nom_local']));
	$MiTemplate->set_var('os_descripcion',tohtml( $res['os_descripcion']));
	$MiTemplate->set_var('os_comentarios',tohtml( $res['os_comentarios']));
	$MiTemplate->set_var('proy_nombre',tohtml( $res['proy_nombre']));

	$MiTemplate->set_var('dire_nombre',tohtml( $res['dire_nombre']));
	$MiTemplate->set_var('dire_direccion',tohtml( $res['dire_direccion']));
	$MiTemplate->set_var('dire_telefono',tohtml( $res['dire_telefono']));
	$MiTemplate->set_var('dire_observacion',tohtml( $res['dire_observacion']));
	$MiTemplate->set_var('dire_comuna',tohtml( $res['comu_nombre']));

	if ($res['clie_tipo']=='e'){
     $MiTemplate->set_var("empresa","Empresa");
     $MiTemplate->set_var("clie_razonsocial",$res['clie_razonsocial']);	 
	}
	$MiTemplate->set_var('clie_rut',tohtml( $res['clie_rut']) . "-" . digiVer($res['clie_rut']));
	$MiTemplate->set_var('clie_nombre',tohtml( $res['clie_nombre']));
	$MiTemplate->set_var('clie_paterno',tohtml( $res['clie_paterno']));
	$MiTemplate->set_var('clie_materno',tohtml( $res['clie_materno']));
	$MiTemplate->set_var('clie_telefonocasa',tohtml( $res['clie_telefonocasa']));
	$MiTemplate->set_var('clie_telcontacto1',($res['clie_telcontacto1'])?", " . tohtml( $res['clie_telcontacto1']):"");
	$MiTemplate->set_var('clie_telcontacto2',($res['clie_telcontacto2'])?", " . tohtml( $res['clie_telcontacto2']):"");
	$MiTemplate->set_var('clie_celular',tohtml( $res['clie_celular']));


	// Query de la direccion Permanente
    $query = "SELECT dire_nombre, dire_direccion, dire_telefono, dire_observacion, comu_nombre
            FROM direcciones d
				JOIN comuna	c on c.id_comuna = d.id_comuna
			WHERE clie_rut = $clie_rut
				AND dire_defecto = 'p'
			";

	//echo $query;

	$rq = tep_db_query($query);
	$res = tep_db_fetch_array( $rq );

	$MiTemplate->set_var('dirp_nombre',tohtml( $res['dire_nombre']));
	$MiTemplate->set_var('dirp_direccion',tohtml( $res['dire_direccion']));
	$MiTemplate->set_var('dirp_telefono',tohtml( $res['dire_telefono']));
	$MiTemplate->set_var('dirp_observacion',tohtml( $res['dire_observacion']));
	$MiTemplate->set_var('dirp_comuna',tohtml( $res['comu_nombre']));



	// Query del detalle de la OT
    $query = "SELECT cod_barra, cod_sap, osde_descuento,osde_descripcion, osde_precio, osde_cantidad
			  FROM os_detalle
			  WHERE ot_id = $id_ot ";

    $MiTemplate->set_block("main", "listado", "PBLlistado");

    if ( $rq = tep_db_query($query) ){
        while( $res = tep_db_fetch_array( $rq ) ) {
            $MiTemplate->set_var('cod_barra',tohtml( $res['cod_barra']));
            $MiTemplate->set_var('cod_sap',tohtml( $res['cod_sap']));
            $MiTemplate->set_var('descripcion',tohtml( $res['osde_descripcion']));
            $MiTemplate->set_var('precio',formato_precio(( $res['osde_precio'])));
if ($res['osde_descuento']!='')
            $MiTemplate->set_var('descuento',tohtml( ($res['osde_descuento']*$res['osde_cantidad'])));
else
            $MiTemplate->set_var('descuento',0);

            $MiTemplate->set_var('cantidad',tohtml( $res['osde_cantidad']));
            $MiTemplate->parse("PBLlistado", "listado", true);   
        }           
    }
    
	// Agregamos el footer
	include "../../includes/footer_cproy.php";

    $MiTemplate->pparse("OUT_H", array("header"), false);
    include "../../menu/menu.php";
    $MiTemplate->parse("OUT_M", array("main","footer"), true);
    $MiTemplate->p("OUT_M");

}



/****************************************************************
 *
 * Proceso de Actualización
 *
 ****************************************************************/
function Update(){
	global $idosde, $precio, $costo, $obs;

	$query = "UPDATE os_detalle
			  SET    osde_preciocosto	= '$costo',
					 osde_precio		= '$precio'
			  WHERE  id_os_detalle		= $idosde
			  ";

	if (!tep_db_query($query))
		echo "error: ".$query;
	else
	 header( "Location: index.php" );
}


/**********************************************************************************************/
switch($req){
	case "sum":
		DisplaySumario();
		break;
	case "upd":
		Update();
		break;	
	default:
		DisplayListado();
		break;
}

/**********************************************************************************************/

include "../../includes/application_bottom.php";

?>