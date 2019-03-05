<?
$pag_ini = '../nueva_cotizacion/nueva_cotizacion_00.php';
include "../../includes/aplication_top.php";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// include_idioma_mod( $ses_idioma, "nueva_cotizacion_01" );

// *************************************************************************************

/** Inicio Acciones CGI **/

/** Fin Acciones CGI **/

$MiTemplate = new Template();
// asignamos degug maximo
$MiTemplate->debug = 0;
// root directory de los templates
$MiTemplate->set_root(DIRTEMPLATES);
// variables perdidas
$MiTemplate->set_unknowns("remove");

// Agregamos el main
$MiTemplate->set_file("main","nueva_cotizacion/nueva_cotizacion_0301.htm");

$usr_local = get_local_usr( $ses_usr_id );
if ($tipocod == "UPC") { //cod_barra//UPC//EAN
	if ( is_numeric(trim($sku))) //es número

	   //echo "sku";
	    //query Original
		$query = "SELECT pr.*, if (pr.prod_tipo = 'PE' and pr.prod_subtipo = 'GE', null, p.prec_valor) prec_valor, p.prec_costo, cb.cod_barra, precio_req, valid_stock, precio_mod FROM codbarra cb LEFT JOIN productos pr ON pr.cod_prod1 = cb.cod_prod1 LEFT JOIN precios p ON p.cod_prod1 = pr.cod_prod1 AND id_local = " . ($usr_local+0) . " JOIN tipo_subtipo tst ON tst.prod_tipo = pr.prod_tipo AND tst.prod_subtipo = pr.prod_subtipo WHERE cb.unid_med not in ('PAL','TON','T') 
		     and cb.cod_barra ='" . trim($sku) . "'    and cb.estadoactivo='C' and pr.estadoactivo='C' AND p.estadoactivo = 'C' ORDER BY cod_ppal desc, unid_med";
//cambiado por el query de arriba validar "11-01-2019"
	// $query = "SELECT pr.*, if (pr.prod_tipo = 'PE' and pr.prod_subtipo = 'GE', null, p.prec_valor) prec_valor, p.prec_costo, cb.cod_barra, precio_req, valid_stock, precio_mod, cod_ppal, unid_med FROM productos pr LEFT JOIN codbarra cb ON pr.cod_prod1 = cb.cod_prod1 LEFT JOIN precios p ON p.cod_prod1 = pr.cod_prod1 AND id_local = " . ($usr_local+0) . " JOIN tipo_subtipo tst ON tst.prod_tipo = pr.prod_tipo AND tst.prod_subtipo = pr.prod_subtipo WHERE  cb.unid_med not in ('PAL','TON','T') and cb.estadoactivo='C' and  pr.cod_prod1 ='" . trim($sku) . "' and pr.estadoactivo='C'  AND p.estadoactivo = 'C' ORDER BY cod_ppal desc, unid_med";

		//file_put_contents('queryProducto.txt', $query);
     // $query = "SELECT *FROM productos pr  WHERE pr.cod_prod1 ='1108305'";
      //echo $query; 

	else // es texto
		$query = "SELECT pr.*, if (pr.prod_tipo = 'PE' and pr.prod_subtipo = 'GE', null, p.prec_valor) prec_valor, p.prec_costo, cb.cod_barra, precio_req, valid_stock, precio_mod FROM codbarra cb LEFT JOIN productos pr ON pr.id_producto = cb.id_producto LEFT JOIN precios p ON p.id_producto = pr.id_producto AND id_local = " . ($usr_local+0) . " JOIN tipo_subtipo tst ON tst.prod_tipo = pr.prod_tipo AND tst.prod_subtipo = pr.prod_subtipo WHERE cb.unid_med not in ('PAL','TON','T') and cb.estadoactivo='C' and  cb.cod_barra = '" . trim($sku) . "' and pr.estadoactivo='C' AND p.estadoactivo = 'C' ORDER BY cod_ppal desc, unid_med" ;
}
else { //código SKU//SAP//COD_PROD1
	if ( is_numeric(trim($sku))) //es número
		$query = "SELECT pr.*, if (pr.prod_tipo = 'PE' and pr.prod_subtipo = 'GE', null, p.prec_valor) prec_valor, p.prec_costo, cb.cod_barra, precio_req, valid_stock, precio_mod, cod_ppal, unid_med FROM productos pr LEFT JOIN codbarra cb ON pr.cod_prod1 = cb.cod_prod1 LEFT JOIN precios p ON p.cod_prod1 = pr.cod_prod1 AND id_local = " . ($usr_local+0) . " JOIN tipo_subtipo tst ON tst.prod_tipo = pr.prod_tipo AND tst.prod_subtipo = pr.prod_subtipo WHERE  cb.unid_med not in ('PAL','TON','T') and cb.estadoactivo='C' and  pr.cod_prod1 ='" . trim($sku) . "' and pr.estadoactivo='C'  AND p.estadoactivo = 'C' ORDER BY cod_ppal desc, unid_med";

	else // es texto
		$query = "SELECT pr.*, if (pr.prod_tipo = 'PE' and pr.prod_subtipo = 'GE', null, p.prec_valor) prec_valor, p.prec_costo, cb.cod_barra, precio_req, valid_stock, precio_mod, cod_ppal, unid_med FROM productos pr LEFT JOIN codbarra cb ON pr.cod_prod1 = cb.cod_prod1 LEFT JOIN precios p ON p.cod_prod1 = pr.cod_prod1 AND id_local = " . ($usr_local+0) . " JOIN tipo_subtipo tst ON tst.prod_tipo = pr.prod_tipo AND tst.prod_subtipo = pr.prod_subtipo WHERE pr.cod_prod1 = '" . trim($sku) . "' and cb.estadoactivo='C' and pr.estadoactivo='C' AND p.estadoactivo = 'C' ORDER BY cod_ppal desc, unid_med";
}

$num_reg = query_to_set_var( $query, $MiTemplate, 1, '', '' );
// echo '<pre>';
// 	print_r($query);
// echo '</pre>';
// Retorna solo la primera fila de la consulta
//var_dump($query);

$rq = tep_db_query($query);

file_put_contents('queryProducto.txt', print_r($rq));


$row = tep_db_fetch_array( $rq );
// echo '<pre>';
// 	print_r($row);
// echo '</pre>';

	$MiTemplate->set_var("cod_prod1",$row['cod_prod1']);
	$MiTemplate->set_var("cod_barra",$row['cod_barra']);
	$MiTemplate->set_var("des_corta",$row['des_corta']);
	$MiTemplate->set_var("prec_valor",$row['prec_valor']);
	$MiTemplate->set_var("id_producto",$row['id_producto']);
	$MiTemplate->set_var("prod_tipo",$row['prod_tipo']);
	$MiTemplate->set_var("prod_subtipo",$row['prod_subtipo']);
	$MiTemplate->set_var("prec_costo",$row['prec_costo']);
	$MiTemplate->set_var("precio_req",$row['precio_req']);
	$MiTemplate->set_var("precio_mod",$row['precio_mod']);
	$MiTemplate->set_var("valid_stock",$row['valid_stock']);
	$MiTemplate->set_var("stock_proveedor",$row['stock_proveedor']);

if ($num_reg > 1) {
	$rq = tep_db_query($query);
    while( $res = tep_db_fetch_array( $rq ) ) {
		$str_barras .= sprintf("%013.13s",$res['cod_barra']) . " -" . sprintf("% 3.3s",$res['unid_med']) . ";";
	}
	$MiTemplate->set_var("mult_cod_bar",$str_barras);
}

/*Para sacar las comas de la descripcióe los productos*/
if ( $rq = tep_db_query($query) ){
	while( $res = tep_db_fetch_array( $rq ) ) {
		$des_corta=htmlspecialchars(str_replace( "'", " ",$res['des_corta']));
		$des_larga=htmlspecialchars(str_replace( "'", " ",$res['des_larga']));
	}
}
	$MiTemplate->set_var("des_larga",$des_larga);
	$MiTemplate->set_var("des_corta",$des_larga);


// Agregamos el footer
$MiTemplate->set_file("footer","footer_ident.html");

$MiTemplate->parse("OUT_M", array("main"), true);
$MiTemplate->p("OUT_M");

include "../../includes/application_bottom.php";
?>
