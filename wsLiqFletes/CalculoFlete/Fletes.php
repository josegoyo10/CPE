<?php
require_once('../../wsLiqFletes/ServiciosWeb/customsoap.php');
require_once('../../includes/nusoap/simplexml.php');


class Fletes {
	var $endpoint = "http://172.20.5.73/CalculoFleteMMWeb/sca/CalculoFleteMediationIFExport1";
	var $ns = "http://CalculoFleteMediation/intefaces/CalculoFleteMediationIF";

	public function calcular($xml){

		$client = new CustomSoapSender($this->endpoint, $this->ns);
		//$client->debug= true;
		$response = $client->send("CalcularFlete",
			'<request>' . $xml . '</request>');

		if (isset($response['response'])) {
			return $response['response'];
		}
		else {
			return false;
		}

	}

}
?>