<?
$pag_ini = '../adm_usr/cambiar_Password.php';
include "../../includes/aplication_top.php";


    $MiTemplate = new Template();
    // asignamos degug maximo
    $MiTemplate->debug = 0;
    // root directory de los templates
    $MiTemplate->set_root(DIRTEMPLATES);
    // variables perdidas
    $MiTemplate->set_unknowns("remove");


    $MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));

    // Agregamos el header
    $MiTemplate->set_file("header","header_cc.html");

    // Agregamos el main
    $MiTemplate->set_file("main","adm_usr/cambiar_password.html");

	/**********************************************************************************************/
	
	if($_POST['accion']=='guardar'){	
		if($_POST['ca']== md5($_POST['clave'])){
			if($_POST['nclave']==$_POST['cclave']){
				$update_cambio = "UPDATE usuarios SET USR_CLAVE=md5('".$_POST['nclave']."') WHERE USR_ID='".$ses_usr_id."';";
				if(tep_db_query($update_cambio)){
					alert('Cambio Exitoso');
					?>	
					<script language='JavaScript' type="text/javascript" >
						location.href='../start/start_01.php';
					</script>
					<?php
				}
				else{
					alert('No es posible atender la solicitud en este momento');

				}
			}
			else{
				alert('La nueva Clave y la Confirmación no son iguales');
			}		
		}
		else{
				alert('PASSWORD Incorrecto');
		}
	}
    
	$query_usu = "SELECT USR_LOGIN, USR_NOMBRES, USR_APELLIDOS, USR_CLAVE FROM usuarios WHERE USR_ID='".$ses_usr_id."';";
	$row = tep_db_query($query_usu);
	$resp = tep_db_fetch_array($row);
	$MiTemplate->set_var("usuario",$resp['USR_LOGIN']);
	$MiTemplate->set_var("nombre",$resp['USR_NOMBRES']);
	$MiTemplate->set_var("apellido",$resp['USR_APELLIDOS']);
	$MiTemplate->set_var("ca",$resp['USR_CLAVE']);
	
	/**********************************************************************************************/
		
    // Agregamos el footer
    $MiTemplate->set_file("footer","footer.html");

    $MiTemplate->pparse("OUT_H", array("header"), false);
    include "../../menu/menu.php";
    $MiTemplate->parse("OUT_M", array("main","footer"), true);
    $MiTemplate->p("OUT_M");


include "../../includes/application_bottom.php";
?>
