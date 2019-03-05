<?php

define('DIR_WEBSERVICES', 'http://192.168.0.26/Desarrollo/centroproytest/ws/');
//define('DIR_WEBSERVICES', 'http://192.168.53.219/centroproy/ws/');

echo "comienzo...<BR>";

// Pull in the NuSOAP code
require_once('lib/nusoap.php');
// Create the client instance
$client = new soapclient(DIR_WEBSERVICES.'wscproyserv.php?wsdl', 'wdsl');


// Check for an error
$err = $client->getError();
if ($err) {
    // Display the error
    echo '<p><b>Constructor error: ' . $err . '</b></p>';
    // At this point, you know the call that follows will fail
}
// Call the SOAP method falla el metodo goobye no existe
//$result = $client->call('goodbye', array('name' => 'Gonzalo Melo Bahamondes'));
//xml version="1.0" encoding="UTF-8"
$msg = '<?xml version="1.0"?><main><header><trx>detalle_producto</trx><type>req</type><local>E510</local><pos>TPS22</pos><operator>OP12</operator><date>2005-08-31</date><time>12:30</time></header><data><barcode>2082000035044</barcode></data></main>';

echo $msg;

// Call the SOAP method
$result = $client->call('detalle_producto', array('cons' => ($msg)));

// Check for a fault
if ($client->fault) {
    echo '<p><b>Fault: ';
    print_r($result);
    echo '</b></p>';
} else {
    // Check for errors
    $err = $client->getError();
    if ($err) {
        // Display the error
        echo '<p><b>Error: ' . $err . '</b></p>';
    } else {
        // Display the result
		print $result;
    }
}

/* Display the request and response */
echo '<h2>Request</h2>';
echo '<pre>' . $client->request, ENT_QUOTES . '</pre>';
echo '<h2>Response</h2>';
echo '<pre>' . $client->response, ENT_QUOTES . '</pre>';


/* Display the debug messages*/
echo '<h2>Debug</h2>';
echo '<pre>' . $client->debug_str, ENT_QUOTES . '</pre>';


?>
