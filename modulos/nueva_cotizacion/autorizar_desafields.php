<?php
//ini_set('display_errors', true);
//error_reporting(E_ALL);
session_start();

$id_estado   = $_REQUEST['id_estado'];
$id_os       = $_REQUEST['id_os'];
$clie_rut 	 = $_POST['clie_rut'];
$id_proyecto = $_REQUEST['id_proyecto'];
$accion 	 = $_REQUEST['accion'];

//include_idioma_mod( $ses_idioma, "nueva_cotizacion_01" );
file_put_contents("modulos.txt", $clie_rut);

$SIN_PER = 1;
include_once('../../includes/aplication_top.php');

	?>
		
	<SCRIPT language="javascript" type="text/javascript">	
	function cerrar() {
			window.close();
	}
    
	</SCRIPT>
		
	<body>
	
	<?php
	
if(isset($_POST['usuario']))
{	
	$usuario = trim($_POST['usuario']);
	$contra = $_POST['contra'];
	$consulta = "SELECT PEMO_MOD_ID FROM permisosxmodulo WHERE PEMO_MOD_ID = " . PERMISO_PAGO_MANUAL . " AND PEMO_PER_ID = ( SELECT USR_ID FROM usuarios WHERE USR_LOGIN= '$usuario' AND USR_CLAVE = md5('$contra') )";
	$resultado = tep_db_query($consulta);
	$resultado_login = tep_db_fetch_array($resultado);	
	
	if(isset($resultado_login['PEMO_MOD_ID']))
	{
	$_SESSION['pago_manual_coti'] = 1;
	
            echo "<script language='JavaScript'>";
              
            echo "location.href='nueva_cotizacion_01.php?clie_rut=" . ($clie_rut+0). "&clie_tipo=01'";
        	echo "</script>";





	}else{
	$_SESSION['pago_manual_coti'] = 0;	
	?>
	
	<script language='JavaScript' type="text/javascript" >
	alert('Su usuario y/o contrase\u00f1a son inv\u00e1lidos, \nPor favor verifique e ingrese nuevamente los datos');
	 location.href='acceso.php?clie_rut=<?=$clie_rut ?>' ;

    </script>
       

	<?php
	}
}

