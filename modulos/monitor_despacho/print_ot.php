<?
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
	$select4,$orden,$select_fecha,$NUM,$RAD_IO,$ORDEN,$TO,$TD,$OTF,$EST,$id_ot,$id_estado;

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
/*    $MiTemplate->set_file("header","header_ident.html");*/

    // Agregamos el main
    $MiTemplate->set_file("main","monitor_despacho/print_ot.htm");
	// Query de la OT
	$query = "	SELECT ot_id, id_os, ot_fechacreacion, e.esta_nombre, ot_tipo
				FROM ot JOIN estados e on e.id_estado = ot.id_estado
				WHERE ot.ot_id =".($id_ot+0);

	$rq = tep_db_query($query);
	$res = tep_db_fetch_array( $rq );
	$MiTemplate->set_var('ot_tipo',($res['ot_tipo']=='PE')?$res['ot_tipo'] . " - Producto Especial":$res['ot_tipo'] . " - Producto Stock");
    $MiTemplate->set_var('id_estado',tohtml( $id_estado));
    $MiTemplate->set_var('id_os',tohtml( $id_os));
	 $query="SELECT ot.ot_id, ot.id_os as id_os, ot_tipo, td.nombre as nombre_despacho , ot_fechacreacion,esta_nombre,ot.id_estado,date_format(ot_fechacreacion, '%d/%m/%Y  %H:%m:%s')OTF, os.id_local,  nom_local,ot.id_tipodespacho,ot.id_estado,esta_tipo
	FROM ot JOIN os on os.id_os = ot.id_os 
	JOIN estados e on e.id_estado = ot.id_estado 
	JOIN locales l on l.id_local = os.id_local 
	JOIN tipos_despacho td on td.id_tipodespacho = ot.id_tipodespacho 
	where ot_tipo in ('PS','PE') and  ot.ot_id=".$id_ot;
	if ( $rq = tep_db_query($query) ){
        while( $res = tep_db_fetch_array( $rq ) ) {
            $MiTemplate->set_var('id_os',tohtml( $res['id_os']));
			$id_ot=$res['ot_id'];
            $MiTemplate->set_var('id_ot',tohtml( $res['ot_id']));
			$MiTemplate->set_var('ot_fechacreacion',fecha_db2php(tohtml( $res['ot_fechacreacion'])));
            $MiTemplate->set_var('desc_prod',tohtml( $res['osde_descripcion']));
            $MiTemplate->set_var('esta_nombre',tohtml( $res['esta_nombre']));
        }           
    }
	// Query de la despaccho
    $queryDes = "SELECT distinct os.id_direccion ,dire_nombre,dire_direccion, dire_telefono, dire_observacion,comu_nombre ,t.nombre FROM os  
				JOIN direcciones	d on d.id_direccion	= os.id_direccion
				JOIN comuna			c on c.id_comuna	= d.id_comuna
				JOIN clientes		cl on cl.clie_rut	= os.clie_rut
				JOIN os_detalle		od on od.id_os	= os.id_os
				JOIN tipos_despacho t on t.id_tipodespacho = od.id_tipodespacho 
			WHERE od.ot_id= ".$id_ot;
	$rq = tep_db_query($queryDes);
	$resD = tep_db_fetch_array( $rq );
	
	$dirServ = consulta_localizacion($resD['id_direccion'],2);
	$dirServicio = getlocalizacion($dirServ);

	// Datos Dirección despacho
	$MiTemplate->set_var('diredes_nombre',tohtml( $resD['dire_nombre']));
	$MiTemplate->set_var('diredes_direccion',tohtml( $resD['dire_direccion']));
	$MiTemplate->set_var('diredes_telefono',tohtml( $resD['dire_telefono']));
	$MiTemplate->set_var('diredes_observacion',tohtml( $resD['dire_observacion']));
	$MiTemplate->set_var('diredes_comuna',tohtml( $dirServicio['barrio']." - ".$dirServicio['localidad']));
	$MiTemplate->set_var('tipodespacho',tohtml( $resD['nombre']));

	// Query del detalle de la OT
    $MiTemplate->set_block("main", "LISTADO_OT", "BLO_detalle");
    $query = "SELECT cod_barra, cod_sap, osde_descripcion, osde_precio, osde_cantidad, osde_tipoprod,osde_especificacion,cant_pickeada 
	,if(cant_pickeada='','0',cant_pickeada)cant_pickeada
	,if(osde_tipoprod='PE',osde_cantidad,cant_pickeada)cant_pickeada 
	FROM os_detalle osd
	JOIN tipos_despacho t on t.id_tipodespacho = osd.id_tipodespacho WHERE ot_id = $id_ot ";
	$quer1=$query;
    query_to_set_var( $query, $MiTemplate, 1, 'BLO_detalle', 'LISTADO_OT' ); 
	if ( $rq = tep_db_query($quer1) ){
        while( $res = tep_db_fetch_array( $rq ) ) {
			$total=$total+$res['osde_cantidad'];
		}
	}
	$MiTemplate->set_var('total',$total);

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