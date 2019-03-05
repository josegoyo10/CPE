<?php
require_once('customsoap.php');
require_once('SimpleXMLParser.php');

class ClientUnique {

	public function searchById($id) {
		$endpoint = "http://172.20.5.73/UniqueClientMMWeb/sca/ClienteUnicoIFExport1";
		$ns="http://ClienteUnicoMediation/intefaces/ClienteUnicoIF";

		$client = new CustomSoapSender($endpoint,$ns);
		$response = $client->sendMessage("SearchById",
			'<input1>' .
				htmlspecialchars("<request><customer><Source>20</Source><IdCustomer>$id</IdCustomer></customer></request>") .
			'</input1>');


		if (isset($response['output1'])) {
			//print($response['output1']);
			return SimpleXMLParser::parseSearchXML($response['output1']);
		}
		else {
			return false;
		}
	}

	function createClient($xmlCreate){

		$endpoint = "http://172.20.5.73/UniqueClientMMWeb/sca/ClienteUnicoIFExport1";
		$ns="http://ClienteUnicoMediation/intefaces/ClienteUnicoIF";

		$client = new CustomSoapSender($endpoint,$ns);
		$response = $client->sendMessage("CreateClient",
			'<input>' .
				htmlspecialchars($xmlCreate) .
			'</input>');				

		if (isset($response['output'])) {
			//print($response['output']);
			return SimpleXMLParser::parseResponseXML($response['output']);
		}
		else {
			return false;
		}

	}

	function updateClient($xmlUpdate){
		$endpoint = "http://172.20.5.73/UniqueClientMMWeb/sca/ClienteUnicoIFExport1";
		$ns="http://ClienteUnicoMediation/intefaces/ClienteUnicoIF";

		$client = new CustomSoapSender($endpoint,$ns);
		$response = $client->sendMessage("UpdateClient",
			'<input>' .
				htmlspecialchars($xmlUpdate) .
			'</input>');

		if (isset($response['output'])) {
			//print $response['output'];
			return SimpleXMLParser::parseResponseXML($response['output']);
		}
		else {
			return false;
		}
	}
}
?>