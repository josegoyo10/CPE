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

//Realizamos la consulta de la Fecha y hora de visita de instalacion asignada previamente.
$queryVisita = "Select fecha_visita_inst From ot where ot_id=".$id_ot."";
	$result = tep_db_query($queryVisita);
	$res = tep_db_fetch_array( $result );
	$fhhr_visita = $res['fecha_visita_inst'];
	
	$fecha_visita = invierte_fechaSlash(substr($fhhr_visita, 0,10));
	$hora_visita = substr($fhhr_visita, 11,2);
	$minuto_visita = substr($fhhr_visita, 14,2);
	$am_pm = substr($fhhr_visita, 16,3);
	
	$MiTemplate->set_var("fecha_visita", ($fecha_visita)?$fecha_visita:"");
	$MiTemplate->set_var("hora_visita", ($hora_visita)?$hora_visita:"");
	$MiTemplate->set_var("minuto_visita", ($minuto_visita)?$minuto_visita:"");
	$MiTemplate->set_var("am_pm", ($am_pm)?$am_pm:"");
	
//Realizamos el Update con la fecha de Visita asignada.
if($accion == 'update'){
	$fecha_visita_inst = invierte_fechaGuion($fecha)." ".$horas.":".$minutos." ".$ampm;
	$update="update ot SET fecha_visita_inst='".$fecha_visita_inst."' where ot_id=".$id_ot."";
	tep_db_query($update);

	insertahistorial("Se asigna la Fecha de Visita de instalador para la OT $id_ot , el dia $fecha_visita_inst.");
	echo '<SCRIPT language="JavaScript">
		  	window.self.close();
	      </SCRIPT>';	
}

// Agregamos el main
$MiTemplate->set_file("main","monitor_ot_sv/asignaFecha.htm");
$MiTemplate->set_var("id_ot",$id_ot);

$MiTemplate->pparse("OUT_H", array("header"), false);
$MiTemplate->parse("OUT_M", array("main"), true);
$MiTemplate->p("OUT_M");
include "../../includes/application_bottom.php";
?>