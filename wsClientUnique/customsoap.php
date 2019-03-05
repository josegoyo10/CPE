<?php
require_once('lib/nusoap.php');

class CustomSoapSender {
	
	var $ns;	
	var $endpoint;
	var $encoding;
	
	public function CustomSoapSender($endpoint, $ns = 'http://tempuri.org', $encoding = 'ISO-8859-1') {
		$this->endpoint = $endpoint;
		$this->ns = $ns;
		$this->encoding = $encoding;
	}
	
	public function sendMessage($method, $body, $soapAction = '') {
		$xml = '';
		$xml .= "<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" 
			xmlns:ns1=\"$this->ns\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" 
			xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\">";
		$xml .= '<soapenv:Body>';
		$xml .= "<ns1:$method>";
		$xml .= $body; 
		$xml .= "</ns1:$method>";
		$xml .= '</soapenv:Body>';
		$xml .= '</soapenv:Envelope>';
		$client = new soapclient($this->endpoint, true);
		$client->soap_defencoding = $this->encoding;
		$client->endpointType = 'soap';
		//$client->setUseCURL(true);
		$ret = $client->send($xml, $soapAction);
		//print $client->debug_str;		
		return 	$ret;
	}
	
}
?>