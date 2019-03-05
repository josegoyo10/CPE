<?php
session_start();
$SIN_PER = 1;
include_once('../../includes/aplication_top.php');

if(isset($_POST['usuario']))
{	
	$usuario = $_POST['usuario'];
	$contra = $_POST['contra'];
	$consulta = "SELECT PEMO_MOD_ID FROM permisosxmodulo WHERE PEMO_MOD_ID = " . PERMISO_REIMPRESION_PICKING . " AND PEMO_PER_ID = ( SELECT USR_ID FROM usuarios WHERE USR_LOGIN= '$usuario' AND USR_CLAVE = md5('$contra') )";
	$resultado = tep_db_query($consulta);
	$resultado_login = tep_db_fetch_array($resultado);	
	
	if(isset($resultado_login['PEMO_MOD_ID']))
	{	
	$_SESSION['reimpresion'] = 1;
	$_SESSION['usu_reimpresion'] = $usuario;
	echo "<script language=JavaScript>";	
//	echo "opener.document.location.reload();";
  echo "window.open('framed_imprimeAll_guiaDespacho.php','nombre2','toolbar=no,width=650,height=auto');";
 	echo "self.close();";
	echo "</script>";
	}
	else{
	$_SESSION['reimpresion'] = 0;	
	?>
	<script language="JavaScript">;
	<?php
	
	
	if($_SESSION['teo_reimp'] == 1)
	{
	?>
		alert('Su usuario y/o contraseña son inválidos, por favor\nverifique e ingrese nuevamente los datos');
		window.close();
		opener.window.close();
	<?php
	}
	else{
	?>
	alert('Usuario no válido, solo podrá reimprimir\nLas Ordenes que no requieren reimpresión');
	window.close();
	opener.imprimir();
	opener.close();
	<?php
	}
	?>
	</script>	
	<?php
	}
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Validar Impresión</title>

<link href="../nueva_cotizacion/estilos.css" rel="stylesheet" type="text/css">
</head>

<body>

<script language="JavaScript"  type="text/javascript">
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

<!-- Funcion que envia el formulario -->
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
	
	function cerrar(teo)
	{		
		if(teo == 1)
		{
		window.close();
		window.opener.close();
		}
		else{		
		window.close();
		opener.print();
		opener.close();
		}
	}

 
</script>
<br>




<form name="form1" method="post" action="validar_reimpresion.php">
<div align="center" class="titulonormal"><strong> Validaci&oacute;n de Cordinador para Reimpresi&oacute;n </strong></div>
<br>
<table width="222" border="0" align="center">
  <tr>
    <td width="85"><div align="right" class="textonormal">Usuario:</div></td>
    <td width="127">
      <input name="usuario" type="text" id="usuario" size="15" maxlength="10">
</td>
  </tr>
  <tr>
    <td><div align="right" class="textonormal">Contrase&ntilde;a:</div></td>
    <td><input name="contra" type="password" id="contra" maxlength="8"></td>
  </tr>
  <tr>
    <td>
      <div align="right">
        <input type="button"  name="Validar" value="Autorizar"  onClick="cerrar_popop();" >
      </div></td>
    <td>      <div align="center">
      <input type="button"  name="Validar" value="Cerrar"  onClick="cerrar('<?php echo $_SESSION['teo_reimp'] ?>');" >
    </div></td>
  </tr>
</table>

</form>
</body>
</html>
