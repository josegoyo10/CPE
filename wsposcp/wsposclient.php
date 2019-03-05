<?php
#define('DIR_WEBSERVICES', 'http://omega/centroproy/wsposcp/');

define('DIR_WEBSERVICES', URL_CPROY.'/wsposcp/');

// Pull in the NuSOAP code
require_once('../../includes/nusoap/nusoap.php');
require_once('../../includes/nusoap/simplexml.php');
// Create the client instance
$client = new soapclient(DIR_WEBSERVICES.'wsposcp_os.php?wsdl', true);

function envia_nueva_os($xml,&$msgerror){
	// Create the client instance
	$client = new soapclient(DIR_WEBSERVICES.'wsposcp_os.php?wsdl', true);
	// Check for an error
	$err = $client->getError();
	if ($err) {
		// Display the error
		echo '<p><b>Constructor error: ' . $err . '</b></p>';
		}
	// Call the SOAP method
	$result = $client->call('Graba_OS', array('cons' => $xml));
	// chequea error del client
	if ($client->fault) {
		echo '<p><b>Fault: ';
		print_r($result);
		echo '</b></p>';
		return 0;
		} 
	// error desde el server
	$err = $client->getError();
	if ($err) {
		// Display the error
		echo '<p><b>Error: ' . $err . '</b></p>';
		return 0;
		} 
	//ok despliega resultado
	$xmlObj = new SimpleXMLParser;
	$result_error=$result;
	writelog("Graba_OS : respuesta: $resp_error");
	$msgerror = $xmlObj->Parse($result, "desc");
	
	if ($msgerror){
		return $result_error;
	}
	return $result_error;
	}//function envia_nueva_os


function busca_sap($xml,&$msgerror){
	// Create the client instance
	$client = new soapclient(DIR_WEBSERVICES.'wsposcp_os.php?wsdl', true);
	// Check for an error
	$err = $client->getError();
	if ($err) {
		// Display the error
		echo '<p><b>Constructor error: ' . $err . '</b></p>';
		}
	// Call the SOAP method
	$result = $client->call('Cons_SAP', array('cons' => $xml));
	// chequea error del client
	if ($client->fault) {
		echo '<p><b>Fault: ';
		print_r($result);
		echo '</b></p>';
		return 0;
		} 
	// error desde el server
	$err = $client->getError();
	if ($err) {
		// Display the error
		echo '<p><b>Error: ' . $err . '</b></p>';
		return 0;
		} 
	//ok despliega resultado
	$xmlObj = new SimpleXMLParser;
	$resp_error=$result;
	writelog("busca_sap : respuesta: $resp_error");
	$msgerror = $xmlObj->Parse($result, "desc");
	
	if ($msgerror){
		return $resp_error;
	}
	return $resp_error;
	}//function busqueda_sap



function busca_prec($xml,&$msgerror){
	// Create the client instance
	$client = new soapclient(DIR_WEBSERVICES.'wsposcp_os.php?wsdl', true);
	// Check for an error
	$err = $client->getError();
	if ($err) {
		// Display the error
		echo '<p><b>Constructor error: ' . $err . '</b></p>';
		}
	// Call the SOAP method
	$result = $client->call('Cons_DESC', array('cons' => $xml));
	// chequea error del client
	if ($client->fault) {
		echo '<p><b>Fault: ';
		print_r($result);
		echo '</b></p>';
		return 0;
		} 
	// error desde el server
	$err = $client->getError();
	if ($err) {
		// Display the error
		echo '<p><b>Error: ' . $err . '</b></p>';
		return 0;
		} 
	//ok despliega resultado
	$xmlObj = new SimpleXMLParser;
	$resp_error=$result;	
        writelog("Cons_DESC : respuesta: $resp_error");	
	$msgerror = $xmlObj->Parse($result, "desc");
	if ($msgerror){
		return $resp_error;
	}
	return $resp_error;

	}//function busca_prec



//Display the request and response 
/*echo '<h2>Request</h2>';
echo '<pre>' . $client->request, ENT_QUOTES . '</pre>';
echo '<h2>Response</h2>';
echo '<pre>' . $client->response, ENT_QUOTES . '</pre>';
// Display the debug messages
echo '<h2>Debug</h2>';
echo '<pre>' . $client->debug_str, ENT_QUOTES . '</pre>';*/
?>
