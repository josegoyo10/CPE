<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title></title>
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
<body onload='imprimir();'>
<div id='helper_iframe' name='helper_iframe' style='display:none'>
<!--  Se instancia la funcio que ejecuta las ventanas emergentes -->
<script language="JavaScript" type="text/JavaScript" src="../nueva_cotizacion/popup.js"></script>
<script type="text/javascript">

var isFirefox = testCSS('MozBoxSizing');                 // FF 0.8+
var isIE = /*@cc_on!@*/false || testCSS('msTransform');  // At least IE6
function testCSS(prop) {
    return prop in document.documentElement.style;
}

if( !isIE )
{


	function hookKeyboardEvents(e) {
		// get key code            
		var key_code = (window.event) ? event.keyCode : e.which;

		// case :if it is IE event
			if (window.event)
			{
				if (!event.shiftKey && !event.ctrlKey) {
						window.event.returnValue = null;
						event.keyCode = 0;
					}
			}
			// case: if it is firefox event
			else
					e.preventDefault();
	}

	window.document.onkeydown = hookKeyboardEvents;
}


if( isIE ) {
	document.onkeydown = function () { 
		if (event.keyCode == 17) alert('No se puede imprimir la guia mas de una vez.'); 
		  if (event.ctrlKey && event.keyCode == 80) {

						  alert('No se puede imprimir la guia mas de una vez.');

						  window.event.returnValue = null; 

						  event.keyCode = 0; 

		  }
	};
}

</script>
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

// Esta Función abre pantalla de validar Reimpresión.
function abrir_popop(id_ot, reimpresion)
{
	if(confirm("Algunas de las Guias de Despacho ya fuerón impresas.\n¿Desea Reimprimirlas?\n"+ id_ot))
	{
	popUpWindow('validar_reimpresion.php', 50, 50, 320, 200);
  //window.close();
  parent.windows_close();
	}
	else
	{
	if(reimpresion == 1)
    parent.windows_close();
		//window.close();
	//	else
//		document.write("<body onload='imprimir();'><div id='helper_iframe' name='helper_iframe' style='display:none'>");
	}
}
	
// Función que envia la Impresión
function enviar_impresion()
{
//document.write("<body onload='imprimir(); '><div id='helper_iframe' name='helper_iframe' style='display:none'>");
}

function imprimir() 
{
	this.focus();  	
	setTimeout("imprimirsinasistente();",1000);	
} 	 
</script>

<!--  Funcion que omite la seleccion de impresora, y envía a la impresora Predeterminada del Sistema. -->
<script Language="Javascript">
function imprimirsinasistente(){  
document.getElementById('helper_iframe').style.display='';
if (NS) {
    window.print();  
} else {
    var WebBrowser = '<OBJECT ID="WebBrowser1" WIDTH=0 HEIGHT=0 CLASSID="CLSID:8856F961-340A-11D0-A96B-00C04FD705A2"></OBJECT>';
	document.body.insertAdjacentHTML('beforeEnd', WebBrowser);
    WebBrowser1.ExecWB(6, 2);
  	WebBrowser1.outerHTML = "";    
	}
   parent.windows_close();
  //window.close();
  
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
	session_start();	
	
	$SIN_PER = 1; $longitud=0; $cadena=0;
	include_once('../../includes/aplication_top.php');
	
	if($_SESSION['id_ot_old'] == 0)
	{
	$_SESSION['id_ot_old'] = $_SESSION['id_ot2'];	
	}
	
	$id_ot13 = $_SESSION['id_ot2'];	
			
	$consulta_ot = "SELECT numero_impresionesGuia, ot_id FROM ot WHERE ot_id IN ($id_ot13) AND numero_impresionesGuia >= 1";	
	$resutadoOT = tep_db_query($consulta_ot); 
	
	$result_reimp = '';
	while($resultado_ot = tep_db_fetch_array( $resutadoOT ))
	{
		$result_reimp = $result_reimp . ',' . $resultado_ot['ot_id'];
	}
	$result_reimp = substr($result_reimp, 1);	
	
	if($_SESSION['result_reimpresines'] == 0)
	{
	$_SESSION['result_reimpresines'] = $result_reimp; 
	}
	if($_SESSION['reimpresion']== 1)
	{
	echo "<script language=JavaScript>";
	echo "enviar_impresion()";
	echo "</script>";
	}
	
	if($_SESSION['id_ot_old'] != 0)	
	$id_ot13 = $_SESSION['id_ot_old'];
	
	$reimpTeo = 1;
	
	if($_SESSION['reimpresion']== 0)
	{
		$_SESSION['teo_reimp'] = 1;
		$consulta_ot = "SELECT numero_impresionesGuia, ot_id FROM ot WHERE ot_id IN ($id_ot13) AND numero_impresionesGuia = 0";	
		$resutadoOT = tep_db_query($consulta_ot);	
	
	$result = '';
		while($resultado_ot = tep_db_fetch_array( $resutadoOT ))
		{
		$result = $result . ',' . $resultado_ot['ot_id'];
		}
		$id_ot13 = substr($result, 1);
	
		if($id_ot13 != '')
		{
		$reimpTeo = 0;
		$_SESSION['teo_reimp'] = 0;
		}
	}
		
	if($result_reimp != '' && isset($_SESSION['reimpresion']) && $_SESSION['reimpresion']== 0)
	{
	echo "<script language=JavaScript>";
	echo "abrir_popop('$result_reimp', '$reimpTeo');";
	echo "</script>";
	}
	else{    
		echo "<script language=JavaScript>";
		echo "enviar_impresion()";
		echo "</script>";	
	}	
	
	// Cuenta las id_od que vienen en la cadena.
	$longitud = count(split(",",$id_ot13));
	// Obtiene las id_ot de la cadena separandolas por ,.
	$cadena = split(",",$id_ot13);
	
	
	// Imprime todas las id_ot.

	for ($i=0; $i<$longitud; $i++){ 
		
	// Inicio de las consultas a base de datos
		//Query de la Orden de trabajo.
		$query = "SELECT ot_id, id_os, ot_fechacreacion, e.esta_nombre, ot_tipo, numero_impresionesGuia, usu_autorizacion_reimpresion
						FROM ot 
						JOIN estados e on e.id_estado = ot.id_estado
						WHERE ot.ot_id =".($cadena[$i]+0);
		$rq = tep_db_query($query); 
    	$res = tep_db_fetch_array( $rq );
		
		//Query de usuario que realiza autorizacion de impresión.
		$queryUsu = "SELECT CONCAT(usr_nombres,' ',usr_apellidos) AS autorizado
						FROM usuarios
						WHERE usr_login = '" . $_SESSION['usu_reimpresion'] . "'";
		$rqUsu = tep_db_query($queryUsu); 
    	$resUsu = tep_db_fetch_array( $rqUsu );
		
		$usu_autorizacion_reimpresion = $resUsu['autorizado'];
		
    	// Se inicia el proceso de verificacion de Re-impresion
    	
    	// Todas las ordenes son de no Reimpresión
    	if($_SESSION['reimpresion']== 0 && $result_reimp == '')
		{
		$numero_impresiones = $res['numero_impresionesGuia'] + 1;
    	$consul_ot = "UPDATE ot SET numero_impresionesGuia = " .  $numero_impresiones . ", usu_autorizacion_reimpresion = '" . $usu_autorizacion_reimpresion . "' WHERE ot_id = " . $cadena[$i];
		$rq_ot = tep_db_query($consul_ot);    	
    	}
		
		$no_reimpresion = $passwd_list = split( ",", $result_reimp);
		$numero = count($no_reimpresion);		
		
		// Hay ordenes de reimpresión y de no Reimpresión
		if($_SESSION['reimpresion']== 1 && $result_reimp == '')
		{
			$verdad = 1;
			for($m=0; $m<$numero; $m++ )
			{
				if ($no_reimpresion[$m] == $cadena[$i])
				$verdad = 0;
			}
			
			if($verdad)
			{
			$numero_impresiones = $res['numero_impresionesGuia'] + 1;
	    	$consul_ot = "UPDATE ot SET numero_impresionesGuia = " .  $numero_impresiones . ", usu_autorizacion_reimpresion = '" . $usu_autorizacion_reimpresion . "' WHERE ot_id = " . $cadena[$i]; 
			$rq_ot = tep_db_query($consul_ot);    		    	
			}
		}
		
		// Hay ordenes de reimpresión y de no Reimpresión		
		if($_SESSION['reimpresion']== 0 && $result_reimp != '')
		{			
			$numero_impresiones = $res['numero_impresionesGuia'] + 1;
	    	$consul_ot = "UPDATE ot SET numero_impresionesGuia = " .  $numero_impresiones . ", usu_autorizacion_reimpresion = '" . $usu_autorizacion_reimpresion . "' WHERE ot_id = " . $cadena[$i]; 
			$rq_ot = tep_db_query($consul_ot);		
		}
		
		
		$no_reimpresion2 = split( ",", $_SESSION['result_reimpresines']);
		$numero2 = count($no_reimpresion2);				
		
		// Hay ordenes de Reimpresión y de no Reimpresión
		if($_SESSION['reimpresion']== 1 && $result_reimp != '')
		{
			$verdad = 0;
						
			if ($_SESSION['result_reimpresines'] != $result_reimp)
			{				
				for($m=0; $m<$numero2; $m++ )
				{
					if ($no_reimpresion2[$m] == $cadena[$i])
					$verdad = 1;
				}
			}
			
			if ($_SESSION['result_reimpresines'] == $result_reimp)
			{
				$verdad = 1;
			}
			
			if($verdad)
			{
			$numero_impresiones = $res['numero_impresionesGuia'] + 1;
	    	$consul_ot = "UPDATE ot SET numero_impresionesGuia = " .  $numero_impresiones . ", usu_autorizacion_reimpresion = '" . $usu_autorizacion_reimpresion . "' WHERE ot_id = " . $cadena[$i]; 
			$rq_ot = tep_db_query($consul_ot);	    	
			}
	}

	
		// Inicio de las consultas a base de datos
		//Query de las Guias de Despacho.
		$query = "SELECT ot_id, id_os, date_format(ot_fechacreacion, '%d/%m/%Y ')ot_fechacreacion, e.esta_nombre, ot_tipo, usu_autorizacion_reimpresion, numero_impresionesGuia
						FROM ot 
						JOIN estados e on e.id_estado = ot.id_estado
						WHERE ot.ot_id =".($cadena[$i]+0); 
		$rq = tep_db_query($query); 
    	$res = tep_db_fetch_array( $rq ); 
		
		// Consulta la direccción de Servicio de la cotización.
		$id_os = $res['id_os'];
		$queryDir="SELECT O.id_direccion, D.dire_telefono, D.dire_direccion, D.dire_observacion
					FROM os O
					JOIN direcciones D ON D.id_direccion=O.id_direccion
					WHERE id_os =".$id_os;
		$osSelDir = tep_db_query($queryDir);
		$osSelDire = tep_db_fetch_array( $osSelDir );
		$dirServ = consulta_localizacion($osSelDire['id_direccion'],2);
		$dirServicio = getlocalizacion($dirServ);

	     $query1="SELECT ot.ot_id, ot.id_os as id_os, ot_tipo, td.nombre as nombre_despacho , ot_fechacreacion,esta_nombre,ot.id_estado,date_format(ot_fechacreacion, '%d/%m/%Y  %H:%m:%s')OTF, os.id_local,  nom_local,ot.id_tipodespacho,ot.id_estado,esta_tipo,ot.numero_impresionesGuia
					FROM ot JOIN os on os.id_os = ot.id_os 
					JOIN estados e on e.id_estado = ot.id_estado 
					JOIN locales l on l.id_local = os.id_local 
					JOIN tipos_despacho td on td.id_tipodespacho = ot.id_tipodespacho 
					where ot_tipo in ('PS','PE') and  ot.ot_id=".$cadena[$i];
		$rq1 = tep_db_query($query1);
		$res1 = tep_db_fetch_array( $rq1 );
		
		// Query del despacho
    	$query2 = "SELECT distinct os.id_direccion, os.clie_rut, CL.clie_nombre, CL.clie_paterno, CL.clie_materno, CL.clie_telcontacto2, dire_nombre, dire_direccion, dire_telefono, dire_observacion, DS.nombre
					FROM os  
						JOIN clientes CL 		ON CL.clie_rut = os.clie_rut
						JOIN os_detalle OD 		ON OD.id_os = os.id_os
						JOIN tipos_despacho DS	ON DS.id_tipodespacho = OD.id_tipodespacho 
						LEFT JOIN direcciones D ON D.id_direccion = os.id_direccion
					WHERE OD.ot_id= ".$cadena[$i];
		$rq2 = tep_db_query($query2);
		$res2 = tep_db_fetch_array( $rq2 );
?>
	<!-- Inicio Formulario de Impresion --> 
	<div class="pagina">

		<div class="content">
			<div style="float:left; width:100px;"><img src="../img/logo3.gif" width="95" height="55" /></div>
			<div class="Estilo1" style="float:left; width:300px;"><span class="Estilo2">&nbsp;&nbsp;GUIA DE DESPACHO N&deg;</span><? echo "$cadena[$i]"; ?></div>
			<? 
			$verdad = 0;
			for($m=0; $m<$numero; $m++ )
			{
				if ($no_reimpresion2[$m] == $cadena[$i])
				$verdad = 1;
			}
				if ($verdad){ ?>
					<div id="rePrint" class="Estilo1" style="float:right; width:130px;"><span class="Estilo2">Reimpresión # </span><? echo $res['numero_impresionesGuia']; ?></div>
					<div id="rePrint" class="Estilo1" style="float:left; width:250px;padding:10px;"><span class="Estilo2">Autorizado Por:&nbsp;&nbsp;</span><? echo $usu_autorizacion_reimpresion; ?></div>
					<? } ?>
		</div>
		
		<div class="content">
		<form action="#" method="post" class="contenedor">
          <fieldset style=" width:620px;">
          <span class="Estilo2">
          <legend> Datos de la Gu&iacute;a  de Despacho </legend>
          </span>
          <table width="620" height="111" border="0" cellpadding="2" cellspacing="1">
            <tr>
              <td width="130" height="28" class="Estilo2">N&deg;. Orden de Trabajo </td>
              <td width="180" class="Estilo1"><div align="left" class="Estilo1"> <span class="Estilo4"><? echo $res['ot_id'] ?></span> </div></td>
              <td width="105" class="Estilo2">Fecha de Entrega </td>
              <td width="126" class="Estilo1"><? echo $res['ot_fechacreacion'] ?></td>
            </tr>
            <tr>
              <td height="14" class="Estilo2">Estado</td>
              <td class="Estilo1"><? echo $res['esta_nombre'] ?></td>
              <td class="Estilo2">&nbsp;</td>
              <td class="Estilo1">&nbsp;</td>
            </tr>
            <tr>
              <td height="14" class="Estilo2">Tienda</td>
              <td class="Estilo1"><? echo $res1['nom_local'] ?></td>
              <td class="Estilo2">&nbsp;</td>
              <td class="Estilo1">&nbsp;</td>
            </tr>
            <tr>
              <td height="26" class="Estilo2">Tipo de Entrega </td>
              <td class="Estilo1"><? echo $res2['nombre'] ?></td>
              <td class="Estilo2">&nbsp;</td>
              <td class="Estilo1">&nbsp;</td>
            </tr>
          </table>
          </fieldset>
          <fieldset style=" width:620px;">
          <legend class="Estilo2">Datos del Cliente </legend>
          <table width="620" height="auto" border="0" cellpadding="2" cellspacing="1">
            <tr>
              <td width="130" class="Estilo2">Nombre del Cliente </td>
              <td width="180" class="Estilo1"><? echo $res2['clie_nombre'].' '.$res2['clie_paterno'].' '.$res2['clie_materno'] ?></td>
              <td width="105" class="Estilo2">C&eacute;dula</td>
              <td width="126" class="Estilo1"><? echo $res2['clie_rut'] ?></td>
            </tr>
            <tr>
              <td class="Estilo2"> Tel&eacute;fono </td>
              <td class="Estilo1"><? echo $res2['dire_telefono'] ?></td>
              <td class="Estilo2">Barrio</td>
              <td class="Estilo1"><span class="Estilo4"><? echo $dirServicio['barrio'] ?> </span></td>
            </tr>
            <tr>
              <td class="Estilo2">Tel&eacute;fono Celular </td>
              <td class="Estilo1"><? echo $res2['clie_telcontacto2'] ?></td>
              <td class="Estilo2">Localidad</td>
              <td class="Estilo1"><span class="Estilo4"><? echo $dirServicio['localidad'] ?></span></td>
            </tr>
            <tr>
              <td class="Estilo2">Direcci&oacute;n del Servicio </td>
              <td><span class="Estilo1"><? echo $osSelDire['dire_direccion'] ?></span></td>
              <td><span class="Estilo2">Ciudad</span></td>
              <td><span class="Estilo4"><? echo $dirServicio['ciudad'] ?></span></td>
            </tr>
            <tr>
              <td><span class="Estilo2">Tel&eacute;fono</span></td>
              <td class="Estilo1"><? echo $osSelDire['dire_telefono'] ?></td>
              <td><span class="Estilo2">Departamento</span></td>
              <td><span class="Estilo4"><? echo $dirServicio['departamento'] ?></span></td>
            </tr>
            <tr>
              <td><span class="Estilo2">Indicaciones</span></td>
              <td colspan="3"><span class="Estilo4"><? echo $res2['dire_observacion'] ?></span></td>
            </tr>
          </table>
          </fieldset>
	  	  </form>
		</div>
		
		<div class="content">
		  <table width="620">
		   <tr>
            <th width="105" bgcolor="#CCCCCC"><div align="center" class="Estilo2"> CODIGO</div>
            <th width="249" bgcolor="#CCCCCC"><div align="center" class="Estilo2">DESCRIPCION</td></div>
            <th width="65" bgcolor="#CCCCCC"><div align="center" class="Estilo2">CANTIDAD COMPRADA</div>
            <th width="69" bgcolor="#CCCCCC"><div align="center" class="Estilo2">CANTIDAD ENTREGADA</div></tr>
		</table>
          <table width="620" border="0" align="center" cellpadding="2" cellspacing="1">
		   <tr><td colspan="5">
			   <? // Query del detalle de la OD
					$query3 = "SELECT cod_barra, cod_sap, osde_descripcion, osde_precio, osde_cantidad, osde_tipoprod,osde_especificacion,cant_pickeada 
					,if(cant_pickeada='','0',cant_pickeada)cant_pickeada
					,if(osde_tipoprod='PE',osde_cantidad,cant_pickeada)cant_pickeada 
					FROM os_detalle osd
					JOIN tipos_despacho t on t.id_tipodespacho = osd.id_tipodespacho WHERE ot_id =".$cadena[$i];
			
					$rq3 = tep_db_query($query3);
					$tot_prod = tep_db_num_rows( $rq3 );
					while( $res3 = tep_db_fetch_array( $rq3 ) ) { 
						 echo'<table width="620" border="0">';
							echo'<tr>';
							echo'<td width="105" height="20" class="borde"><div align="left" class="Estilo1">'.$res3['cod_barra'].'/<br>'.$res3['cod_sap'].'</div></td>';
							echo'<td width="230" height="20" class="borde"><div align="left" class="Estilo1">'.substr($res3['osde_descripcion'],0,40).'</div></td>';
							echo'<td width="80" height="20" class="borde"><div align="right" class="Estilo1">'.$res3['osde_cantidad'].'</div></td>';
							echo'<td width="70" height="20" class="borde"><div align="right" class="Estilo1">'.$res3['cant_pickeada'].'</div></td>';
							echo'</tr>';
						echo'</table>';
					}
					$can_totProd = $can_totProd + $res3['osde_cantidad'];
			?>
		   </td></tr>
		   <tr>
		   <td width="178">&nbsp;</td>
		   <td width="87">&nbsp;</td>
		   <td width="74">&nbsp;</td>
		   <td width="72" class="Estilo2">&nbsp;</td>
		   <td width="134" align="left" class="Estilo4">&nbsp;</td>
		   </tr>
		   <tr>
		   <td><span class="Estilo2">Persona que Despach&oacute;: </span></td>
		   <td colspan="4">___________________________</td>
		   </tr>
			<tr>
		   <td height="20" class="Estilo2">Persona de Seguridad:</td>
		   <td height="20" colspan="4" class="Estilo2">_______________________________</td>
		   </tr>
			<tr>
			  <td colspan="2" height="20" class="Estilo2">Firma del Cliente: </td>
			  <td height="20" colspan="3" class="Estilo2">Persona que recibe: </td>
	        </tr>
			<tr>
			  <td colspan="2" height="46" class="Estilo2"><p>_________________________________ </p>
		      </td>
			  <td colspan="3" height="46" class="Estilo2"><p>______________________________________ </p>
		      </td>
	        </tr>
			<tr>
			  <td height="28" class="Estilo2">DETALLE DE QUIEN RECIBE </td>
			  <td height="28" colspan="4" class="Estilo2"><div align="left">_____________________________________________________</div></td>
		    </tr>
			<tr>
			  <td height="21" class="Estilo2">OBSERVACIONES DE ENTREGA </td>
			  <td height="21" colspan="4" class="Estilo2"><div align="left">_____________________________________________________</div></td>
		    </tr>
			<tr>
			<td height="26" colspan="5"> <div align="left">________________________________________________________________________________</div></td>
			</tr>
			<tr>
			  <td colspan="5" height="20"><div align="left">________________________________________________________________________________</div></td>
		    </tr>
			<tr>
			  <td colspan="5" height="20"><div class="piepag">
                <div align="center" class="Estilo3">Para EASY Colombia es muy importante conocer las novedades que pueda tener con su entrega. Gracias por sus comentarios nos ser&aacute;n de gran ayuda para mejorar nuestro servicio.</div>
		      </div></td>
		    </tr>
		  </table>
		</div>
	</div> 
<?php 
	if ($i < $longitud - 1)
	{		
	echo'<H1 class="SaltoDePagina"> </h1>';
  	}
}
  $_SESSION['reimpresion'] = 0;
  $_SESSION['id_ot_old'] = 0;
  //$_SESSION['result_reimpresines'] = 0;
?>
<!-- Fin Formulario de Impresion -->
</div>
</body>
</html>