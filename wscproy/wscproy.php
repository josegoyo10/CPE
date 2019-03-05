<?
/********************************
WEBSERVICES
Proyecto : Centro de Despacho
Cliente : EASY
Autor : Gisela Estay Jeldes
BBR-i ecommerce &  retail
*********************************/
include_once( "../includes/funciones/database.php" );
include_once("../includes/db_config.php");
include_once("../includes/funciones/general.php");
/////////////////////////////////////////////////////////////////////
//
// Module    : SimpleXMLParser.php
// DateTime  : 26/07/2005 11:32
// Author    : Phillip J. Whillier
// Purpose  : Very lightwieght "simple" XML parser does not support attributes.
//
/////////////////////////////////////////////////////////////////////
define( 'DIR_LOG', '../log/' );
global $usuario;
 function Cambia_Estado_Ot($ARR,&$msgval) {
	 global $usuario;
		$usuario			=$ARR['usr_id'];
		$usr_nombre			= get_login_usr($usuario);
		$fecha				= DATE('Y/m/d h:i:s');
		$idDespacho			=$ARR['idDespacho'];
		$id_estado			=$ARR['id_estado'];
		$ot_id				=$ARR['ot_id'];
		$usrcrea			=$ARR['usrcrea'];
		$msgod			    =$ARR['msgod'];

		//* sacamos el tipo de OT /PE/PS
		$querytipo="Select ot_tipo,id_estado from ot where ot_id=".$ot_id;
		$rq = tep_db_query($querytipo);
		$res = tep_db_fetch_array( $rq );
		$tipo=$res['ot_tipo'];
		$estado=$res['id_estado'];
		if ($tipo=='PE'){
			$estadoFinal='EM';
			$producto='Producto de Pedido Especial';
			$NomEstado='Entregado';
		}else{
			$estadoFinal='PM';
			$producto='Producto de Stock';
			$NomEstado='Terminado';
		}
		if ($estado=='ED' ||$estado=='PD' ){
			tep_db_query("SET AUTOCOMMIT=0");
			$success = true;
			// updatea el estado de la ot
            $query_UpOt = "UPDATE ot SET id_estado='".$estadoFinal."' where ot_id=".$ot_id;
			$success = $success && tep_db_query($query_UpOt);
			if ($success){
				insertahistorial('La OT '.$ot_id.' de '.$producto.' ha cambiado a estado '.$NomEstado.' por entrega de las ordenes de despacho '.$msgod,0,$ot_id);
				$retorno=$ot_id;
				tep_db_query("COMMIT");
			}else {
				tep_db_query("ROLLBACK");
				$msg_val=$ot_id;
				$retorno=0;
				writelog('ERROR: Cambia_Estado_Ot No se puso cambiar a estado la OT='.$ot_id);
			}
			tep_db_query("SET AUTOCOMMIT=1");
		}else{
			writelog('El estado se cambio enteriormente');
				return func_error("CD10","El estado de la OT no permite dejar cambiar a estado final en Centro de Proyectos",$xmlheader,$msg);
			$retorno=0;
		}


	return $retorno;
	}

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
function func_error($cod, $error_msg,$header,$camposql){

$XMLerror = '<main><header>'.chr(13).$header.chr(13).'</header>
<respuesta>'.chr(13).'
<status>ERROR</status>
<camposErr>'.$error_msg.'</camposErr>
<glosastatus>'.$error_msg.'</glosastatus>'.chr(13).'


</respuesta>'.chr(13).'</main>';
writelog("ERROR($cod) : $error_msg");

return $XMLerror;
}
/*********************************************************
FUNCIONES : Cambia_Estado
*********************************************************/
function Cambia_Estado($enc,&$msg_val){
	$idDespacho = $enc[0];
	$id_estado  = $enc[1];
	$ot_id      = $enc[2];
	$usrcrea    = $enc[3];
	$msgod	    = $enc[4];

	$ARR = array_merge (array('idDespacho'=>$idDespacho),array('id_estado'=>$id_estado),array('ot_id'=>$ot_id),array('usrcrea'=>$usrcrea),array('msgod'=>$msgod));
	$ot_id=Cambia_Estado_Ot( $ARR,$msg_val);
	if (($ot_id==0)&&($msg_val!='')){
		return false;
	}
	
	if (!$ot_id) {
		writelog ("Cambia_Estado : No puede rescatar ID de la orden de trabajo ");
		return false;
		}
	return $ot_id;
	}

/*********************************************************
Crea la instancia nueva para el server
*********************************************************/

// nos conectamos a la base de datos
tep_db_connect();

writelog("Conectado ...$rc");
// invoca libreria nusoap
require_once('lib/nusoap.php');
writelog("carga libreria NUSOAP ...");


// Crea la instancia nueva para el server
$server = new soap_server;

writelog("crea instancia soap_server ...");

$server->configureWSDL('wscproy', 'urn:wscproy');

writelog("configura instancia para WDSL ...");


// Register the method to expose
$server->register('EnviaOd_Ot',                    // method name
    array('cons' => 'xsd:string'),          // input parameters
    array('resp' => 'xsd:string'),    // output parameters
    'urn:wscproy',                         // namespace
    'urn:wscproy#EnviaOd_Ot',                   // soapaction
    'rpc',                                    // style
    'encoded',                                // use
    'Vea el manual'        // documentation
);

writelog("registra funcion NUEVA_COTIZACION para instancia WDSL ...");

/*********************************************************
METODOS REGISTRADOS como WEBSERVICEs
*********************************************************/

function EnviaOd_Ot($cons,&$msg) {
$enc = array() ;
//	writelog("EnviaOd_Ot : cons:$cons");
	//libxml_use_internal_errors(true);
	$xmlObj = new SimpleXMLParser;
	writelog("EnviaOd_Ot : genera objeto:xmlObj:$cons");
	
	if (($xml = $xmlObj->Parse($cons, "main"))==""){return func_error("C00","tag MAIN vacio","");	}	
//	writelog("EnviaOd_Ot : parsea main : $xml");

	//HEADER
	if (($xmlheader = $xmlObj->Parse($xml, "header"))==""){return func_error("CH0","tag HEADER vacio",$xmlheader);}
//	writelog("EnviaOd_Ot : parsea header : $xmlheader");

	if (($fecha = $xmlObj->Parse($xmlheader, "fecha"))==""){return func_error("CH1","tag FECHA en el header, vacio",$xmlheader);	}
//	writelog("header - fecha: $fecha");
	if (($hora = $xmlObj->Parse($xmlheader, "hora"))==""){return func_error("CH2","tag HORA en el header, vacio",$xmlheader);	}
//	writelog("header - hora: $hora");
	if (($operador = $xmlObj->Parse($xmlheader, "operador"))==""){return func_error("CH3","tag OPERADOR en el header, vacio",$xmlheader);	}
//	writelog("header - operador: $operador");
	if (($sistema = $xmlObj->Parse($xmlheader, "sistema"))==""){return func_error("CH4","tag SISTEMA en el header, vacio",$xmlheader);	}
//	writelog("header - sistema: $sistema");

	if ($sistema!="Centro Despacho")
		return func_error('CHX', "tag SISTEMA:[$sistema] no es el sistema esperado [centro proy]", $xmlheader);

	//	$xmlheader = ereg_replace("req" ,"resp",$xmlheader);
	writelog("header para la respuesta: $xmlheader");

	//DATA
	if (($xmldata = $xmlObj->Parse($xml, "data"))==""){return func_error("CD0","tag DATA vacio",$xmlheader);}
	//writelog("EnviaOd_Ot : parsea data : $xmldata");

	// ENCABEZADO
	if (($xmlencabezado = $xmlObj->Parse($xmldata, "encabezado"))==""){return func_error("CD1","tag ENCABEZADO vacio",$xmlheader);}
	if (($enc[0] = $xmlObj->Parse($xmlencabezado, "idDespacho"))==""){return func_error("CE1","tag IDDESPACHO en el header, vacio",$xmlheader);	}
	if (($enc[1] = $xmlObj->Parse($xmlencabezado, "id_estado"))==""){return func_error("CE2","tag ID_ESTADO en el header, vacio",$xmlheader);	}
	if (($enc[2] = $xmlObj->Parse($xmlencabezado, "ot_id"))==""){return func_error("CE3","tag OT_ID en el header, vacio",$xmlheader);	}
	//if (($enc[3] = $xmlObj->Parse($xmlencabezado, "usrcrea"))==""){return func_error("CE4","tag USRCREA en el header, vacio",$xmlheader);	}
	if (($enc[3] = $xmlObj->Parse($xmlencabezado, "usrcrea"))==""){	}
	if (($enc[4] = $xmlObj->Parse($xmlencabezado, "msgod"))==""){return func_error("CE4","tag MSGOD en el header, vacio",$xmlheader);	}
	writelog("EnviaOd_Ot : parsea data : $xmldata");

	//GENERA ENCABEZADO COTIZACION
	//autocommit = 0;
	tep_db_query("set autocommit=0");

	$ot_id = Cambia_Estado($enc,$msg);

	//writelog('MSG---->'.$ot_id);

	if (!$ot_id) return func_error("CD4","No es posible cambiar el estado ",$xmlheader,$msg);

	//commit ok;
	//tep_db_query("commit");
	//tep_db_query("set autocommit=1");
	
	$xmlresp='<?xml version="1.0"?>
	<main>
	<header>'.$xmlheader.'</header>
	<respuesta>
		<status>OK</status>
		<glosastatus>ot_id '.$ot_id.' Updateado estado terminal</glosastatus>
		<id_despacho>'.$ot_id.'</id_despacho>
		<despachoOD>'.$enc[0].'</despachoOD>
		<msgod>'.$enc[4].'</msgod>
	</respuesta>
	</main>';
	writelog("EnviaOd_Ot : Finaliza con $xmlresp");
	return $xmlresp;
	}//EnviaOd_Ot

	writelog("Entrega el resultado ...");

// Use the request to (try to) invoke the service
$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
$server->service($HTTP_RAW_POST_DATA);

//tep_db_close();
?>
