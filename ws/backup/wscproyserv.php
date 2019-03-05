<?
/********************************
WEBSERVICES
Proyecto : Centro de poryectos
Cliente : EASY
Autor : Gonzalo Melo Bahamondes
BBR-i ecommerce &  retail
*********************************/
include_once("lib/database.php");
include_once('lib/db_config.php');


/////////////////////////////////////////////////////////////////////
//
// Module    : SimpleXMLParser.php
// DateTime  : 26/07/2005 11:32
// Author    : Phillip J. Whillier
// Purpose  : Very lightwieght "simple" XML parser does not support attributes.
//
/////////////////////////////////////////////////////////////////////

class SimpleXMLParser {
  
   // Find the max number of specific nodes in the XML
   function MaxElements($XMLSource, $XMLName) {
           $MaxElements = 0;
           $XMLTag = "<" . $XMLName . ">";
           $Y = $this->instr($XMLSource, $XMLTag);
           while($Y>=0) {
                   $MaxElements = $MaxElements + 1;
                   $Y = $this->instr($XMLSource, $XMLTag, $Y + strlen($XMLTag));
           }
       return $MaxElements;
   }
  
   // Parse xml to retrieve a specific element
   // Instance number is a zero based index.
   function Parse($XMLSource, $XMLName, $aInstance = 0, $Default = "") {
           $XMLLength = strlen($XMLSource);
           $XMLTag = "<" . $XMLName . ">";
           $XMLTagEnd = "</" . $XMLName . ">";
           $Instance = $aInstance + 1;
      
       /* Find the start of the requested instance... */
           $XMLStart = 0;
  
           for($x = 1; $x < $Instance + 1; $x++) {
                   $Y = $this->instr($XMLSource, $XMLTag, $XMLStart);
  
                   if ($Y >= $XMLStart) {
                           $XMLStart = $Y + strlen($XMLTag);
                   }
                   else {
                           return $Default;
                   }
           }
      
       /* Find the end of the instance... */
           $XMLEnd = $XMLStart;
           $XMLMatch = 1;
  
           while($XMLMatch) {
                   $c = substr($XMLSource, $XMLEnd, strlen($XMLTagEnd));
                   if($c == $XMLTagEnd) {
                           $XMLMatch = $XMLMatch - 1;
                   }
                   else {
                       if (substr(c, 0, 1) == $XMLTag) {
                           $XMLMatch = $XMLMatch + 1;
                       }
                   }
                   $XMLEnd = $XMLEnd + 1;
                   if ($XMLEnd == $XMLLength) {
                           return $Dufault;
                   }
           }
           return substr($XMLSource, $XMLStart, $XMLEnd - $XMLStart - 1);
   }
  
   // Helper function for finding substrings
   function instr($haystack, $needle, $pos = 0) {
       $thispos = strpos($haystack, $needle, $pos);
       if ($thispos===false)
           $thispos = -1;
       return $thispos;
   }
}


/*********************************************************
FUNCIONES
*********************************************************/
function func_error($cod, $error_msg,$header){

$XMLerror =  '<main><header>'.chr(13).$header.chr(13).'</header><data>'.chr(13).'<error_msg cod_error="'.$cod.'">'.$error_msg.'</error_msg>'.chr(13).'</data>'.chr(13).'</main>';
wlog("ERROR($cod) : $error_msg");

return $XMLerror;
}

/*********************************************************
FUNCIONES : busca_id_local
*********************************************************/
function busca_id_local($cod_local){
	$query = "SELECT id_local FROM locales WHERE cod_local='$cod_local'";
	$db_1 = tep_db_query($query);
	if ($res = tep_db_fetch_array( $db_1 ))	$id_local = $res['id_local'];

	wlog("busca_id_local : local encontradoa para el cod_local[$cod_local]= [$id_local]");
	if ($id_local!="") 
		return $id_local;
	else 
		return false;
	}

/*********************************************************
FUNCIONES : inserta_os
*********************************************************/
function inserta_os($id_local, $usuario){
	/*
	INSERT INTO os 

            (id_os, id_estado, id_proyecto, id_local, id_direccion, clie_rut, os_fechacreacion, os_fechacotizacion, 
            os_fechaestimada, os_comentarios, os_numboleta, os_fechaboleta, os_descripcion, usuario, USR_ID, os_fechaestimacion, origen) 
			VALUES (
            null,                             //Para que genere el id Auto increment
            'SE',                             //OS en estado cotización
            1,                                 //Proyecto dummy
            $id_local,                   //Parámetro
            1,                                 //Dirección dummy
            19,                                 /*Cliente dummy
            now(),     //Parámetro <os_fechacreacion>
            now(),   //Parámetro <os_fechacotizacion>
            now(),    //Parámetro<os_fechaestimada>
            '',       //Parámetros <os_comentarios>
            null, 
            null, 
            '',         //Parámetro <os_descripcion>
            $operator,                    //Parámetro <usuario>
            0, 
            '',  //Parámetro <os_fechaestimacion>
            'P'                                //Originada desde Patio
            )
	*/

	$query = "INSERT INTO os (id_os, id_estado, id_proyecto, id_local, id_direccion, clie_rut, os_fechacreacion, os_fechacotizacion, 
            os_fechaestimada, os_comentarios, os_numboleta, os_fechaboleta, os_descripcion, usuario, USR_ID, os_fechaestimacion, origen) 
			VALUES ( null,'".ESTADO_COTIZACION_DUMMY."',1,$id_local,1,".ID_CLIENTE_DUMMY.",now(),now(),now(),'',null,null,'".COMMENT_COTZ."','$usuario',0,'',
				'".ESTADO_ORIGEN_PATIO."')";

	if(!tep_db_query($query)){
			wlog ("inserta_os : Error al realizar la operación : ".$query);
			return false;
		}
	$id_os = tep_db_insert_id(1);
	if ($id_os=="") {
		wlog ("inserta_os : No puede rescatar ID de la cotizacion : ".$query);
		return false;
		}

	return $id_os;
	}
/*********************************************************
FUNCIONES : inserta_os_detalle
*********************************************************/
function inserta_os_detalle($id_os,$price,$qty,$cod_prod1,$id_producto, $des_corta,$barcode){
	/*
	INSERT INTO os_detalle (id_os_detalle,ot_id,id_origen,id_tipodespacho,id_os,osde_tipoprod,osde_subtipoprod,osde_instalacion,osde_precio,osde_cantidad,osde_descuento,osde_preciocosto,cod_sap,osde_especificacion,osde_descripcion,id_producto,cod_barra,usrnomaut,usrpassaut,observacion) VALUES(
            null,  //Para que genere el id Auto increment
            null,                             
            5,               //Sin descuentos
            1,               //Normal
            $id_os,          //Parámetro: Rescatado desde el insert anterior
            'TIPO_PRODUCTO_STOCK',  //Tipo Producto Stock
            'TIPO_PRODUCTO_STOCK',   //SubTipo Producto Stock
            0,                       //Parámetro osde_instalacion
            <osde_precio>,           //Parámetro osde_precio
            <osde_cantidad>,         //Parámetro osde_cantidad
            0,                       //Parámetro osde_descuento
            0,                       //Parámetro osde_preciocosto
            $cod_prod1,              //Parámetro cod_sap
            '',                      //Parámetro osde_especificacion
            $des_corta,     //Parámetro osde_descripcion
            $id_producto,              //Parámetro id_producto
            $cod_barra,                //Parámetro cod_barra
            null,               //Parámetro usrnomaut
            null,              //Parámetro usrpassaut
            null          //Parámetro <observacion>
            )
	*/

	$query = "INSERT INTO os_detalle (id_os_detalle,ot_id,id_origen,id_tipodespacho,id_os,osde_tipoprod,osde_subtipoprod,osde_instalacion,osde_precio,osde_cantidad,osde_descuento,osde_preciocosto,cod_sap,osde_especificacion,osde_descripcion,id_producto,cod_barra,usrnomaut,usrpassaut,observacion) VALUES(
            null,null,5,1,$id_os,'".TIPO_PRODUCTO_STOCK."','".TIPO_PRODUCTO_STOCK."',0,$price,$qty,0,0,'$cod_prod1','','$des_corta',$id_producto,'$barcode',null,null,null)";

	if(!tep_db_query($query)){
			wlog ("inserta_os_detalle : Error al realizar la operación : ".$query);
			return false;
		}
	$id_os_detalle = tep_db_insert_id(1);
	if ($id_os_detalle=="") {
		wlog ("inserta_os_detalle : No puede rescatar ID del detalle de la cotizacion : ".$query);
		return false;
		}

	return $id_os_detalle;
	}
/*********************************************************
FUNCIONES : obtiene_producto
*********************************************************/
//devuelve el codigo de barras o error si no esta bien parseado
function obtiene_producto($codigo_barra, $local) {

	$query = "  SELECT cb.cod_barra cod_barra ,
						p.cod_prod1 cod_prod1,
						p.des_corta des_corta,
						p.des_larga des_larga,
						pr.cod_local cod_local,
						pr.prec_valor pvalor
				FROM codbarra cb 
				JOIN precios pr ON (pr.id_producto = cb.id_producto AND pr.cod_local ='$local')
				JOIN productos p ON (cb.id_producto = p.id_producto)
				WHERE cb.cod_barra='$codigo_barra' AND  cb.estadoactivo='C'";
	wlog("obtiene_producto : MYSQL qry: $query");

	//<product cod_prod1="PR10229449" fpesable="yes"><barcode>780118899933</barcode><desc1>Grecian 2000</desc1><desc2>Grecian 2000 for men</desc2>
	//<price>2500</price><unit>u</unit><stock>2500</stock></product>
	//unit y stock suprimidos

    $db_1 = tep_db_query($query);
	

	//Verificar error mysql
	wlog("obtiene_producto : Numero de filas".tep_db_num_rows($db_1));

    if ( $res = tep_db_fetch_array( $db_1 ) ) {

		wlog("cod_barra:$codigo_barra");
		wlog("cod_prod1:".$res['cod_prod1']);
		wlog("des_corta:".$res['des_corta']);
		wlog("des_larga:".$res['des_larga']);
		wlog("cod_local:".$res['cod_local']);
		wlog("prec_valor:".$res['pvalor']);

		$XMLprod = '<product>'.chr(13);
		$XMLprod .= "<barcode>$codigo_barra</barcode>".chr(13);
		$XMLprod .= "<codsap>".$res['cod_prod1']."</codsap>".chr(13);
		$XMLprod .= "<desc1>".$res['des_corta']."</desc1>".chr(13);
		$XMLprod .= "<desc2>".$res['des_larga']."</desc2>".chr(13);
		$XMLprod .= "<price>".$res['pvalor']."</price>".chr(13);
		$XMLprod .= '<unit>unidad</unit>'.chr(13).'<stock></stock>'.chr(13).'</product>'.chr(13);

		wlog("obtiene_producto : XML del producto : $XMLprod");
		return $XMLprod;
		}
	wlog("obtiene_producto : Termina sin producto");
	
	return false;

/*
	$XMLprod = "<product cod_prod1='PR10229449' fpesable='no'><barcode>780118899933</barcode><desc1>Grecian 2000</desc1><desc2>Greciañ 2000 for men</desc2><price>2500</price><unit>u</unit><stock>2500</stock></product>";
	return $XMLprod;
*/
	}




/*********************************************************
Crea la instancia nueva para el server
*********************************************************/

// nos conectamos a la base de datos
tep_db_connect();
wlog("Conectado ...$rc");
// invoca libreria nusoap
require_once('lib/nusoap.php');
wlog("carga libreria NUSOAP ...");


// Crea la instancia nueva para el server
$server = new soap_server;

wlog("crea instancia soap_server ...");

$server->configureWSDL('wscproyserv', 'urn:wscproyserv');

wlog("configura instancia para WDSL ...");

/*
void addSimpleType (string $name, 
[string $restrictionBase = ''], 
[string $typeClass = 'simpleType'], 
[string $phpType = 'scalar'], 
[array $enumeration = array()])
*/

// Register the method to expose
$server->register('detalle_producto',                    // method name
    array('cons' => 'xsd:string'),          // input parameters
    array('return' => 'xsd:string'),    // output parameters
    'urn:wscproyserv',                         // namespace
    'urn:wscproyserv#detalle_producto',                   // soapaction
    'rpc',                                    // style
    'encoded',                                // use
    'Vea el manual'        // documentation
);

wlog("registra funcion DETALLE_PRODUCTO para instancia WDSL ...");


// Register the method to expose
$server->register('nueva_cotizacion',                    // method name
    array('cons' => 'xsd:string'),          // input parameters
    array('return' => 'xsd:string'),    // output parameters
    'urn:wscproyserv',                         // namespace
    'urn:wscproyserv#nueva_cotizacion',                   // soapaction
    'rpc',                                    // style
    'encoded',                                // use
    'Vea el manual'        // documentation
);

wlog("registra funcion NUEVA_COTIZACION para instancia WDSL ...");

/*********************************************************
METODOS REGISTRADOS como WEBSERVICEs
*********************************************************/


/*********************************************************
Metodo detalle_producto
consulta: envia un codigo de barra en XML
respuesta: Devuelve el detalle del producto en XML
*********************************************************/
function detalle_producto($cons) {

	wlog("detalle_producto:cons:$cons");
	//libxml_use_internal_errors(true);
	$xmlObj = new SimpleXMLParser;
	wlog("genera objeto:xmlObj:$cons");
	
	if (($xml = $xmlObj->Parse($cons, "main"))==""){return func_error("P00","tag MAIN vacio","");	}	
	wlog("parsea main : $xml");

	//HEADER
	if (($xmlheader = $xmlObj->Parse($xml, "header"))==""){return func_error("PH0","tag HEADER vacio",$xmlheader);}
	wlog("parsea header : $xmlheader");


	if (($trx = $xmlObj->Parse($xmlheader, "trx"))==""){return func_error("PH1","tag TRX en el header, vacio",$xmlheader);	}
	wlog("header - trx: $trx");
	if (($type = $xmlObj->Parse($xmlheader, "type"))==""){return func_error("PH2","tag TYPE en el header, vacio",$xmlheader);	}
	wlog("header - type: $type");

	if (($trx!="detalle_producto") || ($type!="req"))
		return func_error('PTX', "tag TRX:[$trx] & TYPE:[$type] no coinciden con el requerimiento esperado", $xmlheader);

	$xmlheader = ereg_replace("req" ,"resp",$xmlheader);
	wlog("header para la respuesta: $xmlheader");

	if (($local = $xmlObj->Parse($xmlheader, "local"))==""){return func_error("PH3","tag LOCAL en el header, vacio",$xmlheader);	}
	wlog("header - local: $local");
	if (($pos = $xmlObj->Parse($xmlheader, "pos"))==""){return func_error("PH4","tag POS en el header, vacio",$xmlheader);	}
	wlog("header - pos: $pos");
	if (($operator = $xmlObj->Parse($xmlheader, "operator"))==""){return func_error("PH5","tag OPERATOR en el header, vacio",$xmlheader);}
	wlog("header - operator: $operator");
	if (($date = $xmlObj->Parse($xmlheader, "date"))==""){return func_error("PH6","tag DATE en el header, vacio",$xmlheader);}
	wlog("header - date: $date");
	if (($time = $xmlObj->Parse($xmlheader, "time"))==""){return func_error("PH7","tag TIME en el header, vacio",$xmlheader);}
	wlog("header - time: $time");

	//DATA
	if (($xmldata = $xmlObj->Parse($xml, "data"))==""){return func_error("PD0","tag DATA vacio",$xmlheader);}
	wlog("parsea data : $xmldata");
	if (($barcode = $xmlObj->Parse($xmldata, "barcode"))==""){return func_error("PD1","tag BARCODE en la data, vacio",$xmlheader);}
	wlog("data - barcode : $barcode");

	wlog ("consulta con cod_barra='$barcode' y local='$local'");
	$prodXML = obtiene_producto($barcode, $local);
	if (!$prodXML){	return func_error("P01","El producto no existe en la base de datos", $xmlheader); }

	wlog("producto :$prodXML");
	$xml_resp = '<?xml version="1.0"?>'.chr(13)
		.'<main><header>'.chr(13).$xmlheader.chr(13)
		.'</header><data>'.chr(13).$prodXML.'</data>'.chr(13)
		.'</main>'.chr(13);
	wlog ("respuesta enviada: $xml_resp");
	return $xml_resp;

	}//cons productos

/*********************************************************
Metodo nueva_cotizacion
consulta: envia un codigo de barra en XML
respuesta: Devuelve el detalle del producto en XML
*********************************************************/
function nueva_cotizacion($cons) {

	wlog("nueva_cotizacion : cons:$cons");
	//libxml_use_internal_errors(true);
	$xmlObj = new SimpleXMLParser;
	wlog("nueva_cotizacion : genera objeto:xmlObj:$cons");
	
	if (($xml = $xmlObj->Parse($cons, "main"))==""){return func_error("C00","tag MAIN vacio","");	}	
	wlog("nueva_cotizacion : parsea main : $xml");

	//HEADER
	if (($xmlheader = $xmlObj->Parse($xml, "header"))==""){return func_error("CH0","tag HEADER vacio",$xmlheader);}
	wlog("nueva_cotizacion : parsea header : $xmlheader");

	if (($trx = $xmlObj->Parse($xmlheader, "trx"))==""){return func_error("CH1","tag TRX en el header, vacio",$xmlheader);	}
	wlog("header - trx: $trx");
	if (($type = $xmlObj->Parse($xmlheader, "type"))==""){return func_error("CH2","tag TYPE en el header, vacio",$xmlheader);	}
	wlog("header - type: $type");

	if (($trx!="nueva_cotizacion") || ($type!="req"))
		return func_error('CTX', "tag TRX:[$trx] & TYPE:[$type] no coinciden con el requerimiento esperado", $xmlheader);

	$xmlheader = ereg_replace("req" ,"resp",$xmlheader);
	wlog("header para la respuesta: $xmlheader");

	if (($local = $xmlObj->Parse($xmlheader, "local"))==""){return func_error("CH3","tag LOCAL en el header, vacio",$xmlheader);	}
	wlog("nueva_cotizacion : header - local: $local");
	if (($pos = $xmlObj->Parse($xmlheader, "pos"))==""){return func_error("CH4","tag POS en el header, vacio",$xmlheader);	}
	wlog("nueva_cotizacion : header - pos: $pos");
	if (($operator = $xmlObj->Parse($xmlheader, "operator"))==""){return func_error("CH5","tag OPERATOR en el header, vacio",$xmlheader);}
	wlog("nueva_cotizacion : header - operator: $operator");
	if (($date = $xmlObj->Parse($xmlheader, "date"))==""){return func_error("CH6","tag DATE en el header, vacio",$xmlheader);}
	wlog("nueva_cotizacion : header - date: $date");
	if (($time = $xmlObj->Parse($xmlheader, "time"))==""){return func_error("CH7","tag TIME en el header, vacio",$xmlheader);}
	wlog("nueva_cotizacion : header - time: $time");

	//DATA
	if (($xmldata = $xmlObj->Parse($xml, "data"))==""){return func_error("CD0","tag DATA vacio",$xmlheader);}
	wlog("nueva_cotizacion : parsea data : $xmldata");

	if (($maxProduct = $xmlObj->MaxElements($xmldata, "product"))==0)
		return func_error("C01","XML sin productos",$xmlheader);   

	$i=0;
	while($i<$maxProduct){

		if (($xmlProduct = $xmlObj->Parse($xmldata, "product",$i))==""){
			return func_error("CD1","tag PRODUCT[$i] en la data, vacio",$xmlheader);
			}
		wlog("nueva_cotizacion : parsea product ".($i+1)." : $xmlProduct");
		if (($barcode = $xmlObj->Parse($xmlProduct, "barcode"))==""){return func_error("CD2","<BARCODE> de producto[$i] en el data, vacio",$xmlheader);	}
		wlog("nueva_cotizacion : product - barcode: $barcode");
		if (($qty = $xmlObj->Parse($xmlProduct, "qty"))==""){return func_error("CD3","<QTY> de producto[$i] en el data, vacio",$xmlheader);}
		wlog("nueva_cotizacion : product - qty: $qty");
		if (($price = $xmlObj->Parse($xmlProduct, "price"))==""){return func_error("CD4","<PRICE> de producto[$i] en el data, vacio",$xmlheader);}
		wlog("nueva_cotizacion : product - price: $price");

		//rescata datos del producto
		$qry = "SELECT p.id_producto id_producto, p.cod_prod1 cod_prod1, p.des_corta des_corta, CURDATE() as hoy
		FROM productos p
		JOIN codbarra cb ON (p.id_producto = cb.id_producto AND cb.cod_barra = '$barcode' AND cb.estadoactivo='C')";
		$db_2 = tep_db_query($qry);
		if ($res = tep_db_fetch_array( $db_2 ))	{
			$id_producto= $res['id_producto'];
			$cod_prod1= $res['cod_prod1'];
			$des_corta= $res['des_corta'];
			$fecha_hoy= $res['hoy'];
			}
		
		if (($id_producto=="") || (!$res))
			return func_error("C02","No es posible encontrar el producto con el barcode [$barcode]",$xmlheader);

		wlog("nueva_cotizacion :PRODUCTO $i : id_producto[$id_producto] - barcode[$barcode] - cod_prod1[$cod_prod1] - qty[$qty] - price[$price] - des_corta[$des_corta]");
		$prd[$i]['barcode']    = $barcode;
		$prd[$i]['qty']        = $qty;
		$prd[$i]['price']      = $price;
		$prd[$i]['id_producto']= $id_producto;
		$prd[$i]['cod_prod1']  = $cod_prod1;
		$prd[$i]['des_corta']  = $des_corta;

		$i++;
		}

	//GENERA ENCABEZADO COTIZACION

	// busca id_local
	
	if (!($id_local = busca_id_local($local))) return func_error("C03","No es posible encontrar el local con el codigo local $local",$xmlheader);

	$id_os = inserta_os($id_local, $operator);
	if (!$id_os) return func_error("C04","No es posible insertar la cotizacion",$xmlheader);

	//GENERA DETALLE DE COTIZACION X CADA PRODUCTO 

	$i=0;
	$tot_price=0;
	while($i<$maxProduct){
		$id_os_detalle = inserta_os_detalle($id_os,$prd[$i]['price'],$prd[$i]['qty'],$prd[$i]['cod_prod1'],$prd[$i]['id_producto'], $prd[$i]['des_corta'],$prd[$i]['barcode'] );
		if (!$id_os_detalle)
			return func_error("C04","No es posible insertar el producto ".($prd[$i]['barcode'])." en el detalle de la cotización",$xmlheader);
		$tot_price=$tot_price+( $prd[$i]['price'] * $prd[$i]['qty'] );
		$i++;
		}
	
	//RESPUESTA
	/*
	<main>
	<header>$xmlheader</header>
	<data>
		<cotz>
			<num_cotz>1234567<num_cotz>
			<tot_cotz>29700<tot_cotz>
			<qty_prod>4<qty_prod>
			<date_cotz>10/01/2005</date_cotz>
			<comments>COMMENT_COTZ</comments>			
		</cotz>
	</data>
	</main>

	*/
	$xmlresp='<?xml version="1.0"?>
	<main>
	<header>'.$xmlheader.'</header>
	<data>
		<cotz>
			<num_cotz>'.$id_os.'</num_cotz>
			<tot_cotz>'.$tot_price.'</tot_cotz>
			<qty_prod>'.($i).'</qty_prod>
			<date_cotz>'.$fecha_hoy.'</date_cotz>
			<comments>'.COMMENT_COTZ.'</comments>			
		</cotz>
	</data>
	</main>';

	wlog("nueva_cotizacion : Finaliza con $xmlresp");

	return $xmlresp;
}//nueva_cotizacion

wlog("Entrega el resultado ...");



// Use the request to (try to) invoke the service
$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';

$server->service($HTTP_RAW_POST_DATA);



//tep_db_close();
?>
