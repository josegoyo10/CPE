<?php
require_once('customsoap.php');
require_once('SimpleXMLParser.php');

class RealInventory {

	public function searchInventoryById($xml) {
		$endpoint = "http://172.20.5.73/StockOnLineWeb/sca/StockOnLineExport1";
		$ns="http://co.com.easy.stockonline.interfaces";

		$client = new CustomSoapSender($endpoint,$ns);
		$response = $client->sendMessage("SearchRealInventoryByCode", 
		"<request1>" .
		htmlspecialchars($xml)
		. "</request1>");
		
		if (isset($response)) {
			return SimpleXMLParser::parseSearchInventoryXML($response['response1']);
		}
		else {
			return false;
		}
		
		return $response;
	}

	
}


 
  

?>
