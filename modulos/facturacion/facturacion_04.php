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
$MiTemplate->set_var("ini_coment","<!--");
$MiTemplate->set_var("fin_coment","-->");
$MiTemplate->set_var("ini_coment_nomov","<!--");
$MiTemplate->set_var("fin_coment_nomov","-->");

// Agregamos el header
$MiTemplate->set_file("header","header_ident.html");

// Agregamos el main
$MiTemplate->set_file("main","facturacion/facturacion_04.htm");

if ($accion=="buscar") {
	//Buscar un instalador o desplegar un mensaje si no existe
	$query1 = "
			SELECT id_instalador, inst_rut, concat(inst_nombre, ' ', inst_paterno, ' ', inst_materno) nombre 
			FROM instaladores 
			WHERE inst_rut = " . ($rut+0) ;
	$db1 = tep_db_query( $query1 );
	if ($res1 = tep_db_fetch_array( $db1 ) ) {
		$MiTemplate->set_var("id_instalador",$res1['id_instalador']);
		$MiTemplate->set_var("nombre",$res1['nombre']);
		$MiTemplate->set_var("ini_coment","");
		$MiTemplate->set_var("fin_coment","");

		//Para el detalle
		$MiTemplate->set_block("main", "Cartola", "BLO_cartola");
		$query_A="	SELECT DATE_FORMAT( fechatrx, '".DATE_FORMAT_S."') fechac, DATE_FORMAT( fechatrx, '".HORA_FORMAT_S."') horac, monto, descripcion, id_lote 
					FROM ctacte_instalador 
					WHERE id_instalador = " . $res1['id_instalador'] . " 
					ORDER BY fechatrx ASC";
		$db2 = tep_db_query( $query_A ); 
		$saldo = 0;
		$registros = 0; 
		while ($res2 = tep_db_fetch_array( $db2 )) {
			$registros = 1; 
			$saldo += $res2['monto'];
			$MiTemplate->set_var("fechac",$res2['fechac']); 
			$MiTemplate->set_var("horac",$res2['horac']); 
			$MiTemplate->set_var("cargo",($res2['monto']<0)?($res2['monto']*-1):"&nbsp;"); 
			$MiTemplate->set_var("abono",($res2['monto']>=0)?$res2['monto']:"&nbsp;"); 
/*			$MiTemplate->set_var("abono",($res2['monto']>0)?$res2['monto']:"&nbsp;"); */
			$MiTemplate->set_var("descripcion",$res2['descripcion']); 
			$MiTemplate->set_var("saldo",$saldo); 
			$MiTemplate->set_var("prefact",$res2['id_lote']); 
			$MiTemplate->parse("BLO_cartola", "Cartola", true); 
		} 
		if (!$registros) {
			$MiTemplate->set_var("ini_coment_nomov","");
			$MiTemplate->set_var("fin_coment_nomov","");
		}
	} 
	else {
		$MiTemplate->set_var("id_instalador", "0");
		$MiTemplate->set_var("nombre", "*** No existe el Instalador en el sistema ***");
	}
}

// Agregamos el footer
include "../../includes/footer_cproy.php";

$MiTemplate->pparse("OUT_H", array("header"), false);
include "../../menu/menu.php";
$MiTemplate->parse("OUT_M", array("main","footer"), true);
$MiTemplate->p("OUT_M");
include "../../includes/application_bottom.php";

?>