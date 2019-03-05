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

/***********************/

$MiTemplate = new Template();
// asignamos degug maximo
$MiTemplate->debug = 0;
// root directory de los templates
$MiTemplate->set_root(DIRTEMPLATES);
// variables perdidas
$MiTemplate->set_unknowns("remove");

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
    //$MiTemplate->set_file("main","picking_tienda/sumario.html");
	$MiTemplate->set_file("main","picking_tienda/print1.htm");

	// Query de la OT
    $query = "	SELECT ot_id, id_os, ot_fechacreacion, e.esta_nombre, ot_tipo, td.nombre
				FROM ot JOIN estados e on e.id_estado = ot.id_estado
				JOIN tipos_despacho td on td.id_tipodespacho = ot.id_tipodespacho
				WHERE ot.ot_id = $id_ot ";
	$rq = tep_db_query($query);
	$res = tep_db_fetch_array( $rq );

	$id_os = $res['id_os'];

	$MiTemplate->set_var('id_ot',tohtml( $res['ot_id']));
	$MiTemplate->set_var('despacho',tohtml( $res['nombre_despacho']));
	$MiTemplate->set_var('ot_fechacreacion',fecha_db2php(tohtml( $res['ot_fechacreacion'])));
	$MiTemplate->set_var('esta_nombre',tohtml( $res['esta_nombre']));
	$MiTemplate->set_var('desp_nombre',tohtml( $res['nombre']));

	// Query de la OS
    $query = "SELECT os.id_local, esta_nombre, os_fechacotizacion, USR_NOMBRES, USR_APELLIDOS,
					 os_descripcion, os_comentarios, proy_nombre, 
					 dire_nombre, dire_direccion, dire_telefono, dire_observacion, comu_nombre,
					 cl.clie_rut as clie_rut, clie_tipo,clie_razonsocial,clie_nombre, clie_paterno, clie_materno, clie_telefonocasa,
					 clie_telcontacto1, clie_telcontacto2
            FROM os
				JOIN estados		e on e.id_estado	= os.id_estado
				JOIN usuarios		u on u.USR_ID		= os.USR_ID
				JOIN locales		l on l.id_local		= os.id_local
				JOIN proyectos		p on p.id_proyecto	= os.id_proyecto
				JOIN direcciones	d on d.id_direccion	= os.id_direccion
				JOIN comuna			c on c.id_comuna	= d.id_comuna
				JOIN clientes		cl on cl.clie_rut	= os.clie_rut
			WHERE id_os = $id_os
			";

	$rq = tep_db_query($query);
	$res = tep_db_fetch_array( $rq );

	$clie_rut = $res['clie_rut'];
	$MiTemplate->set_var('id_os',tohtml( $id_os));
	$MiTemplate->set_var('fcotizacion',fecha_db2php(tohtml( $res['os_fechacotizacion'])));
	$MiTemplate->set_var('estado_os',tohtml( $res['esta_nombre']));
	$MiTemplate->set_var('usuario',tohtml( $res['USR_NOMBRES']." ".$res['USR_APELLIDOS']));
	$MiTemplate->set_var('tienda',tohtml( $res['local_nombre']));
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
    $query = "SELECT cod_barra, cod_sap, osde_descripcion, osde_precio, osde_cantidad
            FROM os_detalle
			WHERE ot_id = $id_ot
			";


    $MiTemplate->set_block("main", "listado", "PBLlistado");


//echo $query;

    if ( $rq = tep_db_query($query) ){
        while( $res = tep_db_fetch_array( $rq ) ) {
            $MiTemplate->set_var('cod_barra',tohtml( $res['cod_barra']));
            $MiTemplate->set_var('cod_sap',tohtml( $res['cod_sap']));
            $MiTemplate->set_var('descripcion',tohtml( $res['osde_descripcion']));
            $MiTemplate->set_var('precio',formato_precio(tohtml( $res['osde_precio'])));
            $MiTemplate->set_var('cantidad',tohtml( $res['osde_cantidad']));
            $MiTemplate->parse("PBLlistado", "listado", true);   
        }           
    }
    
// Agregamos el header
$MiTemplate->set_file("header","header_sc.html");

// Agregamos el main
//$MiTemplate->set_file("main","picking_tienda/print1.htm");

$MiTemplate->pparse("OUT_H", array("header"), false);
$MiTemplate->parse("OUT_M", array("main"), true);
$MiTemplate->p("OUT_M");

include "../../includes/application_bottom.php";
?>