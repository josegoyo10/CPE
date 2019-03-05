<?
/* dalos permisos , ver como */
$SIN_PER = 1;
$pag_ini = '../facturacion/facturacion_03.php';
include "../../includes/aplication_top.php";

//ACCIONES CGI
if ($accion == "grabar") {
	//Grabo datos número 1 y 2 en lote
	$queryUP = "UPDATE lote_instalador SET numero1 = " . (($numero1)?$numero1:"null") . ", numero2 = " . (($numero2)?$numero2:"null") . " WHERE id_lote = $id_lote ";
	tep_db_query($queryUP);
	?>
	<SCRIPT LANGUAGE="JavaScript">
		window.close();
	</SCRIPT>
	<?
}
//FIN ACCIONES

$MiTemplate = new Template();
$MiTemplate->debug = 0;
$MiTemplate->set_root(DIRTEMPLATES);
$MiTemplate->set_unknowns("remove");

$MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO);
$MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);
$MiTemplate->set_var("id_lote", $id_lote);

$qry_fac="select estado, numero1, numero2 from lote_instalador where id_lote=".$id_lote;
$rq = tep_db_query($qry_fac);
$res1 = tep_db_fetch_array( $rq );

if ($res1['estado'] != 'P')
	$MiTemplate->set_var("disabled", "disabled");
else
	$MiTemplate->set_var("disabled", "");

$MiTemplate->set_var("numero1", $res1['numero1']);
$MiTemplate->set_var("numero2", $res1['numero2']);

// Agregamos el header
$MiTemplate->set_file("header","header_sc.html");

// Agregamos el main
$MiTemplate->set_file("main","facturacion/datos_oc.htm");

$MiTemplate->pparse("OUT_H", array("header"), false);
$MiTemplate->parse("OUT_M", array("main","footer"), true);
$MiTemplate->p("OUT_M");
include "../../includes/application_bottom.php";

?>