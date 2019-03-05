<?php
require_once('ClientUnique.php');


$response = ClientUnique::searchById(8373597);

if ($response) {
	print_r ($response);
}
else {
	print "Error del Servidor";
}


?>