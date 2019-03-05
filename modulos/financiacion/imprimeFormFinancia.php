<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<body>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title></title>
<link rel="stylesheet" type="text/css" href="../estilos.css">
<style type="text/css">
body {
	margin-left: 0px;
	margin-top: 0px;
}
.Estilo1 {
	font-family:Verdana, Arial, Helvetica, sans-serif;
	font-size:10px;
	font-style:normal;
}
.Estilo2 {
	font-family:Verdana, Arial, Helvetica, sans-serif;
	font-size:10px;
	font-style:normal;
	font-weight:bold;
}
.Estilo3 {
	font-family:Verdana, Arial, Helvetica, sans-serif;
	font-size:9px;
	font-style:normal;
	font-weight:bold;
}
.Estilo4 {
	font-family:Verdana, Arial, Helvetica, sans-serif;
	font-size:10px;
	font-style:normal;
}
.pagina{
	width: 750px;
	height: 865px;
	margin-top: 0 0 0 0;
	border: 1px;
}
H1.SaltoDePagina
{
	PAGE-BREAK-AFTER: always;
}
.content{
	width: 620px;
	height: auto;
	margin: 0 0 0 0;
	float:inherit;
}
.borde{
	border-width: 1px;
	border-style:solid;
	border-color:#999999;
}
</style>
</head>

<!--  Se instancia la funcio que ejecuta las ventanas emergentes -->
<script language="JavaScript" type="text/JavaScript" src="../nueva_cotizacion/popup.js"></script>

<script language=JavaScript>
<!-- Deshabilita Botón derecho del Mouse en popop de Impresión -->
var message="";

function clickIE() {if (document.all) {(message);return false;}}
function clickNS(e) {if 
(document.layers||(document.getElementById&&!document.all)) {
if (e.which==2||e.which==3) {(message);return false;}}}
if (document.layers) 
{document.captureEvents(Event.MOUSEDOWN);document.onmousedown=clickNS;}
else{document.onmouseup=clickNS;document.oncontextmenu=clickIE;}

document.oncontextmenu=new Function("return false")

// Función que envia la Impresión
function enviar_impresion()
{
document.write("<body onload='imprimir(); window.close();'>");
}

function imprimir() 
{
	this.focus();
	imprimirsinasistente();	
} 	 
</script>

<!--  Funcion que omite la seleccion de impresora, y envía a la impresora Predeterminada del Sistema. -->
<script Language="Javascript">
function imprimirsinasistente(){  
if (NS) {
    window.print();  
} else {
    var WebBrowser = '<OBJECT ID="WebBrowser1" WIDTH=0 HEIGHT=0 CLASSID="CLSID:8856F961-340A-11D0-A96B-00C04FD705A2"></OBJECT>';
	document.body.insertAdjacentHTML('beforeEnd', WebBrowser);
    WebBrowser1.ExecWB(6, 2);
  	WebBrowser1.outerHTML = "";  
	}
}
</script>
<SCRIPT Language="Javascript">  
var NS = (navigator.appName == "Explorer");
var VERSION = parseInt(navigator.appVersion);
if (VERSION < 3) {
	alert ('verifique que este activo el control "Activex no Firmadas", si su version de explorer es inferior a la requerida consulte con el administrador');        
	}
</script>
	
<?php
	$SIN_PER = 1;
	include_once('../../includes/aplication_top.php');
	
	$vars = $_GET['vars'];
	$datos = split(',',$vars);
	$id_os = $datos[10];

	echo "<script language=JavaScript>enviar_impresion()</script>";
	
		//Query del texto del Fromato
	  	$query1 = "SELECT VAR_VALOR FROM glo_variables WHERE VAR_LLAVE = 'CHEQ_POS_TEXT';";
		$rq1 = tep_db_query($query1);
		$res1 = tep_db_fetch_array( $rq1 );
		
	// Query del despacho
    	$query2 = "SELECT distinct os.clie_rut, CONCAT(CL.clie_nombre,' ',CL.clie_paterno,' ',CL.clie_materno) AS cliente, DATE_FORMAT(os_fechacreacion, '%d/%m/%Y') AS fecha, L.nom_local
				   FROM os
				   JOIN clientes CL ON CL.clie_rut = os.clie_rut
				   JOIN os_detalle OD ON OD.id_os = os.id_os
				   LEFT JOIN direcciones D ON D.id_direccion = os.id_direccion
                   LEFT JOIN locales L ON L.id_local = os.id_local
				   WHERE OD.id_os=".$id_os;
		$rq2 = tep_db_query($query2);
		$res2 = tep_db_fetch_array( $rq2 );
?>
	<!-- Inicio Formulario de Impresion --> 
	<div class="pagina">
		<div class="content" style="margin-bottom: 20px;">
			<div style="float:left; width:100px;"><img src="../img/logo3.gif" width="95" height="55" /></div>
			<div class="Estilo1" style="float:center; width:450px; padding-top:20px;">
				<span class="titulonormalnegro" style="padding-left: 150px; padding-bottom: 50px;">COMPROBANTE DE FINANCIAMIENTO</span>
			</div>
		</div>
		
		<div class="content">
		<form action="#" method="post" class="contenedor">
          <fieldset style=" width:500px; margin-left: 50px; margin-bottom: 40px; padding:0px;">
          <legend class="Estilo2">Datos del Cliente </legend>
          <table width="500" height="auto" border="0" cellpadding="5" cellspacing="1">
            <tr>
              <td width="130" class="Estilo2">Fecha </td>
              <td width="180" class="Estilo1"><? echo $res2['fecha'] ?></td>
              <td width="105" class="Estilo2">Tienda </td>
              <td width="126" class="Estilo1"><? echo $res2['nom_local'] ?></td>
            </tr>
            <tr>
              <td class="Estilo2">N°.Identificación </td>
              <td class="Estilo1"><? echo $res2['clie_rut'] ?></td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td class="Estilo2">Nombre </td>
              <td colspan="3" class="Estilo1"><? echo $res2['cliente'] ?></td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td></td>
              <td></td>
            </tr>
          </table>
          </fieldset>
	  	  </form>
		</div>
		
		<div class="content">
		  <table width="620" align="center" class="tabla1">
		   <tr>
            <th width="105" class="tabla1 th">CODIGO</th>
            <th width="249" class="tabla1 th">DESCRIPCIÓN</th>
            <th width="65" class="tabla1 th">PRECIO</th>
            <th width="65" class="tabla1 th">CANTIDAD</th>
            <th width="70" class="tabla1 th">SUBTOTAL</th>
           </tr>
		</table>
          <table style=" width= 620px;" border="0" align="center" cellpadding="2" cellspacing="1">
		   <tr><td colspan="5">
			   <? // Query del detalle de la OD
					$query3 = "SELECT cod_barra, cod_sap, osde_descripcion, osde_precio, osde_cantidad, osde_tipoprod,osde_especificacion,cant_pickeada 
					,if(cant_pickeada='','0',cant_pickeada)cant_pickeada
					,if(osde_tipoprod='PE',osde_cantidad,cant_pickeada)cant_pickeada 
					FROM os_detalle osd
					JOIN tipos_despacho t on t.id_tipodespacho = osd.id_tipodespacho WHERE id_os =".$id_os;
			
					$rq3 = tep_db_query($query3);
					$tot_prod = tep_db_num_rows( $rq3 );
					while( $res3 = tep_db_fetch_array( $rq3 ) ) { 
						 echo'<table width="620" border="0">';
							echo'<tr>';
							echo'<td width="99" height="20" class="borde"><div align="left" class="Estilo1">'.$res3['cod_barra'].'/<br>'.$res3['cod_sap'].'</div></td>';
							echo'<td width="235" height="20" class="borde"><div align="left" class="Estilo1">'.substr($res3['osde_descripcion'],0,40).'</div></td>';
							echo'<td width="65" height="20" class="borde"><div align="right" class="Estilo1">'.number_format($res3['osde_precio'], 0, '', '.').'</div></td>';
							echo'<td width="65" height="20" class="borde"><div align="right" class="Estilo1">'.$res3['osde_cantidad'].'</div></td>';
							echo'<td width="70" height="20" class="borde"><div align="right" class="Estilo1">'.number_format(($res3['osde_precio']*$res3['osde_cantidad']), 0, '', '.').'</div></td>';
							echo'</tr>';
						echo'</table>';
					}
					$can_totProd = $can_totProd + $res3['osde_cantidad'];
			?>
		   </td></tr>
		   <tr><td>
				<table width="620" align="left" border="0">
				   	<tr>
				   		<td colspan="3"></td>
						<td width="120"><div align="left" class="Estilo1">Total:</div></td>
		            	<td width="110"><div align="right" class="Estilo1">$ <?echo $datos[0];?></div></td>
					</tr>
					<tr>
				   		<td colspan="3"></td>
						<td width="120"><div align="left" class="Estilo1">Cuota inicial:</div></td>
		            	<td width="110"><div align="right" class="Estilo1">$ <?echo $datos[1];?></div></td>
					</tr>
					<tr>
				   		<td colspan="3"></td>
						<td width="120"><div align="left" class="Estilo1">N°.Cheques:</div></td>
		            	<td width="110"><div align="right" class="Estilo1"><?echo $datos[2];?></div></td>
					</tr>
				   	<tr>
				   		<td colspan="3"></td>
						<td width="120"><div align="left" class="Estilo1">Valor a financiar:</div></td>
		            	<td width="110"><div align="right" class="Estilo1">$ <?echo $datos[3];?></div></td>
					</tr>
					<tr>
				   		<td colspan="3"></td>
						<td width="120"><div align="left" class="Estilo1">Int. de Financiación:</div></td>
		            	<td width="110"><div align="right" class="Estilo1"><?echo $datos[4];?>% M.V</div></td>
					</tr>
					<tr>
				   		<td colspan="3"></td>
						<td width="120"><div align="left" class="Estilo2">Valor x Cheque:</div></td>
		            	<td width="110"><div align="right" class="Estilo2">$ <?echo $datos[5];?></div></td>
					</tr>
					<tr>
				   		<td colspan="3"></td>						
		            	<td colspan="2"><hr></td>
				   	</tr>
				   	<tr>
				   		<td colspan="3"></td>
						<td width="120"><div align="left" class="Estilo2">Valor Intereses:</div></td>
		            	<td width="110"><div align="right" class="Estilo2">$ <?echo $datos[7];?></div></td>
					</tr>
					<tr>
				   		<td colspan="3"></td>
						<td width="120"><div align="left" class="Estilo1">IVA Intereses:</div></td>
		            	<td width="110"><div align="right" class="Estilo1">$ <?echo $datos[8];?></div></td>
					</tr>
					<tr>
				   		<td colspan="3"></td>
						<td width="120"><div align="left" class="Estilo1">Base Intereses:</div></td>
		            	<td width="110"><div align="right" class="Estilo1">$ <?echo $datos[9];?></div></td>
					</tr>
				</table>
		   </td></tr>
		   <tr><td></td><tr>
		   <tr>
		   	<td colspan="5"><hr></td>
		   </tr>
		   <tr>
		   	<td colspan="5">
		   		<div align="center" class="Estilo1"><? echo $res1['VAR_VALOR'] ?></div>
		   	</td>
		   </tr>
		  </table>
		</div>
	</div> 
<!-- Fin Formulario de Impresion -->
</body>
</html>