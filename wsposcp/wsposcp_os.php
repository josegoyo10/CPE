<?
/********************************
WEBSERVICES
Proyecto : Centro de proyectos
Cliente : EASY
Autor : Gisela Estay Jeldes
BBR-i ecommerce &  retail
*********************************/
include_once( "../includes/funciones/database.php" );
include_once("../includes/db_config.php");
include_once("../includes/funciones/generalws.php");
include_once("../includes/funciones/general.php");
/////////////////////////////////////////////////////////////////////
//
// Module    : SimpleXMLParser.php
// DateTime  : 26/07/2005 11:32
// Author    : Phillip J. Whillier
// Purpose  : Very lightwieght "simple" XML parser does not support attributes.
//
/////////////////////////////////////////////////////////////////////
global $usuario,$origenSistema;
define( 'DIR_LOG_WS', '../wsposcp/log_ws/' );
/*********************************/
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
<main>
<header>blah blah</header>
<data>
<error_msg cod_error="xxxx">bla bla</error_msg>
</data>
</main>

*********************************************************/
function func_error($cod, $error_msg,$header,$id_os){
$estado=0;
$XMLerror = '<?xml version="1.0"?><resp><os>'.$id_os.'</os>
		<estado>'.$estado.'</estado>
		<desc>'.$cod.'-'.$error_msg.'</desc></resp>';

//writeeventws("ERROR($cod) : $error_msg");
writeeventws("ERROR($cod) : $XMLerror");
return $XMLerror;
}
/*********************************************************/
function func_error_sap($cod, $error_msg,$header,$sap){
$estado=0;
$XMLerror = '<?xml version="1.0"?><resp><producto><codigo/></producto>
<estado>'.$estado.' </estado>
<desc>'.$cod.'-'.$error_msg.'</desc></resp>';

//writeeventws("ERROR($cod) : $error_msg");
writeeventws("ERROR($cod) : $XMLerror");
return $XMLerror;
}

/*********************************************************/
function func_error_prec($cod, $error_msg,$header,$sap){
$estado=0;
$XMLerror = '<?xml version="1.0" ?><resp><producto/>
		<estado>'.$estado.' </estado>
		<desc>'.$cod.'-'.$error_msg.'</desc></resp>';

//writeeventws("ERROR($cod) : $error_msg");
writeeventws("ERROR($cod) : $XMLerror");
return $XMLerror;
}

/*************************************************************/
function regula_utf8($texto){
$txt = $texto;
$utf8 ='';
$max = strlen($txt);

for ($i = 0; $i < $max; $i++) {
	if ((ord($txt{$i}) > 126) && (ord($txt{$i})< 256)){
		if ((ord($txt{$i}) == 241) || (ord($txt{$i})== 209)){
			$palabra = 'N';
		}else{
			$palabra = '';
		}
	}else {
		$palabra = $txt{$i};
	}
$utf8 .= $palabra;
} // for $i
$cadena=$utf8;
return $cadena;
}			

/*********************************************************
FUNCIONES : inserta_os
*********************************************************/
function inserta_os($enc,&$msg_val){
	$id_local = $enc[0];
	$rut	  = $enc[1];

	$id_cotizacion=inserta_os_encabezado( array_merge ( $ARR, array('id_local'=>$id_local),array('clie_rut'=>$rut)),$msg_val);
	if (($id_cotizacion==0)&&($msg_val!='')){
		writeeventws('inserta_os : Id_cotizacion no se pudo generar');
		return false;
	}
	if (!$id_cotizacion) {
		writeeventws ("inserta_os : No puede rescatar ID de la Orden de Cotizacion ");
		return false;
		}
	return $id_cotizacion;
}
/*********************************************************
FUNCIONES : inserta_os_encabezado
*********************************************************/
function inserta_os_encabezado($ARR,&$msgval) {
	$id_local			=$ARR['id_local'];
	$clie_rut			=$ARR['clie_rut'];
	$id_estado			='SE';
	$id_proyecto		=30589;			
	#$id_direccion		=51864;			
	$id_direccion		=verifica_direccion($ARR['clie_rut']);			
	$os_fechacreacion	=date("Y-m-d H:i:s");
	$os_fechacotizacion	=date("Y-m-d H:i:s");
	$os_fechaestimacion	=date("Y-m-d H:i:s");
	$os_comentarios		='Ingresada desde POS';
	$os_descripcion		='Ingresada desde POS';
	$usuario			='Usuarios POS';
	$USR_ID				=281;
	$origen				='S';
	$USR_ORIGEN			=3;

	// inserta el encabezado la OS
	$query_IEOS = " INSERT INTO os (id_local,clie_rut,id_estado, id_proyecto, id_direccion,os_fechacreacion, os_fechacotizacion, 
	os_comentarios,os_descripcion, usuario, USR_ID, os_fechaestimacion, origen, USR_ORIGEN) 
	values(".$id_local.",".$clie_rut.",'".$id_estado."',".$id_proyecto.",".$id_direccion.",'".$os_fechacreacion."','".
	$os_fechacotizacion."','".$os_comentarios."','".$os_descripcion."','".$usuario."',".$USR_ID.",'".$os_fechaestimacion.
	"','".$origen."','".$USR_ORIGEN."')";
	
	if(!tep_db_query($query_IEOS)){
		writeeventws ("inserta_os_encabezado : Error al realizar la operacion : ".$query_IEOS);
		return false;
	}else{
		$ultimoID = tep_db_insert_id('');
		writeeventws ("inserta_os_encabezado : EXITO! al realizar la operacion nueva OS: ".$ultimoID."  ");
	}
	return $ultimoID;
}

/*********************************************************
FUNCIONES : verifica_direccion
*********************************************************/
function verifica_direccion($clie_rut){
/*verifica si tiene direccion primaria*/
	$direexiste=busca_direccion($clie_rut);
		if ($direexiste){
			return $direexiste;
		}else{
			/*si no tiene inserta direccion dummy para pos*/
			$nuevadireccion=inserta_nueva_direccion($clie_rut);
			/**rescata el id de la direccion*/	
			return $nuevadireccion;
		}
}
/*********************************************************/

/*********************************************************
FUNCIONES : inserta_os_detalle
*********************************************************/
function inserta_os_detalle($origen, $id_tipodespacho, $id_producto, $id_os, $prod_tipo, $prod_subtipo, $precio, $cantidad, $cod_prod1, $des_larga, $ean){

	$query =  " INSERT INTO os_detalle (id_origen, id_tipodespacho, id_producto, id_os, osde_tipoprod, osde_subtipoprod,	 osde_precio, osde_cantidad, cod_sap, osde_descripcion,  cod_barra) 				  
	VALUES(".$origen.",".$id_tipodespacho.",".$id_producto.",".$id_os.",'".$prod_tipo."','".$prod_subtipo."',".$precio.",".$cantidad.",".$cod_prod1.",'".$des_larga."',".$ean.")";

 	if(!tep_db_query($query)){
			return false;
	}
	$id_os_detalle = tep_db_insert_id(1);
	if ($id_os_detalle=="") {
		writeeventws ("inserta_os_detalle : No puede rescatar ID del detalle de la cotizacion : ".$query);
		return false;
		}
	return $id_os_detalle;
}

/*********************************************************
Crea la instancia nueva para el server
*********************************************************/

// nos conectamos a la base de datos
tep_db_connect();
//writeeventws("Conectado ...$rc");
// invoca libreria nusoap
require_once('lib/nusoap.php');
//writeeventws("carga libreria NUSOAP ...");

global $usuario;
// Crea la instancia nueva para el server
$server = new soap_server;

writeeventws("crea instancia soap_server .....wspoccp_os");

$server->configureWSDL('wsposcp', 'urn:wsposcp');

//writeeventws("configura instancia para WDSL ...");
// Register the method to expose

$server->register('Graba_OS',                    // method name
    array('cons' => 'xsd:string'),          // input parameters
    array('resp' => 'xsd:string'),    // output parameters
    'urn:wsposcp',                         // namespace
    'urn:wsposcp#Graba_OS',                   // soapaction
    'rpc',                                    // style
    'encoded',                                // use
    'Vea el manual'        // documentation
);

$server->register('Cons_SAP',                    // method name
    array('cons' => 'xsd:string'),          // input parameters
    array('resp' => 'xsd:string'),    // output parameters
    'urn:wsposcp',                         // namespace
    'urn:wsposcp#Cons_SAP',                   // soapaction
    'rpc',                                    // style
    'encoded',                                // use
    'Vea el manual'        // documentation
);

$server->register('Cons_DESC',                    // method name
    array('cons' => 'xsd:string'),          // input parameters
    array('resp' => 'xsd:string'),    // output parameters
    'urn:wsposcp',                         // namespace
    'urn:wsposcp#Cons_DESC',                   // soapaction
    'rpc',                                    // style
    'encoded',                                // use
    'Vea el manual'        // documentation
);

//writeeventws("registra funcion NUEVA_COTIZACION para instancia WDSL ...");

/*********************************************************
METODOS REGISTRADOS como WEBSERVICEs
*********************************************************/
/* FUNCION CREA NUEVA OS
*********************************************************/
function Graba_OS($cons,&$msg) {
writeeventws("Inicia Graba_OS xml entrada->". $cons);
$enc = array() ;
	$xmlObj = new SimpleXMLParser;
	if (($xml = $xmlObj->Parse($cons, "req"))==""){return func_error("00","tag REQ vacio en xml","");	}	

	//HEADER
	if (($xmlheader = $xmlObj->Parse($xml, "encabezado"))==""){return func_error("01","tag ENCABEZADO vacio en xml",$xmlheader);}
	if (($local  = $xmlObj->Parse($xmlheader, "local"))==""){return func_error("02","tag LOCAL vacio en xml",$xmlheader);	}
	if (($enc[0] = $xmlObj->Parse($xmlheader, "local"))==""){return func_error("02","tag LOCAL vacio en xml",$xmlheader);	}
	if (($rut    = $xmlObj->Parse($xmlheader, "rut"))==""){return func_error("03","tag RUT vacio en xml",$xmlheader);	}
	if (($enc[1] = $xmlObj->Parse($xmlheader, "rut"))==""){return func_error("03","tag RUT vacio en xml",$xmlheader);	}

	if ($local && $rut) {
		writeeventws("Graba_OS : Existe Local y rut");
		$encabezado=1;
	}
	if(!$local){
		$encabezado=0;
		return func_error("04","tag Local en el header, vacio",$xmlheader);
	}
	if(!$rut){
		$encabezado=0;
		return func_error("05","tag Rut en el header, vacio",$xmlheader);
	}
	
	//Revisa numero de productos
	if (($maxProduct = $xmlObj->MaxElements($xml, "detalle"))==0)
		return func_error("06","XML sin productos",$xmlheader);   
 
	$i=0;
	$lineadetalle=0;
	while($i<$maxProduct){
		if (($xmlProduct = $xmlObj->Parse($xml, "detalle",$i))==""){
			return func_error("07","tag PRODUCT[$i] en la detalle de producto, vacio",$xmlheader);
		}
		if (($prd[$i]['precio'] = $xmlObj->Parse($xmlProduct, "precio"))==""){
			return func_error("08","'precio' de producto[$i] en el data, vacio",$xmlheader);	
		}
		if (($prd[$i]['cantidad'] = $xmlObj->Parse($xmlProduct, "cantidad"))==""){
			return func_error("09","'cantidad' de producto[$i] en el data, vacio",$xmlheader);
		}
		if (($prd[$i]['ean'] = $xmlObj->Parse($xmlProduct, "ean"))==""){
			return func_error("10","'ean' de producto[$i] en el data, vacio",$xmlheader);
		}
		if (($prd[$i]['precio'])&&($prd[$i]['cantidad'])&&($prd[$i]['ean'])){
			$lineadetalle++;
		}else{
			$lineadetalle=0;		
			return func_error("11","'precio' o 'cantidad' o 'ean' sin datos ,vacio",$xmlheader);
		}
		$i++;
	}

	//GENERA ENCABEZADO COTIZACION
	tep_db_query("set autocommit=0");
	if ($encabezado &&($lineadetalle==$i)){
		writeeventws('Graba_OS : cumple con 2 parametros de encabezado  y con al menos un detalle');
	}else{
		writeeventws('Graba_OS : faltan datos no puede enviar');
		return func_error("12","no se puede enviar datos para creacion de OS, falta encabezado o detalle",$xmlheader);
	}

	/*Validaciones  Validar local existente*/
	if (!$enc[0]=verifica_local($local)){
		writeeventws('Graba_OS :No se verifica local');
		return func_error("13","No se verifica local para local $local");
	}else{
		writeeventws('Graba_OS : Se verifica local ->'.$enc[0]);
	}

	if (!$enc[1]=verifica_rut($rut)){
		writeeventws('Graba_OS :No se verifica Rut ->'.$rut);
		return func_error("14","No se verifica,erroneo rut $rut");
	}else{
		writeeventws('Graba_OS : Se verifica Rut, rut correcto ->'.$enc[1]);
		$rutnuevo=verifica_cliente($enc[1]);
	}
	if ($rutnuevo){
		writeeventws('Graba_OS : Se obtiene Rut Cliente ->'.$rutnuevo);
		$enc[1]=$rutnuevo;
	}

	//GENERA DETALLE DE COTIZACION X CADA PRODUCTO 
	$i=0;
	while($i<$maxProduct){
		$verifica=1;
		/*Verifica productos*/
		if(!$vp=verifica_precio($prd[$i]['precio'])){
			$verifica=0;
			writeeventws('Graba_OS :No se verifica precio ->'.$prd[$i]['precio']);
			return func_error("16","No se verifica precio =".$prd[$i]['precio'],$prd[$i]['precio'],$id_os);
		}
		if(!$eanl=verifica_ean($prd[$i]['ean'])){
			$verifica=0;
			writeeventws('Graba_OS :No se verifica ean ->'.$prd[$i]['ean']);
			return func_error("17","No se verifica ean =".$prd[$i]['ean']." ",$id_os);
		}
		if(!$eanv=verifica_eancp($prd[$i]['ean'])){
			$verifica=0;			
			writeeventws('Graba_OS :No se verifica ean ->'.$prd[$i]['ean'].' en CPE ');
			return func_error("18","No se verifica ean = ".$prd[$i]['ean']." en CPE ",$prd[$i]['ean'],$id_os);
		}
		if(!$vcantidad=verifica_cantidad($prd[$i]['cantidad'])){
			$verifica=0;
			writeeventws('Graba_OS :No se verifica cantidad ->'.$prd[$i]['cantidad']);
			return func_error("19","No se verifica cantidad =".$prd[$i]['cantidad'],$id_os);
		}
		$i++;
	}
	if($verifica){
	$id_os = inserta_os($enc,$msg);
		if (!$id_os) return func_error("15","No es posible insertar la OS ",$xmlheader,$msg);
		$j=0;
		while($j<$maxProduct){			  
		$origen=4;
		$id_tipodespacho=3; # retira cliente
		$arrayean       = recuperadatos_eancp($prd[$j]['ean']);
		$id_producto    = $arrayean[4];
		$prod_tipo      = $arrayean[8];
		$prod_subtipo   = $arrayean[9];
		$cod_prod1      = $arrayean[1];
  		$des_larga      = $arrayean[7];
			$id_os_detalle = inserta_os_detalle($origen,
								$id_tipodespacho,		     
								$id_producto,
								$id_os,		   
								$prod_tipo,
								$prod_subtipo,		  			    
								$prd[$j]['precio'],
								$prd[$j]['cantidad'],      
								$cod_prod1,
								$des_larga, 
								$prd[$j]['ean']);

			if (!$id_os_detalle) {
				//rollback;
				tep_db_query("rollback");
				return func_error("20","No es posible insertar el producto =".($prd[$j]['ean'])." en el detalle de la OS $id_os",$xmlheader,$id_os);
			}else{
				$estado=1;
			}
			$j++;
		}
	}
	//commit ok;
	tep_db_query("commit");

	//autocommit=1;
	tep_db_query("set autocommit=1");
	
	$xmlresp='<?xml version="1.0"?>
		<resp>
		<os>'.$id_os.'</os>
		<estado>'.$estado.' </estado>
		<desc></desc>
	</resp>';
	writeeventws("Graba_OS : Finaliza con $xmlresp");
	return $xmlresp;
	}//Graba_OS

/* FUNCION BUSCA SAP
*********************************************************/

function Cons_SAP($cons,&$msg) {
writeeventws("Cons_SAP Ingresa XML ->". $cons);
	$enc = array() ;
	$xmlObj = new SimpleXMLParser;
	
	if (($xml = $xmlObj->Parse($cons, "req"))==""){
		return func_error_sap("00","tag REQ vacio en xml","");	
	}		
	//HEADER
	if (($xmlheader = $xmlObj->Parse($xml, "sap"))==""){
		return func_error_sap("01","tag SAP vacio en xml",$xmlheader);
	}
	if (($enc[0] = $xmlObj->Parse($xml, "sap"))==""){
		return func_error_sap("02","tag SAP vacio en xml ,en asignar el sap",$xmlheader);	
	}
	if ($enc[0] ) {
		$sap=verifica_numerosap($enc[0]);
		if(!$sap){
			writeeventws("Cons_SAP : el numero sap buscado no cumple con los parametros");
			return func_error_sap("03","el numero sap $enc[0] buscado no cumple con los parametros",$xmlheader);
		}else{
			writeeventws('Cons_SAP : el numero sap buscado cumple con los parametros->'.$enc[0]);		
		}
	}
	
	/*Validaciones  Validar sap existente*/
	if (!$codigosean=verifica_sapcp($sap)){
		return func_error_sap("04","No se verifica en CPE el codigo SAP $sap",$sap);
	}else{
		writeeventws('Cons_SAP : Trae codigos ean para producto SAP ->'.$sap);
	}
	$i=0;
	foreach ($codigosean as $key=>$value) { 
		$i++;
		$cont=count($codigosean);
		if (($cont-1)!=$i)
			$ean="<ean>".$codigosean[$key]."</ean>\n".$ean;
		else		
			$ean="<ean>".$codigosean[$key]."</ean>".$ean;
	}

	if ($ean){
		writeeventws('Cons_SAP : Trae codigos ean ->'.$ean);
		$estado=1;
	}else{
		return func_error_sap("05","No trae codigos Ean para producto sap $sap",$sap);
		$estado=0;
	}

	$xmlresp='<?xml version="1.0" ?>
	<resp>
		<producto>
			<codigo>
			'.$ean.'
			</codigo>
		</producto>
		<estado>'.$estado.' </estado>
		<desc></desc>
	</resp>';
	writeeventws("Cons_SAP : Finaliza con $xmlresp");
	return $xmlresp;
}//Cons_SAP

/* FUNCION BUSCA PREC
*********************************************************/

function Cons_DESC($cons,&$msg) {
writeeventws("Cons_DESC ->". $cons);
$enc = array() ;
//	writeeventws("busqueda_prec : cons:$cons");
	$xmlObj = new SimpleXMLParser;
	
	if (($xml = $xmlObj->Parse($cons, "req"))==""){return func_error_prec("00","tag REQ vacio en xml","");	}		

	//HEADER
	if (($xmlheader = $xmlObj->Parse($xml, "local"))==""){return func_error_prec("01","tag LOCAL vacio en xml",$xmlheader);}
	if (($enc[0] = $xmlObj->Parse($xml, "local"))==""){return func_error_prec("02","tag LOCAL vacio en xml ,en asignar el local",$xmlheader);	}
	if (($xmlheader = $xmlObj->Parse($xml, "texto"))==""){return func_error_prec("01","tag TEXTO vacio en xml",$xmlheader);}
	if (($enc[1] = $xmlObj->Parse($xml, "texto"))==""){return func_error_prec("02","tag TEXTO vacio en xml ,en asignar el texto",$xmlheader);	}

	if ($enc[0]) {
		if (!$id_local=verifica_local($enc[0])){
			writeeventws('busqueda_prec :No se verifica local');
			return func_error_prec("03","No se verifica local para codlocal $enc[0]");
		}else{
			writeeventws('busqueda_prec : Se verifica local ->'.$enc[0]);
		}
	}else{
		writeeventws('busqueda_prec :No existe el valor en el XML para local');
		return func_error_prec("04","No existe el valor en el XML para local");
	}
	$arrtexto = Array();
	$texto    = $enc[1];
	$texto =str_replace ( '*', '-', $enc[1]);
	$arrtexto = split('-',$texto);
	
	if(verifica_texto($enc[1])){
		foreach ($arrtexto as $key=>$value) { 
			if (verifica_texto($arrtexto[$key])){
				$condicion=" and p.des_larga like '%".$arrtexto[$key]."%' ".$condicion;
			}else{
				writeeventws('busqueda_prec : no cumple con las condiciones de busqueda el texto '.$arrtexto[$key]);
			}
		}
	}else{
		writeeventws('busqueda_prec : no cumple con las condiciones de busqueda el texto '.$enc[1]);
		return func_error_prec("06","No existe texto para busqueda o el texto es muy grande ".$enc[1]);	
	}

	/*valida texto de ingreso  texto >1 y <50*/
	if (!$condicion){
		return func_error_prec("06","No existe texto para busqueda o el texto es muy grande ".$enc[1]);	
	}else{
		if (!$prodprecio=verifica_codprec($condicion,$enc[0])){
			return func_error_prec("07","No se verifica en CPE el texto $enc[1]");
		}else{
			writeeventws('busqueda_prec : Trae codigos ean para producto descripcion->'.$enc[1]);
			$estado=1;
		}
	}
	$i=0;

if ($prodprecio){
	$contreg = mysql_num_rows($prodprecio);
	while( $res = tep_db_fetch_array( $prodprecio ) ) {
		$i++;
		$codprec="<producto>".
		"<desc>".regula_utf8($res['des_larga'])."</desc>\n".
		"<codigo>\n".
		"<ean>".$res['cod_barra']."</ean>\n".
		"<precio>".$res['prec_valor']."</precio>\n".
		"</codigo>\n".
		"</producto>\n".$codprec;
	}
}else{
		return func_error_prec("06","No se verifica en CPE el texto $enc[1]");
}
	$xml=$codprec;
	$xmlresp='<?xml version="1.0"?>
	<resp>'.$xml.'<estado>'.$estado.' </estado>
	<desc></desc>
	</resp>';
	writeeventws("Cons_DESC : respuesta Finaliza con $xmlresp");
	return $xmlresp;
}//busqueda_prec


// Use the request to (try to) invoke the service
$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
$server->service($HTTP_RAW_POST_DATA);

//tep_db_close();
?>
