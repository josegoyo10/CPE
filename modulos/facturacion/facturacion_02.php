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

// Agregamos el main
$MiTemplate->set_file("main","facturacion/facturacion_02.htm");
global $id_lote;
/* para el caso de llevar solamente el rut, que venga de prefacturación*/
if (($accion=='buscar1')){
	$accion='buscar';
}

if ($accion=="buscar" || $accion=="ingresar") {
	/* para el caso de ingresar factura*/
	if($id_lote){
		$numpref=$id_lote;
		/*para los datos de la factura*/
		$qry_fac="select id_lote,estado,fechageneracion,usuarioingreso,monto_factura,retencion_lote from lote_instalador where id_lote=".$id_lote;
		$rq = tep_db_query($qry_fac);
		$res = tep_db_fetch_array( $rq );
			$MiTemplate->set_var("monfact",$res['monto_factura']);
			$MiTemplate->set_var("numpref",$res['id_lote']);
	
	}
	//Buscar un instalador o desplegar un mensaje si no existe
	$query1 = "
			SELECT id_instalador, inst_rut, concat(inst_nombre, ' ', inst_paterno, ' ', inst_materno) nombre 
			FROM instaladores 
			WHERE inst_rut = " . ($rut+0) ;
	$db1 = tep_db_query( $query1 );
	if ($res1 = tep_db_fetch_array( $db1 ) ) {
		$MiTemplate->set_var("id_instalador",$res1['id_instalador']);
		$MiTemplate->set_var("nombre",$res1['nombre']);
		if ($accion=="ingresar") {
			/* Hacemos la asociación de la factura con la prefactura */
			$query2 = " SELECT id_lote, inst_rut, estado, monto_factura, retencion_lote, concat(inst_nombre, ' ', inst_paterno, ' ', inst_materno) nombre
						FROM lote_instalador li join instaladores i ON i.id_instalador = li.id_instalador 
						WHERE id_lote = $numpref AND inst_rut = " . ($rut+0) ; 
				$db2 = tep_db_query( $query2 ) ; 
			if ($res2 = tep_db_fetch_array( $db2 ) ) { 
				if ($res2['estado'] != 'P') {
					$MiTemplate->set_var("mensaje","alert('La Pre-Factura $numpref ya ha sido previamente ingresada en el sistema');");				
				} 
				elseif ($res2['monto_factura'] != $monfact) {
					$MiTemplate->set_var("mensaje","alert('El monto de la Factura ($monfact) no concuerda con el valor almacenado en el sistema para la Pre-Factura $numpref');");				
				} 
				else {
				/*si el número de factura es distinto de cero*/
					/*if($numfact!=0){*/
						$queryUP = "UPDATE lote_instalador SET estado = 'F', fechafacturacion = now(), usuarioingreso = '$nombre_usuario_sesion', num_factura = $numfact WHERE id_lote = $numpref ";
						tep_db_query($queryUP);
						//Obtenemos las OT asociadas
						$query3 = " SELECT ot_id 
									FROM ot 
									WHERE id_lote = $numpref "; 
						$db3 = tep_db_query( $query3 ) ; 
						while ($res3 = tep_db_fetch_array( $db3 ) ) { 
							//Cambiamos estado de las OT asociadas
							$queryUP = "UPDATE ot SET id_estado='VF' WHERE ot_id= " . $res3['ot_id'];
							tep_db_query($queryUP);

							//Insertamos en historial
							insertahistorial("OT " . $res3['ot_id'] . " ha sido ingresada al sistema en factura: $numfact, Pre-factura: $numpref, Instalador: " . $res1['nombre'], 0, $res3['ot_id']);	
							insertahistorial("OT " . $res3['ot_id'] . " cambia a estado " . get_nombre_estado('VF'), 0, $res3['ot_id']);

							//Hacemos el abono en la cuenta corriente
							/*if ($res2['retencion_lote']) {
								$queryUP = "INSERT INTO ctacte_instalador VALUES(null, " . $res1['id_instalador'] . ", now(), " . $res2['retencion_lote'] . ", 'Abono de $" .$res2['retencion_lote']. " a cuenta del instalador " .$res1['nombre']. " generado desde lote de facturación número $numpref', $numpref, '$nombre_usuario_sesion', $ses_usr_id) ";
								tep_db_query($queryUP);
							}*/
						}
							//Hacemos el abono en la cuenta corriente
							if ($res2['retencion_lote']) {
								$queryUP = "INSERT INTO ctacte_instalador VALUES(null, " . $res1['id_instalador'] . ", now(), " . $res2['retencion_lote'] . ", 'Abono de $" .$res2['retencion_lote']. " a cuenta del instalador " .$res1['nombre']. " generado desde lote de facturación número $numpref', $numpref, '$nombre_usuario_sesion', $ses_usr_id) ";
								tep_db_query($queryUP);
							}

						$MiTemplate->set_var("id_instalador",0);
						$MiTemplate->set_var("nombre",'');
						$MiTemplate->set_var("rutcd",'');
						$MiTemplate->set_var("numfact",'');
						$MiTemplate->set_var("monfact",'');
						$MiTemplate->set_var("numpref",'');
						$MiTemplate->set_var("mensaje","alert('Se ha ingresado exitosamente la Factura $numfact para el instalador " . $res1['nombre'] . "');");
					/*}else{
							if ($mensaje){
								$queryUP = "UPDATE lote_instalador SET estado = 'F', fechafacturacion = now(),num_factura = $numfact WHERE id_lote = $numpref  ";
								tep_db_query($queryUP);
								$queryUPo = "UPDATE ot SET id_estado = 'VF' ,num_factura =0 WHERE id_lote = $numpref ";
								tep_db_query($queryUPo);
								$MiTemplate->set_var("mensaje","alert('No se generó factura para este lote, las OT sólo se marcaron como facturadas ');");
								insertahistorial("Las OT del lote" . $numpref . " cambian a estado " . get_nombre_estado('VF').", Se créo número de factura 0");
							}
						}*/
				}
			}
			else { 
				$MiTemplate->set_var("mensaje","alert('El instalador " . $res1['nombre'] . " NO tiene asociada la Pre-Factura $numpref');");				
			}
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