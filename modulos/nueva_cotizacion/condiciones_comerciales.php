<?
$pag_ini = '../nueva_cotizacion/nueva_cotizacion_00.php';
include "../../includes/aplication_top.php";

// include_idioma_mod( $ses_idioma, "nueva_cotizacion_0303" );
// include_idioma_mod( $ses_idioma, "monitor_cotizaciones" );

// *************************************************************************************

/** Inicio Acciones  **/
if ($accion == "editar") {
	if ($tipodesc == "porcent")
		$descuento = ($osde_precio_aux+0) * $osde_descuento/100;
	else
		$descuento = $osde_descuento;

	if (trim($prec_req))  {//Precio requerido
		if ($osde_precio_aux)
			$query =  "UPDATE os_detalle SET usrnomaut = '$usuarioaut', usrpassaut = '$claveaut', id_origen = ". ($select_origen+0).", osde_descuento = ". ($descuento+0).", osde_precio = ". ($osde_precio_aux+0)." WHERE id_os_detalle = " . ($id_os_detalle+0) ;
		else
			$query =  "UPDATE os_detalle SET usrnomaut = '$usuarioaut', usrpassaut = '$claveaut', id_origen = ". ($select_origen+0).", osde_descuento = ". ($descuento+0).", osde_precio = null WHERE id_os_detalle = " . ($id_os_detalle+0) ;
	}
	else
		$query =  "UPDATE os_detalle SET usrnomaut = '$usuarioaut', usrpassaut = '$claveaut', id_origen = ". ($select_origen+0).", osde_descuento = ". ($descuento+0)." WHERE id_os_detalle = " . ($id_os_detalle+0) ;

	tep_db_query($query);
	?>
	<SCRIPT LANGUAGE="JavaScript">
		window.returnValue = "reload";
		window.close();
	</SCRIPT>
	<?
	tep_exit();
}
/** Fin Acciones  **/

$MiTemplate = new Template();
// asignamos degug maximo
$MiTemplate->debug = 0;
// root directory de los templates
$MiTemplate->set_root(DIRTEMPLATES);
// variables perdidas
$MiTemplate->set_unknowns("remove");

// Agregamos el main
$MiTemplate->set_file("main","nueva_cotizacion/condiciones_comerciales.htm");

// Esto es en caso de que no exista previamente la os.
if (!$id_os && $clie_rut) 
	$id_os = $clie_rut*-1;

$query="SELECT osd.*
		FROM os_detalle osd 
		WHERE osd.id_os = " . ($id_os+0) . "
		ORDER BY osd.id_os_detalle desc
		limit $IndexSelected, 1";
$tdq_1 = tep_db_query($query);
$res_1 = tep_db_fetch_array( $tdq_1 );

$query_2="SELECT precio_mod 
		  FROM productos p join tipo_subtipo t on t.prod_tipo = p.prod_tipo and t.prod_subtipo = p.prod_subtipo 
		  WHERE id_producto = " . ($res_1['id_producto']+0) ; 
$tdq_2 = tep_db_query($query_2);
$res_2 = tep_db_fetch_array( $tdq_2 );

$MiTemplate->set_var("id_os_detalle",$res_1['id_os_detalle']);
$MiTemplate->set_var("usrnomaut",$res_1['usrnomaut']);
$MiTemplate->set_var("usrpassaut",$res_1['usrpassaut']);
$MiTemplate->set_var("osde_descripcion",$res_1['osde_descripcion']);
$MiTemplate->set_var("osde_precio",$res_1['osde_precio']);
$MiTemplate->set_var("osde_descuento",$res_1['osde_descuento']);
$MiTemplate->set_var("osde_tipoprod",$res_1['osde_tipoprod']);
$MiTemplate->set_var("disabled",($res_2['precio_mod'])?'':'disabled');
$MiTemplate->set_var("prec_mod",$res_2['precio_mod']);

$MiTemplate->set_block("main", "Origen", "BLO_orig");
$queryP = "SELECT o.id_origen, o.nombre, if(d.id_origen, 'selected', '') selected 
		   FROM origen_descuentos o LEFT JOIN os_detalle d ON d.id_origen = o.id_origen 
		   AND id_os_detalle = " . ($res_1['id_os_detalle']+0) ;
query_to_set_var( $queryP, $MiTemplate, 1, 'BLO_orig', 'Origen' );

$MiTemplate->parse("OUT_M", array("main"), true);
$MiTemplate->p("OUT_M");
