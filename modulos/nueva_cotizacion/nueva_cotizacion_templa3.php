<?
$SIN_PER = 1;
include ("../../includes/aplication_top.php");

/** ACCIONES*************/

	$queryclos ="SELECT CL.clie_rut, CL.clie_nombre, CL.clie_paterno,CL.clie_tipo, CL.clie_materno,CL.clie_telefonocasa,CL.clie_telcontacto1,CL.clie_telcontacto2 FROM clientes CL inner join os OS on (CL.clie_rut=OS.clie_rut) where OS.id_os=".($id_os+0)."";
        $eclios= tep_db_query($queryclos);
        $eclios = tep_db_fetch_array( $eclios );
        $definido=$eclios['clie_tipo'];
        $clie_rut=$eclios['clie_rut'];
    /* si el cliente existe y es empresa se redirecciona */
            if($definido=='e'){
                header ('Location: nueva_cotizacion_empresa_01.php?donde='.$donde.'&clie_rut='.($clie_rut+0).'&clie_tipo=e');
                tep_exit();
            }else{
                header ('Location: nueva_cotizacion_01.php?donde='.$donde.'&clie_rut='.($clie_rut+0).'&clie_tipo=p');
                tep_exit();
     
            }

include "../../includes/application_bottom.php";
?>