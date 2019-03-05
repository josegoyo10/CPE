<?php
$pag_ini = "../seleccionar_cliente/seleccionar_vendedor.php";
include ("../../includes/aplication_top.php");

if(isset($_POST['cedula']) || isset($_POST['nombres']) || isset($_POST['apellidos']))
{
$cedula = $_POST['cedula'];
$nombres = $_POST['nombres'];
$apellidos = $_POST['apellidos'];
	
	$queryClie =  "SELECT USR_LOGIN, USR_NOMBRES, USR_APELLIDOS FROM usuarios WHERE 1 AND ";
	
	if($_POST['cedula'] != '')	
	$queryClie =  $queryClie . " USR_LOGIN = $cedula ";    
	
	if($_POST['nombres'] != '' && $_POST['cedula'] != '')
	$queryClie =  $queryClie . " AND UPPER(USR_NOMBRES) LIKE UPPER('%$nombres%') ";
	
	if($_POST['nombres'] != '' && $_POST['cedula'] == '')
	$queryClie =  $queryClie . " UPPER(USR_NOMBRES) LIKE UPPER('%$nombres%') ";
	
	if($_POST['apellidos'] != '' && ($_POST['cedula'] != '' || $_POST['nombres'] != ''))
	$queryClie =  $queryClie . " AND UPPER(USR_APELLIDOS) LIKE UPPER('%$apellidos%')";
	
	if($_POST['apellidos'] != '' && ($_POST['cedula'] == '' && $_POST['nombres'] == ''))
	$queryClie =  $queryClie . " UPPER(USR_APELLIDOS) LIKE UPPER('%$apellidos%')";

	$ecliente = tep_db_query($queryClie);
    $numero  = tep_db_num_rows($ecliente);
	    
	if($numero == 0)
	{
		echo "<script language=JavaScript>";
		echo "alert('No existen vendedores creados para la consulta solicitada')";
		echo "</script>";
	}

}
?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<script language="javascript" src="../../includes/funciones.js"></script>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Seleccionar Vendedor</title>
<link rel="stylesheet" type="text/css" href="../estilos.css">


<script language="javascript"  type="text/javascript">

function KeyIsNumber(evt)
{
var isNav = (navigator.appName.indexOf("Netscape") != -1)
var isIE = (navigator.appName.indexOf("Microsoft") != -1)
	if (isNav) {
		if ( evt.which == 13 || evt.which == 44 || evt.which == 8 || (evt.which >= 48 &&  evt.which <=57) )
		return true;
	return false;
	}
	else if (isIE)
		{evt = window.event;
		if ( evt.keyCode == 13 || evt.keyCode == 44 || evt.keyCode == 8 || (evt.keyCode >= 48 && evt.keyCode <= 57) )
			return true;
		return false;
		}
	else {
		alert("Su browser no es soportado por esta aplicación")
	}
	return false
}


function KeyIsLetra(evt)
{
var isNav = (navigator.appName.indexOf("Netscape") != -1)
var isIE = (navigator.appName.indexOf("Microsoft") != -1)

	if (isNav) {
		if ( evt.which == 209 || evt.which == 241 || evt.which == 13 || evt.which == 8 || (evt.which >= 65 &&  evt.which <=90) || (evt.which >= 97 &&  evt.which <=122) || evt.which == 32)
		return true;
	return false;
	}
	else if (isIE)
		{
		evt = window.event;		
		if ( evt.keyCode == 209 || evt.keyCode == 241 || evt.keyCode == 13 || evt.keyCode == 8 || (evt.keyCode >= 65 && evt.keyCode <= 90) || (evt.keyCode >= 97 && evt.keyCode <= 122) || evt.keyCode == 32 )
			return true;
		return false;
		}
	else {
		alert("Su browser no es soportado por esta aplicación")
	}
	return false
}


function validar()
{		
	if( document.forma.cedula.value == '' && document.forma.nombres.value == '' && document.forma.apellidos.value == '')
	{
	alert('Debe ingresar al menos un parámetro de consulta');
	return false;
	}
	
	
	var cedula = document.forma.cedula.value;
	var lonCedula = cedula.length;	
	if(lonCedula < 5 && document.forma.cedula.value != '')
	{
	alert('Debe digitar por lo menos Cinco Números en el campo Cédula');
	return false;	
	}
		
	var nombres = document.forma.nombres.value;
	var lonNombres = nombres.length;	
	if(lonNombres < 3 && document.forma.nombres.value != '')
	{
	alert('Debe digitar por lo menos Tres caracteres en el campo Nombre');
	return false;	
	}
		
	var apellidos = document.forma.apellidos.value;
	var lonApellidos = apellidos.length;	
	if(lonApellidos < 3 && document.forma.apellidos.value != '')
	{
	alert('Debe digitar por lo menos Tres caracteres en el campo Apellido');
	return false;	
	}
			
	return true;	
	
}

var Nombre = '';
var Cedula = '';

function concatenar(valor, cedula)
{
Nombre = valor;
Cedula = cedula;
}

function colocar_valor()
{
	if(Nombre == '')
	{
	alert('Por favor seleccione un vendedor y oprima Aceptar');
	}
	else{
	opener.vendedor.clien.value = Nombre;
	opener.form_paginas.rut.value = Cedula;
	opener.vendedor.rut.value = Cedula;
	window.close();
	}
}

var IdCelda = '';

function cambiar_color_over(celda){	
   	celda.style.backgroundColor="#66FF33";
	
	if(IdCelda != '')
	{
	anterior = document.getElementById(IdCelda);
    anterior.style.backgroundColor="#E9E9E9";
    } 	
}

function cambiar_color_out(celda){
   celda.style.backgroundColor="#E9E9E9";
} 

function cambiar_color(Id){   
   	IdCelda = Id;	
}


</script>


</head>

<body>
<p align="center" class="titulonormal"><strong>BUSQUEDA DE VENDEDOR</strong></p>
<form name="forma" method="post" action="seleccionar_vendedor.php" onSubmit="return validar();">
  <table width="686" border="0" align="center">
    <tr class="textoespecial1">
      <td width="" class="subtitulonormal"><div align="right" class="tabla1">C&eacute;dula/Nit:</div></td>
      <td width="144"><div align="left">
        <input name="cedula" type="text" id="cedula" maxlength="15">
      </div></td>
      <td width="75" class="subtitulonormal"><div align="right" class="tabla1">Nombres:</div></td>
      <td width="155"><div align="left">
        <input name="nombres" type="text" id="nombres" maxlength="45">
      </div></td>
      <td width="64" class="subtitulonormal"><div align="right" class="tabla1">Apellidos:</div></td>
      <td width="159"><div align="left">
        <input name="apellidos" type="text" id="apellidos" maxlength="90">
      </div></td>
    </tr>


    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
	
	
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>
        <div align="left">
          &nbsp;&nbsp;&nbsp;
          <input type="submit" name="Submit" value="Consultar">
        </div></td><td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  </table>
  

  
</form>

	<script LANGUAGE="javascript">			
			window.document.forma.cedula.onkeypress = KeyIsNumber;			
			window.document.forma.nombres.onkeypress = KeyIsLetra ;
			window.document.forma.apellidos.onkeypress = KeyIsLetra ;			
	</script>


<table width="688" border="1" align="center" class="tabla1">
  <tr class="textoespecial1">
    <td width="199"><div align="left" class="tabla2">C&eacute;dula/Nit</div></td>
    <td width="236"><div align="left" class="tabla2">Nombres</div></td>
    <td width="226"><div align="left" class="tabla2">Apellidos</div></td>
  </tr>
  
  
<?php 
  while($ResulClie = tep_db_fetch_array($ecliente))
  {
  
?>
  <tr id="<?php echo $ResulClie['USR_LOGIN']; ?>" bgcolor="#E9E9E9" class="tabla2" onClick="concatenar(this.getElementsByTagName('td')[1].innerHTML + ' ' + this.getElementsByTagName('td')[2].innerHTML, this.getElementsByTagName('td')[0].innerHTML); cambiar_color_over(this); cambiar_color(id); " >
    <td><?php echo $ResulClie['USR_LOGIN']; ?></td>
    <td id="<?php echo $ResulClie['USR_LOGIN']; ?>"><?php echo $ResulClie['USR_NOMBRES']; ?></td>
    <td ><?php echo $ResulClie['USR_APELLIDOS']; ?></td>
  </tr>
  
<?php 
	}  
?>
</table>


<br>

<div align="center">
  <input  type="button" name="Aceptar" value="Aceptar" onClick="colocar_valor();">
</div>

</body>
</html>