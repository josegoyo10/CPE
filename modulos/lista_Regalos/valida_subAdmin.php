<?php
session_start();

$idLista = $_REQUEST['idLista'];
$clie_rut = $_REQUEST['clie_rut'];
$accion = $_REQUEST['accion'];

$SIN_PER = 1;
include_once('../../includes/aplication_top.php');
$USR_LOGIN = get_login_usr( $ses_usr_id );
// valida los Datos del usuario que Autoriza
if(isset($_POST['usuario']))
{	
	$usuario = $_POST['usuario'];
	$contra = $_POST['contra'];
	$consulta = "SELECT PEMO_MOD_ID FROM permisosxmodulo WHERE PEMO_MOD_ID IN (".VALIDA_SUB_ADMIN.") AND PEMO_PER_ID = ( SELECT USR_ID FROM usuarios WHERE USR_LOGIN= '$usuario' AND USR_CLAVE = md5('$contra') )";
	$resultado = tep_db_query($consulta);
	$resultado_login = tep_db_fetch_array($resultado);	

	if(isset($resultado_login['PEMO_MOD_ID']))
	{
		// Anula la Cotizacion de Lista de regalos
		if($accion == 'Anl' && $clie_rut == 'Ctz'){
			$QryList = "SELECT idLista_enc  FROM list_os_enc WHERE idLista_OS_enc=".$idLista;
			$result = tep_db_query($QryList);
			$res = tep_db_fetch_array($result);	
			insertahistorial_ListaReg("Se autoriza la Anulación de la Cotizacion N°. ".$idLista.", por el usuario. ".$usuario." ", $USR_LOGIN, $idLista, $res['idLista_enc'], null, $tipo = 'SYS');
			?>
			<script language=JavaScript>
				var idLista = '<? echo $idLista; ?>';	
		 		location.href='../cotiza_Regalos/monitor_OSRegalos.php?accion=Anl&idLista='+idLista+'';   
				window.opener.location.reload();
				window.close();
		 	</script>
		 	<?
		}
		// Anula la Lista de Regalos
		if($accion == 'Anl' && $clie_rut == 'List'){	
			insertahistorial_ListaReg("Se autoriza la Anulación de la Lista de Regalos N°. ".$idLista.", por el usuario. ".$usuario." ", $USR_LOGIN, null, $idLista, null, $tipo = 'SYS');
			?>
			<script language=JavaScript>
				var idLista = '<? echo $idLista; ?>';	
		 		location.href='./monitor_ListRegalos.php?accion=Anl&idLista='+idLista+'';   
				window.opener.location.reload();
				window.close();
		 	</script>
		 	<?
		}
		// Paga la Lista de regalos
		if($accion == 'Paga' && $clie_rut == 'Paga'){
			// Busca la Lista de Regalos origen de la Cotización a Pagar.
			$QryList = "SELECT idLista_enc  FROM list_os_enc WHERE idLista_OS_enc=".$idLista;
			$result = tep_db_query($QryList);
			$res = tep_db_fetch_array($result);	
			
			insertahistorial_ListaReg("Se autoriza el Pago de la Cotización N°. ".$idLista.", por el usuario. ".$usuario." ", $USR_LOGIN, null, $res['idLista_enc'], $idLista, $tipo = 'SYS');
			?>
			<script language=JavaScript>
				var idLista = '<? echo $idLista; ?>';	
		 		window.open('../cotiza_Regalos/estado3.php?accion=Ver&id_os='+idLista);
		 		window.close();
		 	</script>
		 	<?
		}
		//Valida Reimpresion de OT Lista de regalos
		if($accion == 'Imp' && $clie_rut == 'OT'){
			$UPD =  "UPDATE list_ot SET ot_usuAutoriza = '".$usuario."' WHERE ot_idList in ('".$idLista."')";
			tep_db_query($UPD);
			?>
			<script language=JavaScript>
				var idLista = '<? echo $idLista; ?>';	 
				window.open('../cotiza_Regalos/print_OT.php?id_OT='+idLista, 1,0,0,0,0,1,1);
		 		window.close();
		 	</script>
		 	<?
		}
		//Valida Reimpresion de Guia de despacho de Lista de regalos
		if($accion == 'Imp' && $clie_rut == 'GD'){
			$UPD =  "UPDATE list_regalos_enc SET GD_usReimpre = '".$usuario."' WHERE idLista in ('".$idLista."')";
			tep_db_query($UPD);
			?>
			<script language=JavaScript>
				var idLista = '<? echo $idLista; ?>';	 
				window.open('printDespacho.php?idLista='+idLista, 1,0,0,0,0,1,1);
		 		window.close();
		 	</script>
		 	<?
		}
		//Valida el usuario para la Liquidación del Bono
		if($accion == 'Val' && $clie_rut == 'BN'){
			?>
			<script language=JavaScript>
				var idLista = '<? echo $idLista; ?>';	 
				var usuario = '<? echo $usuario; ?>';
				window.open('liquidarBono.php?idLista='+idLista+'&usuario='+usuario+'', 1,0,0,0,0,1,1);
		 		window.close();
		 	</script>
		 	<?
		}
		//Valida Reimpresion de Bonos
		if($accion == 'Imp' && $clie_rut == 'BN'){
			$UPD = "UPDATE list_bono SET usu_creacion='".$usuario."'  WHERE id_Lista=".$idLista."";
			tep_db_query($UPD);
			?>
			<script language=JavaScript>
				var idLista = '<? echo $idLista; ?>';	 
				window.open('print_Bono.php?idLista='+idLista, 1,0,0,0,0,1,1);
		 		window.close();
		 	</script>
		 	<?
		}
	}
	else{	
	?>
	<script language='JavaScript' type="text/javascript" >
	alert('Su usuario y/o contraseña son inválidos, \nPor favor verifique e ingrese nuevamente los datos');
	window.close();
	</script>";		
	<?php
	}
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Validar Pago Manual</title>

<link href="../estilos.css" rel="stylesheet" type="text/css">
</head>

<body>


<script language=JavaScript>
<!-- Deshabilita Botón derecho del Mouse en popop de Impresión 
var message="";

function clickIE() {if (document.all) {(message);return false;}}
function clickNS(e) {if 
(document.layers||(document.getElementById&&!document.all)) {
if (e.which==2||e.which==3) {(message);return false;}}}
if (document.layers) 
{document.captureEvents(Event.MOUSEDOWN);document.onmousedown=clickNS;}
else{document.onmouseup=clickNS;document.oncontextmenu=clickIE;}

document.oncontextmenu=new Function("return false")
// -->

<!-- Función que envia el formulario -->
	function cerrar_popop()
	{
		if(document.form1.usuario.value == ''){
		alert('Usuario no puede Ser vació');
		document.form1.usuario.focus();
		return false;
		}
		
		if(document.form1.contra.value == ''){
		alert('Cantraseña no puede Ser vació');
		document.form1.contra.focus();
		return false;
		}
			
	document.form1.submit();
	}
	
	function cerrar()
	{				
		window.close();	
	}	
 
</script>
<br>




<form name="form1" method="post" action="valida_subAdmin.php">

<div align="center" class="titulonormal"><strong> Validaci&oacute;n de SubAdministrador</strong></div>
<br>
<table width="222" border="0" align="center">
  <tr>
    <td width="85"><div align="right" class="textonormal">Usuario:</div></td>
    <td width="127">
      <input name="usuario" type="text" id="usuario" size="15" maxlength="10" >
</td>
  </tr>
  <tr>
    <td><div align="right" class="textonormal">Contrase&ntilde;a:</div></td>
    <td><input name="contra" type="password" id="contra" maxlength="10" ></td>
  </tr>
  <tr>
    <td>
      <div align="right">
        <input type="button"  name="Validar" value="Autorizar"  onClick="cerrar_popop();" >
      </div></td>
    <td>      <div align="center">
      <input type="button"  name="Validar" value="Cerrar"  onClick="cerrar();" >
    </div></td>
  </tr>
</table>

<input name="idLista" type="hidden" id="idLista" size="15" value="<?php echo $_REQUEST['idLista']; ?>">
<input name="clie_rut" type="hidden" id="clie_rut" size="15" value="<?php echo $_REQUEST['clie_rut']; ?>">
<input name="accion" type="hidden" id="accion" size="15" value="<?php echo $_REQUEST['accion']; ?>">

</form>

</body>
</html>
