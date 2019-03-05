<?php
define('DIR_WEBSERVICES', 'http://localhost/centroproy2/ws/');


// Pull in the NuSOAP code
require_once('lib/nusoap.php');
// Create the client instance
$client = new soapclient(DIR_WEBSERVICES.'wscproyserv.php?wsdl', true);


// Check for an error
$err = $client->getError();
if ($err) {
    // Display the error
    echo '<p><b>Constructor error: ' . $err . '</b></p>';
    // At this point, you know the call that follows will fail
}
// Call the SOAP method falla el metodo goobye no existe
//$result = $client->call('goodbye', array('name' => 'Gonzalo Melo Bahamondes'));


// Call the SOAP method
$result = $client->call('nueva_cotizacion', array('cons' => '
<?xml version="1.0"?>
<main>
	<header>
		<trx>nueva_cotizacion</trx>
		<type>req</type>
		<local>E502</local>
		<pos>TP23</pos>
		<operator>Rodrigo Bola8</operator>
		<date>2005-08-31</date>
		<time>12:00</time>
	</header>
	<data>
		<product>
			<barcode>7798054473033</barcode>
			<qty>2</qty> 
			<price>14980</price>
		</product>
		<product>
			<barcode>7798054473026</barcode>
			<qty>5</qty> 
			<price>14980</price>
		</product>
		<product>
			<barcode>7798054471404</barcode>
			<qty>7</qty> 
			<price>21980</price>
		</product>
		<product>
			<barcode>7798054471305</barcode>
			<qty>1</qty> 
			<price>21980</price>
		</product>
	</data>
</main>
'));

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
        print_r($result);
    }
}

/* Display the request and response */
echo '<h2>Request</h2>';
echo '<pre>' . $client->request, ENT_QUOTES . '</pre>';
echo '<h2>Response</h2>';
echo '<pre>' . $client->response, ENT_QUOTES . '</pre>';
// Display the debug messages
echo '<h2>Debug</h2>';
echo '<pre>' . $client->debug_str, ENT_QUOTES . '</pre>';


?>
