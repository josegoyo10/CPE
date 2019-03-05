<?
/* dalos permisos , ver como */
$pag_ini = '../facturacion/facturacion_00.php';
include "../../includes/aplication_top.php";

///////////////////// ACCIONES //////////////////////
//////////////////FIN ACCIONES //////////////////////

$MiTemplate = new Template();
$MiTemplate->debug = 0;
$MiTemplate->set_root(DIRTEMPLATES);
$MiTemplate->set_unknowns("remove");

$MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO);
$MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);
$MiTemplate->set_var("SUBTITULO1",TEXT_2);
$nombre_usuario_sesion = get_nombre_usr( $ses_usr_id ); 
$MiTemplate->set_var("USR_NOMBRE", $nombre_usuario_sesion);
$MiTemplate->set_var("numfact",$numfact);
$MiTemplate->set_var("monfact",$monfact);
$MiTemplate->set_var("numpref",$numpref);
$MiTemplate->set_var("rutcd",($rut)?$rut . "-" . dv($rut):'');


// Agregamos el header
$MiTemplate->set_file("header","header_ident.html");

if($id_lote){
	// Agregamos el main
	$MiTemplate->set_file("main","facturacion/facturacion_pf.htm");
	/*para el detalle de los servicios prefacturados*/
		$MiTemplate->set_block("main", "detallelote", "blo_lote");
		$qeryfd="select O.id_instalador,O.ot_id,O.ot_tipo,OD.osde_descripcion,OD.osde_precio,O.margenapagar,O.subtotalapagar, O.id_os ,if (OD.osde_descuento<>0,ROUND((OD.osde_precio-OD.osde_descuento)*OD.osde_cantidad),ROUND(OD.osde_precio*OD.osde_cantidad)) 'sub_total'
		from ot O 
		join os_detalle OD on OD.ot_id=O.ot_id
		join lote_instalador LI on O.id_lote=LI.id_lote
		where O.id_lote=".$id_lote;
		if ( $rq = tep_db_query($qeryfd) ){
			while( $res = tep_db_fetch_array( $rq ) ) {
				$MiTemplate->set_var("ot_id",$res['ot_id']);
				$MiTemplate->set_var("ot_tipo",$res['ot_tipo']);
				$MiTemplate->set_var("osde_descripcion",$res['osde_descripcion']);
				$MiTemplate->set_var("osde_precio",$res['osde_precio']);
				$MiTemplate->set_var("osde_precio_round",$res['sub_total']);
				$MiTemplate->set_var("margenapagar",$res['margenapagar']);
				$MiTemplate->set_var("subtotalapagar",$res['subtotalapagar']);
				$id_instalador=$res['id_instalador'];
				$MiTemplate->parse("blo_lote", "detallelote", true);
			}
		}

	/*para los datos del instalador*/
	$qry_ins="select distinct i.id_instalador,i.inst_rut,i.inst_nombre,i.inst_paterno,i.inst_materno,i.inst_telefono,i.direccion,i.email 		from instaladores i
	where i.id_instalador=".($id_instalador+0);
		$rq = tep_db_query($qry_ins);
		$res = tep_db_fetch_array( $rq );

		$MiTemplate->set_var('inst_rut',$res["inst_rut"]);
		$MiTemplate->set_var('id_instalador',$res["id_instalador"]);
		$MiTemplate->set_var('rut',$res["inst_rut"]."-".dv($res["inst_rut"]));
		$MiTemplate->set_var('inst_nombre',$res["inst_nombre"]);
		$MiTemplate->set_var('inst_paterno',$res["inst_paterno"]);
		$MiTemplate->set_var('telefono',$res["inst_telefono"]);
		$MiTemplate->set_var('direccion',$res["direccion"]);
		$MiTemplate->set_var('email',$res["email"]);
		$MiTemplate->set_var('nombre',$res["inst_nombre"]."&nbsp;".$res["inst_paterno"]);
	
	/*para los datos de la factura*/
	$qry_fac="select id_lote,estado,fechageneracion,usuarioingreso,monto_factura,retencion_lote,num_factura,numero1,numero2 from lote_instalador where id_lote=".$id_lote;
		$rq = tep_db_query($qry_fac);
		$res1 = tep_db_fetch_array( $rq );
		$MiTemplate->set_var('id_lote',$res1["id_lote"]);
			if ($res1["estado"]=='P'){
				$MiTemplate->set_var('estadofac','Pre-Facturado');
			}
			if ($res1["estado"]=='F'){
				$MiTemplate->set_var('estadofac','Facturado');
			}
		$MiTemplate->set_var('num_factura',$res1["num_factura"]);
		$MiTemplate->set_var('id_estado',$res1["estado"]);
		$MiTemplate->set_var('fechageneracion',fecha_db2php(tohtml( $res1['fechageneracion'])));
		$MiTemplate->set_var('usuarioingreso',$res1["usuarioingreso"]);
		$MiTemplate->set_var('monto_factura',$res1["monto_factura"]);
		$MiTemplate->set_var('retencion_lote',$res1["retencion_lote"]);
		$MiTemplate->set_var('numero1',$res1["numero1"]);
		$MiTemplate->set_var('numero2',$res1["numero2"]);
		$MiTemplate->set_var('subtotal',$res1["retencion_lote"]+$res1["monto_factura"]);

} 

// Agregamos el footer
include "../../includes/footer_cproy.php";

//$MiTemplate->pparse("OUT_H", array("header"), false);
$MiTemplate->parse("OUT_M", array("main"), true);
$MiTemplate->p("OUT_M");
include "../../includes/application_bottom.php";

?>