<?php
session_start();

$id_estado = $_REQUEST['id_estado'];
$id_os = $_REQUEST['id_os'];
$clie_rut = $_REQUEST['clie_rut'];
$id_proyecto = $_REQUEST['id_proyecto'];
$accion = $_REQUEST['accion'];

$SIN_PER = 1;
include_once('../../includes/aplication_top.php');

	?>
		
	<SCRIPT language="javascript" type="text/javascript">	
	function cerrar() {
	window.close();
	}
	</SCRIPT>
		
	<body onLoad="setTimeout('cerrar()',60*1000)">
	
	<?php
	
if(isset($_POST['usuario']))
{	
	$usuario = $_POST['usuario'];
	$contra = $_POST['contra'];
	$consulta = "SELECT PEMO_MOD_ID FROM permisosxmodulo WHERE PEMO_MOD_ID = " . PERMISO_PAGO_MANUAL . " AND PEMO_PER_ID = ( SELECT USR_ID FROM usuarios WHERE USR_LOGIN= '$usuario' AND USR_CLAVE = md5('$contra') )";
	$resultado = tep_db_query($consulta);
	$resultado_login = tep_db_fetch_array($resultado);	
	
	if(isset($resultado_login['PEMO_MOD_ID']))
	{
	$_SESSION['pago_manual_coti'] = 1;
	
	

	echo "<script language=JavaScript>";
 	echo "location.href='estado3.php?id_estado=$id_estado&id_os=$id_os&clie_rut=$clie_rut&id_proyecto=$id_proyecto&accion=Ver';";	   
	echo "</script>";
	}
	else{
	$_SESSION['pago_manual_coti'] = 0;	
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

<link href="../nueva_cotizacion/estilos.css" rel="stylesheet" type="text/css">
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




<form name="form1" method="post" action="validar_pagoManual.php">

<div align="center" class="titulonormal"><strong> Validaci&oacute;n de Supervisor para Pago Manual </strong></div>
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

<input name="id_estado" type="hidden" id="id_estado" size="15" value="<?php echo $_REQUEST['id_estado']; ?>" >
<input name="id_os" type="hidden" id="id_os" size="15" value="<?php echo $_REQUEST['id_os']; ?>">
<input name="clie_rut" type="hidden" id="clie_rut" size="15" value="<?php echo $_REQUEST['clie_rut']; ?>">
<input name="id_proyecto" type="hidden" id="id_proyecto" size="15" value="<?php echo $_REQUEST['id_proyecto']; ?>">
<input name="accion" type="hidden" id="accion" size="15" value="<?php echo $_REQUEST['accion']; ?>">

</form>

</body>
</html>
