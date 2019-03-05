<?php
$pag_ini = '../nueva_cotizacion/consulta_inventario.php?menu=1';
include ("../../includes/aplication_top.php");
require_once('../../wsRealInventory/RealInventory.php');

// *************************************************************************************

$MiTemplate = new Template();
// asignamos degug maximo
$MiTemplate->debug = 0;
// root directory de los templates
$MiTemplate->set_root(DIRTEMPLATES);
// variables perdidas
$MiTemplate->set_unknowns("remove");


$MiTemplate->set_file("main","nueva_cotizacion/consulta_inventario.htm");

if($_REQUEST['menu']==0)
$MiTemplate->set_var("menu", "<input type='hidden' name='menu' value='0'>");

if($_REQUEST['menu']==1)
$MiTemplate->set_var("menu", "<input type='hidden' name='menu' value='1'>");


	// Inicio la recuperación de los Tipos de Despacho
	$MiTemplate->set_block("main", "tiendas", "BLO_tienda");
	if($tipo_entrega == '')			
	$query = "SELECT  cod_local,  nom_local  FROM locales ORDER BY cod_local ";
	else
	$query = "SELECT  cod_local,  nom_local,  case WHEN cod_local = '$tienda'   then 'selected' else '' end selected  FROM  locales  ORDER BY cod_local ";
					
	query_to_set_var($query, $MiTemplate, 1, 'BLO_tienda', 'tiendas');
	// Fin de la  recuperación

if($accion == 'Buscar')
{
        //para efc2542; <-> si no se quiere usar SAP, poner como true;
        $nosap = TRUE;
        // // // // // // // // // // // // // // // // // // // // //
        
	if($cod_sap == 'sap'){
	$codigo_sap = $codigo;
	$codigo_ean = '';
	}
	
	if($cod_ean == 'ean'){
	$codigo_ean = $codigo;
	$codigo_sap = 0;
	}
	
	if(!$nosap){
		$xml = "<request>
				<inventory>";
		
		if($codigo_ean != '')
		$xml = $xml . " <ean>$codigo_ean</ean>";
		
		if($codigo_sap != '')
		$xml = $xml . "<sap>$codigo_sap</sap>";
				  
		if($tienda != '')
		$xml = $xml . "<store>$tienda</store>";		      
				
		$xml = $xml . "</inventory>
					</request>";
		
		$respuestaInventario = RealInventory::searchInventoryById($xml);
	}
	
	$MiTemplate->set_block("main", "consulta_inv", "BLO_consulta");
	
	$whereTienda = "";
	if($tienda != "")
	$whereTienda = " AND L.cod_local = '$tienda' ";
	
	
	if($codigo_sap != "" && $codigo_ean == "")
	{
	$db_query = "SELECT  P.cod_prod1 AS cod_sap,  P.des_larga AS producto, PR.prec_valor AS precio , nom_local AS tienda, L.cod_local, C.cod_barra AS ean".( ($nosap)? ", PR.stock as inventario" : "")."  
				 FROM  productos   P
				 INNER JOIN precios PR ON  PR.cod_prod1 = P.cod_prod1
				 INNER JOIN   locales L ON  L.cod_local  =  PR.cod_local
				 INNER JOIN  codbarra C  ON  C.cod_prod1 = P.cod_prod1 
				 WHERE  PR.estadoactivo = 'C' AND P.estadoactivo = 'C' AND C.estadoactivo='C' AND  P.cod_prod1 = $codigo_sap $whereTienda ";
	}
	
	if($codigo_ean != "" && $codigo_sap == "")
	{
	$db_query = "SELECT  P.cod_prod1 AS cod_sap,  P.des_larga AS producto, PR.prec_valor AS precio , nom_local AS tienda, L.cod_local, C.cod_barra AS ean".( ($nosap)? ", PR.stock as inventario" : "")."
			     FROM  productos   P
				 INNER JOIN precios PR ON  PR.cod_prod1 = P.cod_prod1
				 INNER JOIN   locales L ON  L.cod_local  =  PR.cod_local
				 INNER JOIN  codbarra C  ON  C.cod_prod1 = P.cod_prod1 
				 WHERE PR.estadoactivo = 'C' AND P.estadoactivo = 'C' AND C.estadoactivo='C' AND  C.cod_barra  = '$codigo_ean' $whereTienda ";
	}	
	
 	
        if($nosap){
            query_to_set_var_inventory($db_query, $MiTemplate, 1, 'BLO_consulta', 'consulta_inv', $respuestaInventario, $nosap);
        }elseif($respuestaInventario['state'] == 1)
	{
	query_to_set_var_inventory($db_query, $MiTemplate, 1, 'BLO_consulta', 'consulta_inv', $respuestaInventario);
	}
	else{
		$MiTemplate->set_var("mensaje", "EL PRODUCTO NO SE ENCONTRÓ");		
	}
	
}	

$MiTemplate->set_var("PAGETITLE", 'CENTRO DE PROYECTOS - Inventario real');

$MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));

$MiTemplate->set_var("TEXT_CAMPO_0",TEXT_CAMPO_0);
$MiTemplate->set_var("TEXT_CAMPO_1",TEXT_CAMPO_1);
$MiTemplate->set_var("TEXT_CAMPO_3",TEXT_CAMPO_3);
$MiTemplate->set_var("TEXT_FLECHA_SIG",TEXT_FLECHA_SIG);

// Agregamos el header
$MiTemplate->set_file("header","header_ident.html");

// Agregamos el main
$MiTemplate->set_file("main","nueva_cotizacion/consulta_inventario.htm");


// Agregamos el footer
include "../../includes/footer_cproy.php";

$MiTemplate->pparse("OUT_H", array("header"), true);

//echo "Menu: ",  $_GET['menu'], "<br>";

if($_REQUEST['menu'] == 1)
include "../../menu/menu.php";

$MiTemplate->parse("OUT_M", array("main","footer"), true);

$MiTemplate->p("OUT_M");

include "../../includes/application_bottom.php";



?>
