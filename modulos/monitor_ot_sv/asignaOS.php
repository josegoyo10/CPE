<?
//$pag_ini = '../monitor_ot_pe/monitor_ot_pe_00.php';
$SIN_PER = 1;
$id_ot = $_REQUEST['id_ot'];
$asigna = $_REQUEST['asigna'];
include "../../includes/aplication_top.php";

$MiTemplate = new Template();
// asignamos degug maximo
$MiTemplate->debug = 0;
// root directory de los templates
$MiTemplate->set_root(DIRTEMPLATES);
// variables perdidas
$MiTemplate->set_unknowns("remove");
// Agregamos el main
$MiTemplate->set_file("main","monitor_ot_sv/asignaOS.htm");

if($asigna == 'IN'){
	$MiTemplate->set_var("legend", "Ingrese el número de la OS asociada a la Instalación");
	$subtipoprod = $asigna;
	$tipoAso = 'os_asoInstala';
}	
if($asigna == 'VI'){
	$MiTemplate->set_var("legend", "Ingrese el número de la OS asociada a la Visita");
	$subtipoprod = $asigna;
	$tipoAso = 'os_asoVisita';
}
if($asigna == 'PS'){
	$MiTemplate->set_var("legend", "Ingrese el número de la OS asociado a los Materiales");
	$subtipoprod = $asigna;
	$tipoAso = 'os_asoMaterial';
}
//Realizamos la consulta de la cedula del cliente asociada a las OT.
$queryUsuario = "SELECT os.clie_rut FROM ot JOIN os ON os.id_os=ot.id_os WHERE ot_id=".$id_ot."";
$resultUsuario = tep_db_fetch_array(tep_db_query($queryUsuario));

$MiTemplate->set_block("main", "OS", "BLO_OS");
$queryAsigna = "SELECT OS.id_os AS id_os FROM os AS OS 
			   JOIN os_detalle OD ON OD.id_os=OS.id_os
			   WHERE OS.clie_rut=".$resultUsuario['clie_rut']." 
			   AND OD.osde_subtipoprod='".$asigna."' AND OS.id_estado='SP'
			   GROUP BY OS.id_os";

$resultAsigna = tep_db_query($queryAsigna);
$count=0;
	while($row=tep_db_fetch_array($resultAsigna)){		
		$MiTemplate->set_var("id_os", $row['id_os']);
		$MiTemplate->set_var("value", $row['id_os']);
		$MiTemplate->parse('BLO_OS', 'OS',true );
		$count ++;
		}
if($count == 0){
	$MiTemplate->set_var("id_os", "No existe OS Asociada");
	$MiTemplate->set_var("value", $count);
	$MiTemplate->parse('BLO_OS', 'OS',true );
}

//Si la OS ya fue asociada 
$queryAsocia = "SELECT ".$tipoAso." 
				FROM ot 
				WHERE ot_id=".$id_ot;
$resultAsocia = tep_db_query($queryAsocia);
$rowAsocia=tep_db_fetch_array($resultAsocia);

if($rowAsocia[0] != '0'){
    echo '<SCRIPT language="JavaScript">
		  	alert("Ya fue asignada una OS");
			window.close();
	      </SCRIPT>';
}

//Realizamos el Update con la OS seleccionada.
if($actua == 'update'){
	$update="update ot SET ".$tipoAso." ='".$OS."' where ot_id=".$id_ot."";
	tep_db_query($update);

	insertahistorial("Se asigna la OS N°".$OS."");
	echo '<SCRIPT language="JavaScript">
		  	window.self.close();
	      </SCRIPT>';
}

$MiTemplate->set_var("id_ot", $id_ot);
$MiTemplate->set_var("tipoAso", $tipoAso);
$MiTemplate->parse("OUT_H", array("header"), false);
$MiTemplate->parse("OUT_M", array("main"), true);
$MiTemplate->p("OUT_M");
include "../../includes/application_bottom.php";
?>