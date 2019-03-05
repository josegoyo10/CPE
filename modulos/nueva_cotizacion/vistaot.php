<?
$SIN_PER = 1;
//$pag_ini = '../nueva_cotizacion/nueva_cotizacion_00.php';
include "../../includes/aplication_top.php";

$MiTemplate = new Template();
$MiTemplate->debug = 0;
$MiTemplate->set_root(DIRTEMPLATES);
$MiTemplate->set_unknowns("remove");

$MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
$MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);

$MiTemplate->set_var("clie_rut",$clie_rut);
$MiTemplate->set_var("accion",$accion);
$MiTemplate->set_var("id_os",$id_os);
    
// Agregamos el header
$MiTemplate->set_file("header","header_ident.html");

// Agregamos el main
$MiTemplate->set_file("main","nueva_cotizacion/vistaot.htm");

$MiTemplate->set_block("main", "DatosHeader", "BLO_DatosHeader");
$queryHead="SELECT id_os, if (clie_tipo = 'p', concat(clie_nombre, ' ', clie_paterno, ' ', clie_materno), clie_razonsocial) clie_nombre, dire_telefono, dire_direccion, NE.DESCRIPTION AS comu_nombre 
			FROM os
			JOIN clientes c ON c.clie_rut = os.clie_rut
			JOIN direcciones D ON D.clie_rut = os.clie_rut and D.id_direccion=os.id_direccion
			LEFT JOIN cu_neighborhood NE ON (D.dire_localizacion = NE.LOCATION)
			WHERE id_os = ".($id_os+0)." ";
query_to_set_var( $queryHead, $MiTemplate, 0, 'BLO_DatosHeader', 'DatosHeader' );

$MiTemplate->set_block("main", "Otes", "BLO_ot");
    $queryHist="SELECT ot.ot_id, ot_tipo, nombre ot_despacho, DATE_FORMAT(ot_fechacreacion, '%e/%m/%Y %H:%i:%S') ot_fechacreacion, esta_nombre
				FROM ot
				JOIN tipos_despacho td on td.id_tipodespacho = ot.id_tipodespacho
				JOIN estados e on e.id_estado = ot.id_estado
				WHERE id_os = ".($id_os+0)." order by 1";
$num_otes = query_to_set_var( $queryHist, $MiTemplate, 1, 'BLO_ot', 'Otes' );

$MiTemplate->set_var("num_otes",$num_otes);

// Agregamos el footer
include "../../includes/footer_cproy.php";

$MiTemplate->pparse("OUT_H", array("header"), true);
include "../../menu/menu.php";
$MiTemplate->parse("OUT_M", array("main","footer"), true);
$MiTemplate->p("OUT_M");

include "../../includes/application_bottom.php";
?>