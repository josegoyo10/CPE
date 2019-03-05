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
$MiTemplate->set_file("main","monitor_ot_sv/devuelveOS.htm");

$MiTemplate->set_var("legend", "Ingrese el número de la OS asociada a la Devolución");

//Realizamos la consulta de los motivos de devolucion de la OS de visita.
$MiTemplate->set_block("main", "MOTIVOS", "BLO_MOTIVOS");
$queryMotivo = "SELECT * FROM motivo_dev_os;";

$resultMotivo = tep_db_query($queryMotivo);
	while($row=tep_db_fetch_array($resultMotivo)){		
		$MiTemplate->set_var("id", $row['id']);
		$MiTemplate->set_var("motivo", $row['nombre']);
		$MiTemplate->parse('BLO_MOTIVOS', 'MOTIVOS',true );
		}
		
//Verificamos si la OS ya fue devuelta
$queryVerifica = "SELECT os_motDevolucion 
				  FROM ot 
				  WHERE ot_id=".$id_ot;
$resultVerifica = tep_db_query($queryVerifica);
$rowVerifica=tep_db_fetch_array($resultVerifica);

if($rowVerifica['os_motDevolucion'] != '0'){
    echo '<SCRIPT language="JavaScript">
		  	alert("Ya se realizo la devolución para la OT '.$id_ot.'");
			window.close();
	      </SCRIPT>';
}

//Realizamos el Update con el motivo y valor de la devolucion.
if($actua == 'update'){	
	$update="update ot SET os_motDevolucion='".$motivo."', os_valDevolucion='".$valor."' where ot_id=".$id_ot."";
	tep_db_query($update);

	insertahistorial("Se realiza la devolucion para la OT N°".$id_ot.", por motivo de ".$motivo." y un valor de ".$valor.".");
	echo '<SCRIPT language="JavaScript">
		  	window.self.close();
	      </SCRIPT>';
}

$MiTemplate->set_var("id_ot", $id_ot);
$MiTemplate->parse("OUT_H", array("header"), false);
$MiTemplate->parse("OUT_M", array("main"), true);
$MiTemplate->p("OUT_M");
include "../../includes/application_bottom.php";
?>