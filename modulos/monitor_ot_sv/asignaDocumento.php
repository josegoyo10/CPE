<?
//$pag_ini = '../monitor_ot_pe/monitor_ot_pe_00.php';
$SIN_PER = 1;
$id_ot = $_REQUEST['id_ot'];
include "../../includes/aplication_top.php";

$MiTemplate = new Template();
// asignamos degug maximo
$MiTemplate->debug = 0;
// root directory de los templates
$MiTemplate->set_root(DIRTEMPLATES);
// variables perdidas
$MiTemplate->set_unknowns("remove");
// Agregamos el main
$MiTemplate->set_file("main","monitor_ot_sv/asignaDocumento.htm");

//Envia el Número de Documento Guardado en Base de Datos. 
$MiTemplate->set_var("legend", 'Ingrese el Número de Documento  "Acta de Entrega o Levantamiento de Obra"  con el cúal recibio el Cliente');
//Realizamos la consulta del número de Documento Asignado.
$queryDoc = "Select doc_instalacion From ot where ot_id=".$id_ot."";
	$result = tep_db_query($queryDoc);
	$res = tep_db_fetch_array( $result );
	$doc = $res['doc_instalacion'];
	
	$MiTemplate->set_var("doc", ($doc)?$doc:"");
	$MiTemplate->set_var("id_ot",$id_ot);
	$MiTemplate->set_var("accion","update");
	
//Realizamos el Update con el número de Documento asignada.
if($accion == 'update'){
	$update="update ot SET doc_instalacion='".$docu."' where ot_id=".$id_ot."";
	tep_db_query($update);

	insertahistorial("Se ingreso el número de Documento de Entrega  ".$docu." para la OT ".$id_ot.".");
}

$MiTemplate->pparse("OUT_H", array("header"), false);
$MiTemplate->parse("OUT_M", array("main"), true);
$MiTemplate->p("OUT_M");
include "../../includes/application_bottom.php";
?>