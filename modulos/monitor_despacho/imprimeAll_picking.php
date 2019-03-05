<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title></title>
<style type="text/css">
body {
	margin-left: 0px;
	margin-top:  0px;
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
.pagina{
	width: 750px;
	height: 860px;
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
	margin: 0px 0 0 0;
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

document.oncontextmenu=new Function("return false");

//Esta Función abre pantalla de validar Reimpresión  -->
function abrir_popop(id_ot, reimpresion)
{
	if(confirm("Algunas de las Ordenes de Picking ya fuerón impresas.\n¿Desea Reimprimirlas?\n"+ id_ot))
	{
	popUpWindow('validar_reimpresion.php', 50, 50, 320, 200);
	}
	else
	{
		if(reimpresion == 1)
		window.close();
		else
		document.write("<body onload='imprimir(); window.close();'>");
	}
}

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
	session_start();	
	
	$SIN_PER = 1; $longitud=0; $cadena=0;
	include_once('../../includes/aplication_top.php');
	
	if($_SESSION['id_ot_old'] == 0)
	{
	$_SESSION['id_ot_old'] = $_SESSION['id_ot2'];	
	}
	
	$id_ot13 = $_SESSION['id_ot2'];	
	
			
	$consulta_ot = "SELECT numero_impresiones, ot_id FROM ot WHERE ot_id IN ($id_ot13) AND numero_impresiones >= 1";	
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
		$consulta_ot = "SELECT numero_impresiones, ot_id FROM ot WHERE ot_id IN ($id_ot13) AND numero_impresiones = 0";	
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
		$query = "SELECT ot_id, id_os, numero_impresiones, usu_autorizacion_reimpresion
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
		$numero_impresiones = $res['numero_impresiones'] + 1;
    	$consul_ot = "UPDATE ot SET numero_impresiones = " .  $numero_impresiones . ", usu_autorizacion_reimpresion = '" . $usu_autorizacion_reimpresion . "' WHERE ot_id = " . $cadena[$i];
		$rq_ot = tep_db_query($consul_ot);    	
    	}
		
		$no_reimpresion = split( ",", $result_reimp);
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
			$numero_impresiones = $res['numero_impresiones'] + 1;
	    	$consul_ot = "UPDATE ot SET numero_impresiones = " .  $numero_impresiones . ", usu_autorizacion_reimpresion = '" . $usu_autorizacion_reimpresion . "' WHERE ot_id = " . $cadena[$i]; 
			$rq_ot = tep_db_query($consul_ot);    		    	
			}
		}
		
		// Hay ordenes de reimpresión y de no Reimpresión		
		if($_SESSION['reimpresion']== 0 && $result_reimp != '')
		{			
			$numero_impresiones = $res['numero_impresiones'] + 1;
	    	$consul_ot = "UPDATE ot SET numero_impresiones = " .  $numero_impresiones . ", usu_autorizacion_reimpresion = '" . $usu_autorizacion_reimpresion . "' WHERE ot_id = " . $cadena[$i]; 
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
			$numero_impresiones = $res['numero_impresiones'] + 1;
	    	$consul_ot = "UPDATE ot SET numero_impresiones = " .  $numero_impresiones . ", usu_autorizacion_reimpresion = '" . $usu_autorizacion_reimpresion . "' WHERE ot_id = " . $cadena[$i]; 
			$rq_ot = tep_db_query($consul_ot);	    	
			}
	}
		
    	// Inicio de las consultas a base de datos
		//Query de la Orden de trabajo.
	     $query1 = "SELECT distinct O.ot_id, U.USR_ID, USR_NOMBRES,  USR_APELLIDOS, O.id_tipodespacho, O.id_os, O.ot_fechacreacion, OD.osde_fecha_entrega, O.ot_tipo, E.esta_tipo, E.esta_nombre AS nom_estado, E.id_estado, OS.os_cotizaciones_cruzadas, OS.os_comentarios, 
					 CL.clie_rut, CL.clie_nombre, CL.clie_tipo, CL.clie_paterno, CL.clie_materno, L.nom_local, TD.nombre 
					 FROM ot O 
					 JOIN estados E ON E.id_estado = O.id_estado 
					 JOIN os OS ON OS.id_os = O.id_os 
					 JOIN clientes CL ON CL.clie_rut= OS.clie_rut 
					 LEFT JOIN direcciones D ON D.id_direccion = OS.id_direccion 
					 JOIN os_detalle OD ON OD.id_os = OS.id_os 
					 JOIN tipos_despacho TD ON TD.id_tipodespacho = OD.id_tipodespacho AND TD.id_tipodespacho = ( SELECT id_tipodespacho FROM ot where ot_id = ". $cadena[$i] .") 
					 JOIN locales L ON L.id_local = OS.id_local 
					 LEFT JOIN usuarios U ON U.USR_ID = OS.USR_ID					 
					 WHERE O.ot_id = ".$cadena[$i];
		$rq1 = tep_db_query($query1);
		$res1 = tep_db_fetch_array( $rq1 );
		
		// Registra en el historial todas las impresiones realizadas.
		insertahistorial("Se realiza la impresion de la Orden de Picking por el usuario ".get_nombre_usr( $ses_usr_id ), $res1['id_os'], $cadena[$i], "SYS");

		// Consulta la direccción de Servicio de la cotización.
		$id_os = $res1['id_os'];
		$queryDir="SELECT O.id_direccion, D.dire_telefono, D.dire_direccion, D.dire_observacion
					FROM os O
					JOIN direcciones D ON D.id_direccion=O.id_direccion
					WHERE id_os =".$id_os;
		$osSelDir = tep_db_query($queryDir);
		$osSelDire = tep_db_fetch_array( $osSelDir );
		$dirServ = consulta_localizacion($osSelDire['id_direccion'],2);
		$dirServicio = getlocalizacion($dirServ);
		
		//Establece el Tipo de Orden de Trabajo
		if($res1['ot_tipo'] == 'PS')
			$tipoOT = 'Producto Stock';
			
		else if ($res1['ot_tipo'] == 'SV')
			$tipoOT = 'Servicio';
		
		else if ($res1['ot_tipo'] == 'PE')
			$tipoOT = 'Pedido Especial';
		
?>

	<!-- Inicio Formulario de Impresion -->		
		<div class="content">
			<div style="float:left; width:100px;"><img src="../img/logo3.gif" width="95" height="55" /></div>
			<div class="Estilo1" style="float:left; width:300px;"><span class="Estilo2">&nbsp;&nbsp;Picking Orden de Trabajo # </span><? echo "$cadena[$i]"; ?></div>
			<? 
			$verdad = 0;
			for($m=0; $m<$numero; $m++ )
			{
				if ($no_reimpresion2[$m] == $cadena[$i])
				$verdad = 1;
			}
				if ($verdad){ ?>
					<div id="rePrint" class="Estilo1" style="float:right; width:130px;"><span class="Estilo2">Reimpresión N&deg; </span><? echo $res['numero_impresiones']; ?></div>
					<div id="rePrint" class="Estilo1" style="float:left; width:250px;padding:10px;"><span class="Estilo2">Autorizado Por:&nbsp;&nbsp;</span><? echo $usu_autorizacion_reimpresion; ?></div>
					<? }?>
		</div>
		
		<div class="content">
		<form action="#" method="post" class="contenedor">
          <fieldset style=" width:620px;">
          <span class="Estilo2">
          <legend> Datos Orden de Trabajo </legend>
          </span>
          <table width="620" height="124" border="0" cellpadding="3" cellspacing="2">
            <tr>
              <td height="20" colspan="2" class="Estilo2">No. Orden de Trabajo </td>
              <td width="43" height="20" class="Estilo1"><div align="left" class="Estilo4"> <? echo $res1['ot_id'] ?> </div></td>
              <td width="71" height="20" class="Estilo2">No.<span class="Estilo8">Orden de Servicio </span></td>
              <td width="66" height="20" class="Estilo1"><? echo $res1['id_os'] ?></td>
              <td width="67" class="Estilo2">Fecha de Generaci&oacute;n </td>
              <td width="56" class="Estilo1"><? echo $res1['ot_fechacreacion'] ?></td>
              <td width="48" class="Estilo1"><span class="Estilo2">Fecha de Entrega </span></td>
              <td width="55" class="Estilo1"><? echo $res1['osde_fecha_entrega'] ?></td>
            </tr>
            <tr>
              <td height="14" colspan="2" class="Estilo2">Tipo Orden de Trabajo </td>
              <td class="Estilo1"><? echo $tipoOT; ?></td>
              <td class="Estilo2">Estado</td>
              <td class="Estilo1"><? echo $res1['nom_estado'] ?></td>
              <td width="67" class="Estilo2">Tienda</td>
              <td colspan="3" class="Estilo1"><? echo $res1['nom_local'] ?></td>
            </tr>
            <tr>
              <td height="24" colspan="2" class="Estilo2">Ordenes de Servicio Cruzadas </td>
              <td class="Estilo1"><? echo $res1['os_cotizaciones_cruzadas'] ?></td>
			  <td class="Estilo2">Vendedor</td>
			  <td colspan="6" class="Estilo1"><? echo $res1['USR_NOMBRES'] . " " . $res1['USR_APELLIDOS']; ?></td>
            </tr>
            <tr>
              <td width="146" height="17" class="Estilo2">Observaciones</td>
              <td colspan="11" class="Estilo1"><? echo $res1['os_comentarios'] ?></td>
            </tr>
          </table>
          </fieldset>
          <fieldset style=" width:620px;">
          <legend class="Estilo2">Datos del Servicio</legend>
          <table width="620" height="auto" border="0" cellpadding="3" cellspacing="2">
            <tr>
              <td width="83" class="Estilo2">Nombre</td>
              <td width="222" class="Estilo1"><? echo $res1['clie_nombre'].' '.$res1['clie_paterno'].' '.$res1['clie_materno'] ?></td>
              <td width="85" class="Estilo2">Tel&eacute;fono</td>
              <td width="138" class="Estilo1"><? echo $osSelDire['dire_telefono'] ?></td>
            </tr>
            <tr>
              <td class="Estilo2">Direcci&oacute;n</td>
              <td class="Estilo1"><? echo $osSelDire['dire_direccion'] ?></td>
              <td class="Estilo2">Barrio</td>
              <td class="Estilo1"><? echo $dirServicio['barrio']." - ".$dirServicio['localidad'] ?></td>
            </tr>
            <tr>
              <td class="Estilo2">Tipo Despacho</td>
              <td class="Estilo1"><? echo $res1['nombre'] ?></td>
              <td class="Estilo2">Ciudad</td>
              <td class="Estilo1"><? echo $dirServicio['ciudad'] ?></td>
            </tr>
            <tr>
              <td class="Estilo2">Indicaciones</td>
              <td><span class="Estilo1"><? echo $osSelDire['dire_observacion'] ?></span></td>
              <td><span class="Estilo2">Departamento</span></td>
              <td><span class="Estilo1"><? echo $dirServicio['departamento'] ?></span></td>
            </tr>
          </table>
          </fieldset>
	  	  </form>
		</div>
		
		<div class="content">
		<table width="620" align="center" style="margin-left: 0px;">
		   <tr>
            <td bgcolor="#CCCCCC"><div style="width: 100px;" align="center" class="Estilo2"><strong>UPC/(SAP)</strong></div></td>
            <td bgcolor="#CCCCCC"><div style="width: 215px;" align="center" class="Estilo2"><strong>Descripci&oacute;n</strong></div></td>
            <td bgcolor="#CCCCCC"><div style="width: 40px;" align="center" class="Estilo2"><strong>Precio Unitario </strong></div></td>
            <td bgcolor="#CCCCCC"><div style="width: 40px;" align="center" class="Estilo2"><strong>Cantidad Solicitada </strong></div></td>
            <td bgcolor="#CCCCCC"><div style="width: 35px;" align="center" class="Estilo2"><strong>Cantidad Pickeada </strong></div></td>
           </tr>
		</table>
		</div>
		<table width="620" border="0">
		   <tr><td colspan="5">
			 
			<? // Query del detalle de la OT
		       $tot_prod_1 = 0;
					$query3 = "SELECT cod_barra, cod_sap, osde_descripcion, osde_precio, osde_cantidad, osde_tipoprod,osde_especificacion,cant_pickeada 
					,if(cant_pickeada='','0',cant_pickeada)cant_pickeada
					,if(osde_tipoprod='PE',osde_cantidad,cant_pickeada)cant_pickeada 
					FROM os_detalle osd
					JOIN tipos_despacho t on t.id_tipodespacho = osd.id_tipodespacho WHERE ot_id =".$cadena[$i];
			
					$rq3 = tep_db_query($query3);
					
					while( $res3 = tep_db_fetch_array( $rq3 ) ) { 
						  echo'<table width="620" align="center" style="margin-left: 0px;">';
						  	echo'<tr>';
							  echo'<td width="100" height="20" class="borde"><div align="left" class="Estilo1">'.$res3['cod_barra'].'/<br>('.$res3['cod_sap'].')</div></td>';
							  echo'<td width="200" class="borde"><div style="width: 200px;" align="left" class="Estilo1">'.substr($res3['osde_descripcion'],0,40).'</div></td>';
							  echo'<td width="50" class="borde"><div style="width: 50px;" align="right" class="Estilo1">'.formato_precio($res3['osde_precio']).'</div></td>';
							  echo'<td width="45" class="borde"><div style="width: 45px;"align="right" class="Estilo1">'.$res3['osde_cantidad'].'</div></td>';
							  echo'<td width="35" class="borde"><div style="width: 35px;"align="right" class="Estilo1"> _______ </div></td>';
							  echo'</tr>';
					      echo'</table>';
					      
					      $can_totProd = $can_totProd + $res3['osde_cantidad']; 
							} 	  
			?>
			</td>
		   </tr>
		   <tr>
		   <td width="77">&nbsp;</td>
		   <td width="190">&nbsp;</td>
		   <td width="130">&nbsp;</td>
		   <td width="120" class="Estilo2">Total:</td>
		   <td width="100" class="Estilo1"><? echo $can_totProd; $can_totProd=0;?></td>
		   </tr>
		   <tr>
		   <td colspan="5">&nbsp;</td>
		   </tr>
		   <tr>
             <td colspan="2"><div align="right" class="Estilo2">Firma Operador Logistico: </div></td>
             <td colspan="3">_________________________</td>
	      </tr>
			<tr>
		   <td colspan="2"><div align="right" class="Estilo2">Revisada por Auxiliar de seguridad: </div></td>
		   <td colspan="3">_________________________</td>
		   </tr>		  
		  </table>
		</div>		
	</div>
</td>
</tr>
</table>
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
</body>
</html>
