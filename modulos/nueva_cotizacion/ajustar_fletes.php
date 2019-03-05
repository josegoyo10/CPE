<?php
$pag_ini = '../nueva_cotizacion/nueva_cotizacion_00.php';
include ("../../includes/aplication_top.php");

	$id_os = $_REQUEST['id_os'];

	$consul_valor = "SELECT  id_tipodespacho  FROM os_detalle WHERE id_os = $id_os  AND osde_tipoprod IN ('PS', 'PE' )
					 GROUP BY id_tipodespacho ";

	$resul_valor = tep_db_query($consul_valor);

	while($arreglo_valor = tep_db_fetch_array($resul_valor))
	{
		if($arreglo_valor['id_tipodespacho'] == 2)
		$express = $arreglo_valor['id_tipodespacho'];

		if($arreglo_valor['id_tipodespacho'] == 1)
		$programado = $arreglo_valor['id_tipodespacho'];

	}

?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Ajustar Fletes</title>
<script language="javascript" src="../../includes/funciones.js"></script>
<link rel="stylesheet" type="text/css" href="estilos.css">

<script language="JavaScript" type="text/javascript">

	function sumarP(){

		var ValorP = document.getElementById('ValorP');
		var Total = 0;
		ValorP.value = 0;

		if(document.forma.flete100.value  > 0)
		{
		Total = parseFloat(ValorP.value) + parseFloat(document.forma.flete100.value) * 100;
		document.forma.valorP.value = Total;
		}

		if(document.forma.flete1000.value  > 0)
		{
		Total = parseFloat(ValorP.value) + parseFloat(document.forma.flete1000.value) * 1000;
		document.forma.valorP.value = Total;
		}

		if(document.forma.flete10000.value  > 0)
		{
		Total = parseFloat(ValorP.value) + parseFloat(document.forma.flete10000.value) * 10000;
		document.forma.valorP.value = Total;
		}

		if(document.forma.flete100000.value  > 0)
		{
		Total = parseFloat(ValorP.value) + parseFloat(document.forma.flete100000.value) * 100000;
		document.forma.valorP.value = Total;
		}

	}




	function sumarE(){

		var ValorE = document.getElementById('ValorE');
		var Total = 0;
		ValorE.value = 0;

		if(document.forma.fleteE100.value  > 0)
		{
		Total = parseFloat(ValorE.value) + parseFloat(document.forma.fleteE100.value) * 100;
		document.forma.valorE.value = Total;
		}

		if(document.forma.fleteE1000.value  > 0)
		{
		Total = parseFloat(ValorE.value) + parseFloat(document.forma.fleteE1000.value) * 1000;
		document.forma.valorE.value = Total;
		}

		if(document.forma.fleteE10000.value  > 0)
		{
		Total = parseFloat(ValorE.value) + parseFloat(document.forma.fleteE10000.value) * 10000;
		document.forma.valorE.value = Total;
		}

		if(document.forma.fleteE100000.value  > 0)
		{
		Total = parseFloat(ValorE.value) + parseFloat(document.forma.fleteE100000.value) * 100000;
		document.forma.valorE.value = Total;
		}

	}


	function cerrar() {
	window.close();
	}


</script>


</head>

<body onLoad="setTimeout('cerrar()',120*1000)">
<p align="center" class="titulonormal">AJUSTAR FLETES</p>
<form name="forma" method="post" action="ajustar_fletes.php">
 <input name="id_os" type="hidden" id="id_os" value="<?php echo $id_os; ?>">
 <?php
 if($programado == 1)
 {
 ?>

  <p align="center" class="subtitulonormal"><strong>FLETES D. PROGRAMADO</strong></p>
  <table width="537" border="1" align="center" class="tabla1">
    <tr class="trobscuro">
      <td width="81"><strong>C&oacute;digo SAP </strong></td>
      <td width="239"><strong>Descripci&oacute;n</strong></td>
      <td width="75"><strong>Valor</strong></td>
      <td width="114"><strong>Cantidad</strong></td>
    </tr>
    <tr>
      <td>539</td>
      <td>FLETES ADICIONAL X 100 </td>
      <td>100</td>
      <td><div align="right">
        <input name="flete100" type="text" id="flete100" size="10" maxlength="3" >
      </div></td>
    </tr>
    <tr>
      <td>540</td>
      <td>FLETES ADICIONAL X 1000</td>
      <td>1000</td>
      <td><div align="right">
        <input name="flete1000" type="text" id="flete1000" size="10" maxlength="3" >
      </div></td>
    </tr>
    <tr>
      <td>541</td>
      <td>FLETES ADICIONAL X 10000</td>
      <td>10000</td>
      <td><div align="right">
        <input name="flete10000" type="text" id="flete10000" size="10" maxlength="3" >
      </div></td>
    </tr>
    <tr>
      <td>542</td>
      <td>FLETES ADICIONAL X 100000</td>
      <td>100000</td>
      <td><div align="right">
        <input name="flete100000" type="text" id="flete100000" size="10" maxlength="3" >
      </div></td>
    </tr>
  </table>
  <table width="539" height="28" border="1" align="center" class="tabla1">
    <tr class="trobscuro">

	  <td width="186">
        <input type="button" name="boton" value="Calcular" onClick="sumarP();" >
	  </td>

	  <td width="217"><div align="right"><strong>
        VALOR TOTAL FLETES D. PROGRAMADO</strong></div></td>

      <td width="114">
        <div align="right">
          <input name="valorP" type="text" id="valorP" value="0" size="15" readonly>
        </div></td></tr>
  </table>

   <?php
	}

 	if($express == 2)
 	{
 	?>


  <p align="center" class="subtitulonormal"><strong>FLETES EXPRESS</strong></p>
  <table width="537" border="1" align="center" class="tabla1">
    <tr class="trobscuro">
      <td width="81"><strong>C&oacute;digo SAP </strong></td>
      <td width="239"><strong>Descripci&oacute;n</strong></td>
      <td width="77"><strong>Valor</strong></td>
      <td width="112"><strong>Cantidad</strong></td>
    </tr>
    <tr>
      <td>539</td>
      <td>FLETES ADICIONAL X 100 </td>
      <td>100</td>
      <td><div align="right">
        <input name="fleteE100" type="text" id="fleteE100" size="10" maxlength="3">
      </div></td>
    </tr>
    <tr>
      <td>540</td>
      <td>FLETES ADICIONAL X 1000</td>
      <td>1000</td>
      <td><div align="right">
        <input name="fleteE1000" type="text" id="fleteE1000" size="10" maxlength="3">
      </div></td>
    </tr>
    <tr>
      <td>541</td>
      <td>FLETES ADICIONAL X 10000</td>
      <td>10000</td>
      <td><div align="right">
        <input name="fleteE10000" type="text" id="fleteE10000" size="10" maxlength="3">
      </div></td>
    </tr>
    <tr>
      <td>542</td>
      <td>FLETES ADICIONAL X 100000</td>
      <td>100000</td>
      <td><div align="right">
        <input name="fleteE100000" type="text" id="fleteE100000" size="10" maxlength="3">
      </div></td>
    </tr>
  </table>
  <table width="539" height="28" border="1" align="center" class="tabla1">
    <tr class="trobscuro">

	<td width="49">
	<input type="button" name="botonE" value="Calcular" onClick="sumarE();" >
	</td>

      <td width="356"><div align="right"><strong>
        VALOR TOTAL FLETES EXPRESS </strong></div></td>
      <td width="112"><div align="right">
        <input name="valorE" type="text" id="valorE" value="0" size="15" readonly>
      </div></td>
    </tr>
  </table>

  <?php
  }
  ?>
  <p>&nbsp;</p>
  <p align="center">

  	<?php
  	if($programado == 1 ||  $express == 2)
 	{
  	?>
    <input name="Submit" type="submit" class="userinput" value="Insertar">
	<?php
  	}
    ?>
</p>
</form>
<p>
  <?php


	//================ CALCULO FLETES D. PROGRAMADO ===============================================

	if($_REQUEST['flete100'] > 0)
	{
	$codSAP = 539;
	$cantidad100 = $_REQUEST['flete100'];
	$consul_flete = "SELECT DISTINCT C.cod_barra, P.* FROM productos  P
							LEFT JOIN codbarra C ON P.cod_prod1 = C.cod_prod1
							WHERE P.prod_tipo = 'SV' AND P.prod_subtipo = 'DE' 
							AND C.estadoactivo = 'C' AND  P.estadoactivo = 'C' 
							AND P.cod_prod1 = " . $codSAP;
	$resul_flete = tep_db_query($consul_flete);
	$arreglo_flete = tep_db_fetch_array($resul_flete);

	$consulta = "INSERT INTO  os_detalle( id_origen, id_tipodespacho,  id_os,                   osde_tipoprod,                     osde_subtipoprod,                 osde_instalacion,   osde_precio,  osde_cantidad, osde_descuento,      cod_sap,                osde_descripcion,                       id_producto,                             cod_barra,                   ind_dec )
				                      VALUES( 4,           1,          $id_os,      '" . $arreglo_flete['prod_tipo'] . "',  '" . $arreglo_flete['prod_subtipo'] . "',          0,            100,       $cantidad100,        0,           '$codSAP',     '" . $arreglo_flete['des_corta'] . "',   " . $arreglo_flete['id_producto'] . ", '" . $arreglo_flete['cod_barra'] . "',        0 )";
	$resultado = tep_db_query($consulta);

	}


	if($_REQUEST['flete1000'] > 0)
	{
	$codSAP = 540;
	$cantidad1000 = $_REQUEST['flete1000'];

	$consul_flete = "SELECT DISTINCT C.cod_barra, P.* FROM productos  P
							LEFT JOIN codbarra C ON P.cod_prod1 = C.cod_prod1
							WHERE P.prod_tipo = 'SV' AND P.prod_subtipo = 'DE' 
							AND C.estadoactivo = 'C' AND  P.estadoactivo = 'C' 
							AND P.cod_prod1 = " . $codSAP;
	$resul_flete = tep_db_query($consul_flete);
	$arreglo_flete = tep_db_fetch_array($resul_flete);

	$consulta = "INSERT INTO  os_detalle( id_origen, id_tipodespacho,  id_os,                   osde_tipoprod,                     osde_subtipoprod,                 osde_instalacion,   osde_precio,  osde_cantidad, osde_descuento,      cod_sap,                osde_descripcion,                       id_producto,                                cod_barra,                      ind_dec )
				                      VALUES( 4,           1,          $id_os,      '" . $arreglo_flete['prod_tipo'] . "',  '" . $arreglo_flete['prod_subtipo'] . "',          0,            1000,       $cantidad1000,        0,           '$codSAP',     '" . $arreglo_flete['des_corta'] . "',   " . $arreglo_flete['id_producto'] . ", '" . $arreglo_flete['cod_barra'] . "',          0   )";
	$resultado = tep_db_query($consulta);

	}



	if($_REQUEST['flete10000'] > 0)
	{
	$codSAP = 541;
	$cantidad10000 = $_REQUEST['flete10000'];

	$consul_flete = "SELECT DISTINCT C.cod_barra, P.* FROM productos  P
							LEFT JOIN codbarra C ON P.cod_prod1 = C.cod_prod1
							WHERE P.prod_tipo = 'SV' AND P.prod_subtipo = 'DE' 
							AND C.estadoactivo = 'C' AND  P.estadoactivo = 'C' 
							AND P.cod_prod1 =  " . $codSAP;
	$resul_flete = tep_db_query($consul_flete);
	$arreglo_flete = tep_db_fetch_array($resul_flete);

	$consulta = "INSERT INTO  os_detalle( id_origen, id_tipodespacho,  id_os,                   osde_tipoprod,                     osde_subtipoprod,                 osde_instalacion,   osde_precio,  osde_cantidad, osde_descuento,      cod_sap,                osde_descripcion,                       id_producto,                                 cod_barra,                     ind_dec )
				                      VALUES( 4,           1,          $id_os,      '" . $arreglo_flete['prod_tipo'] . "',  '" . $arreglo_flete['prod_subtipo'] . "',          0,            10000,       $cantidad10000,        0,           '$codSAP',     '" . $arreglo_flete['des_corta'] . "',   " . $arreglo_flete['id_producto'] . ", '" . $arreglo_flete['cod_barra'] . "',        0   )";
	$resultado = tep_db_query($consulta);

	}


	if($_REQUEST['flete100000'] > 0)
	{
	$codSAP = 542;
	$cantidad100000 = $_REQUEST['flete100000'];

	$consul_flete = "SELECT DISTINCT C.cod_barra, P.* FROM productos  P
							LEFT JOIN codbarra C ON P.cod_prod1 = C.cod_prod1
							WHERE P.prod_tipo = 'SV' AND P.prod_subtipo = 'DE' 							
							AND C.estadoactivo = 'C' AND  P.estadoactivo = 'C' 
							AND P.cod_prod1 = " . $codSAP;
	$resul_flete = tep_db_query($consul_flete);
	$arreglo_flete = tep_db_fetch_array($resul_flete);

	$consulta = "INSERT INTO  os_detalle( id_origen, id_tipodespacho,  id_os,                   osde_tipoprod,                     osde_subtipoprod,                 osde_instalacion,   osde_precio,  osde_cantidad, osde_descuento,      cod_sap,                osde_descripcion,                       id_producto,                                       cod_barra,              ind_dec )
				                      VALUES( 4,           1,          $id_os,      '" . $arreglo_flete['prod_tipo'] . "',  '" . $arreglo_flete['prod_subtipo'] . "',          0,          100000,     $cantidad100000,      0,            '$codSAP',     '" . $arreglo_flete['des_corta'] . "',   " . $arreglo_flete['id_producto'] . ", '" . $arreglo_flete['cod_barra'] . "',          0   )";
	$resultado = tep_db_query($consulta);

	}



	//================ CALCULO FLETES EXPRESS ========================================

	if($_REQUEST['fleteE100'] > 0)
	{
	$codSAP = 539;
	$cantidad100 = $_REQUEST['fleteE100'];

	$consul_flete = "SELECT DISTINCT C.cod_barra, P.* FROM productos  P
							LEFT JOIN codbarra C ON P.cod_prod1 = C.cod_prod1
							WHERE P.prod_tipo = 'SV' AND P.prod_subtipo = 'DE' 
							AND C.estadoactivo = 'C' AND  P.estadoactivo = 'C' 
							AND P.cod_prod1 = " . $codSAP;
	$resul_flete = tep_db_query($consul_flete);
	$arreglo_flete = tep_db_fetch_array($resul_flete);

	$consulta = "INSERT INTO  os_detalle( id_origen, id_tipodespacho,  id_os,                   osde_tipoprod,                     osde_subtipoprod,                 osde_instalacion,   osde_precio,  osde_cantidad, osde_descuento,      cod_sap,                osde_descripcion,                       id_producto,                               cod_barra,                   ind_dec )
				                      VALUES( 4,           2,          $id_os,      '" . $arreglo_flete['prod_tipo'] . "',  '" . $arreglo_flete['prod_subtipo'] . "',          0,            100,       $cantidad100,        0,           '$codSAP',     '" . $arreglo_flete['des_corta'] . "',   " . $arreglo_flete['id_producto'] . ", '" . $arreglo_flete['cod_barra'] . "',        0   )";
	$resultado = tep_db_query($consulta);	

	}


	if($_REQUEST['fleteE1000'] > 0)
	{
	$codSAP = 540;
	$cantidad1000 = $_REQUEST['fleteE1000'];

	$consul_flete = "SELECT DISTINCT C.cod_barra, P.* FROM productos  P
							LEFT JOIN codbarra C ON P.cod_prod1 = C.cod_prod1
							WHERE P.prod_tipo = 'SV' AND P.prod_subtipo = 'DE' 
							AND C.estadoactivo = 'C' AND  P.estadoactivo = 'C' 
							AND P.cod_prod1 = " . $codSAP;
	$resul_flete = tep_db_query($consul_flete);
	$arreglo_flete = tep_db_fetch_array($resul_flete);

	$consulta = "INSERT INTO  os_detalle( id_origen, id_tipodespacho,  id_os,                   osde_tipoprod,                     osde_subtipoprod,                 osde_instalacion,   osde_precio,  osde_cantidad, osde_descuento,      cod_sap,                osde_descripcion,                       id_producto,                                   cod_barra,                   ind_dec )
				                      VALUES( 4,           2,          $id_os,      '" . $arreglo_flete['prod_tipo'] . "',  '" . $arreglo_flete['prod_subtipo'] . "',          0,            1000,       $cantidad1000,        0,           '$codSAP',     '" . $arreglo_flete['des_corta'] . "',   " . $arreglo_flete['id_producto'] . ", '" . $arreglo_flete['cod_barra'] . "',          0   )";
	$resultado = tep_db_query($consulta);

	}



	if($_REQUEST['fleteE10000'] > 0)
	{
	$codSAP = 541;
	$cantidad10000 = $_REQUEST['fleteE10000'];

	$consul_flete = "SELECT DISTINCT C.cod_barra, P.* FROM productos  P
							LEFT JOIN codbarra C ON P.cod_prod1 = C.cod_prod1
							WHERE P.prod_tipo = 'SV' AND P.prod_subtipo = 'DE' 
							AND C.estadoactivo = 'C' AND  P.estadoactivo = 'C' 
							AND P.cod_prod1 = " . $codSAP;
	$resul_flete = tep_db_query($consul_flete);
	$arreglo_flete = tep_db_fetch_array($resul_flete);

	$consulta = "INSERT INTO  os_detalle( id_origen, id_tipodespacho,  id_os,                   osde_tipoprod,                     osde_subtipoprod,                 osde_instalacion,   osde_precio,  osde_cantidad, osde_descuento,      cod_sap,                osde_descripcion,                       id_producto,                                        cod_barra,                ind_dec )
				                      VALUES( 4,           2,          $id_os,      '" . $arreglo_flete['prod_tipo'] . "',  '" . $arreglo_flete['prod_subtipo'] . "',          0,            10000,       $cantidad10000,        0,           '$codSAP',     '" . $arreglo_flete['des_corta'] . "',   " . $arreglo_flete['id_producto'] . ", '" . $arreglo_flete['cod_barra'] . "',          0   )";
	$resultado = tep_db_query($consulta);

	}


	if($_REQUEST['fleteE100000'] > 0)
	{
	$codSAP = 542;
	$cantidad100000 = $_REQUEST['fleteE100000'];

	$consul_flete = "SELECT DISTINCT C.cod_barra, P.* FROM productos  P
							LEFT JOIN codbarra C ON P.cod_prod1 = C.cod_prod1
							WHERE P.prod_tipo = 'SV' AND P.prod_subtipo = 'DE' 
							AND C.estadoactivo = 'C' AND  P.estadoactivo = 'C' 
							AND P.cod_prod1 = " . $codSAP;
	$resul_flete = tep_db_query($consul_flete);
	$arreglo_flete = tep_db_fetch_array($resul_flete);

	$consulta = "INSERT INTO  os_detalle( id_origen, id_tipodespacho,  id_os,                   osde_tipoprod,                     osde_subtipoprod,                 osde_instalacion,   osde_precio,  osde_cantidad, osde_descuento,      cod_sap,                osde_descripcion,                       id_producto,                              cod_barra,                      ind_dec )
				                      VALUES( 4,           2,          $id_os,      '" . $arreglo_flete['prod_tipo'] . "',  '" . $arreglo_flete['prod_subtipo'] . "',          0,          100000,    $cantidad100000,      0,           '$codSAP',     '" . $arreglo_flete['des_corta'] . "',   " . $arreglo_flete['id_producto'] . ", '" . $arreglo_flete['cod_barra'] . "',           0   )";
	$resultado = tep_db_query($consulta);

	}


	if($_REQUEST['flete100'] > 0  || $_REQUEST['flete1000'] > 0 || $_REQUEST['flete10000'] > 0 || $_REQUEST['flete100000'] > 0 || $_REQUEST['fleteE100'] > 0  || $_REQUEST['fleteE1000'] > 0 || $_REQUEST['fleteE10000'] > 0 || $_REQUEST['fleteE100000'] > 0)
	{
	?>
	<script language="JavaScript" type="text/javascript">
		opener.document.location.reload();
		window.close();
	</script>
  	<?php
	}



?>
</p>


	<?php
  	if($programado == 1)
 	{
  	?>
	<script LANGUAGE="javascript">
		window.document.forma.flete100.onkeypress = NumberIsKey;
		window.document.forma.flete1000.onkeypress = NumberIsKey;
		window.document.forma.flete10000.onkeypress = NumberIsKey;
		window.document.forma.flete100000.onkeypress = NumberIsKey;
	</script>

	<?php
  	}

	if($express == 2)
 	{
  	?>
	<script LANGUAGE="javascript">
		window.document.forma.fleteE100.onkeypress = NumberIsKey;
		window.document.forma.fleteE1000.onkeypress = NumberIsKey;
		window.document.forma.fleteE10000.onkeypress = NumberIsKey;
		window.document.forma.fleteE100000.onkeypress = NumberIsKey;
	</script>

	<?php
  	}
  	?>

</body>
</html>