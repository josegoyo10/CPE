<?php
//require_once('../../wsClientUnique/customsoap.php');
require_once('customsoapBOPOS.php');
require_once('../../wsClientUnique/SimpleXMLParser.php');

class Bopos {

	var $endpoint = "http://172.20.5.73/ConsultarInfoBoposMMWeb/sca/ConsultarInfoBoposMediationIFExport1";
	var $ns = "http://ConsultarInfoBoposMediation/intefaces/ConsultarInfoBoposMediationIF";

	public function validar($idTrx){
		$client = new CustomSoapSender($this->endpoint, $this->ns);
		$client->debug = true;
		$response = $client->sendMessage("ConsInfoBopos",
			'<request>' .
				"<idTransaccion>".$idTrx."</idTransaccion>".
			'</request>');

		if (isset($response['response'])) {
			return $response['response'];
		}
		else {
			return false;
		}

	}

}
?>