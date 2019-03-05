<?php
$pag_ini = "../nueva_cotizacion/seleccionar_prodPs_Pe.php";
include ("../../includes/aplication_top.php");

if(isset($_POST['codSap']))
{
$codigoSap = $_POST['codSap'];


$queryClie= "SELECT DISTINCT P.cod_prod1, P.des_larga, PV.cod_prov, PRV.nom_prov, V.numero_dias, V.cantidadventaXvolumen FROM productos P
			JOIN prodxprov PV ON P.id_producto = PV.id_producto
			JOIN proveedores PRV ON PV.cod_prov = PRV.cod_prov
			LEFT JOIN ventaxvolumenps V ON P.cod_prod1 = V.cod_sap AND PRV.cod_prov = V.cod_proveedor
			JOIN precios PR  ON P.cod_prod1 = PR.cod_prod1
			JOIN codbarra CB ON CB.cod_prod1 = P.cod_prod1
			WHERE P.prod_tipo = 'PS' AND  P.estadoactivo = 'C'  AND  PR.estadoactivo = 'C' AND CB.estadoactivo = 'C' ";

//PV.cod_prov <> 'INT_C009'
		
	if($_POST['codSap'] != '')	
	$queryClie =  $queryClie . " AND P.cod_prod1 = $codSap ";
	
	$ecliente = tep_db_query($queryClie);
    $numero  = tep_db_num_rows($ecliente);	    
		
	if($numero == 0)
	{
		echo "<script language=JavaScript>";
		echo "alert('No existen Productos PS creados para la consulta solicitada')";
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
<title>Seleccionar Cliente</title>
<link rel="stylesheet" type="text/css" href="../estilos.css">


<script language="javascript"  type="text/javascript">

function KeyIsNumber(evt)
{
var isNav = (navigator.appName.indexOf("Netscape") != -1)
var isIE = (navigator.appName.indexOf("Microsoft") != -1)
	if (isNav) {
		if ((evt.which >= 48 &&  evt.which <=57) )
		return true;
	return false;
	}
	else if (isIE)
		{evt = window.event;
		if ((evt.keyCode >= 48 && evt.keyCode <= 57) )
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
	if( document.forma.codSap.value == '')
	{
	alert('Debe ingresar el Código Sap');
	return false;
	}
		
	return true;	
	
}

var codSap2 = ''; 
var nomProv2 = ''; 
var codProv2 = ''; 
var codProd2 = ''; 
var descProd2 = '';
var numDias2 = '';
function concatenar(codSap, nomProv, codProv, valorMax, descProd, numDias)
{
codSap2 = codSap;
nomProv2 = nomProv;
codProv2 = codProv;
valorMax2 = valorMax;
descProd2 = descProd;
numDias2 = numDias;
}

function colocar_valor()
{
	if(codSap2 == '')
	{
	alert('Por favor seleccione un Producto y oprima Aceptar');
	}
	else{
	window.opener.formulario.codSap.value = codSap2;
	window.opener.formulario.nomProv.value = nomProv2;
	window.opener.formulario.codProv.value = codProv2;
	window.opener.formulario.valorMax.value = valorMax2;
	window.opener.formulario.descProd.value = descProd2;
	window.opener.formulario.numero_dias.value = numDias2;	
	
	if(numDias2 != '')
	{
	window.opener.formulario.botonE.value = 'Modificar';	
	window.opener.formulario.botonElim.disabled = false;	
	window.opener.formulario.accion.value = 'Modificar';	
	}
	else{
		window.opener.formulario.accion.value = 'Insertar';	
	}	
	
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
<p align="center" class="titulonormal"><strong>BUSQUEDA DE PRODUCTO PS COMO PE </strong></p>
<form name="forma" method="post" action="seleccionar_prodPs_Pe.php" onSubmit="return validar();">
  <table width="287" border="0" align="center">
    <tr class="textoespecial1">
      <td width="106" class="subtitulonormal"><div align="right" class="tabla1">C&oacute;digo Sap:</div></td>
      <td width="171"><div align="left">
        <input name="codSap" type="text" id="codSap" maxlength="15">
      </div></td>
    </tr>


    <tr>
      <td><div align="left" class="tabla2"></div></td>
      <td>&nbsp;</td>
    </tr>
	
	
    <tr>
      <td>&nbsp;</td>
      <td><input type="submit" name="Submit" value="Consultar"></td>
    </tr>
  </table>
  

  
</form>

	<script LANGUAGE="javascript">			
			window.document.forma.codSap.onkeypress = KeyIsNumber;			
	</script>


<table width="800" border="1" align="center" class="tabla1">
  <tr class="textoespecial1">
    <td width="66"><div align="left" class="tabla2">C&oacute;digo Sap </div></td>
    <td width="288"><div align="left" class="tabla2">Descripci&oacute;n Producto </div></td>
	<td width="95"><div align="left" class="tabla2">C&oacute;digo Proveedor</div></td>
	<td width="131"><div align="left" class="tabla2">Nombre Proveedor </div></td>
		<td width="90"><div align="left"><span class="tabla2">Valor M&aacute;ximo</span></div></td>
		<td width="90"><div align="left" class="tabla2">N&uacute;mero de D&iacute;as </div></td>
  </tr>
  
  
<?php 
  while($ResulClie = tep_db_fetch_array($ecliente))
  {
  
?>															
 
  <tr id="<?php echo $ResulClie['cod_prod1'] . $ResulClie['cod_prov']; ?>" bgcolor="#E9E9E9" class="tabla2" onClick="concatenar(this.getElementsByTagName('td')[0].innerHTML,  this.getElementsByTagName('td')[3].innerHTML, this.getElementsByTagName('td')[2].innerHTML, this.getElementsByTagName('td')[4].innerHTML, this.getElementsByTagName('td')[1].innerHTML, this.getElementsByTagName('td')[5].innerHTML); cambiar_color_over(this); cambiar_color(id);" >
    <td><?php echo $ResulClie['cod_prod1']; ?></td>
    <td><?php echo $ResulClie['des_larga']; ?></td>
	<td><?php echo $ResulClie['cod_prov']; ?></td>
	<td><?php echo $ResulClie['nom_prov']; ?></td>
	<td><?php echo $ResulClie['cantidadventaXvolumen']; ?></td>
	<td><?php echo $ResulClie['numero_dias']; ?></td>	
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