<?php
// Estas funciones ya estan declaradas en "aplication_top.php"
require_once('lib/nusoap.php');

class CustomSoapSender {
	
	var $ns;	
	var $endpoint;
	var $encoding = 'UTF-8';
	var $debug = false;
	
	public function CustomSoapSender($endpoint, $ns = 'http://tempuri.org', $encoding = 'UTF-8') {
		$this->endpoint = $endpoint;
		$this->ns = $ns;
		$this->encoding = $encoding;
	}
	
	public function send($method, $body, $soapAction = '') {
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
		$client->setUseCURL(false);
		$client->soap_defencoding = $this->encoding;
		$client->endpointType = 'soap';
		
		$ret =& $client->send($xml, $soapAction, 30, 30);
		if ($this->debug) {
			print $client->debug_str;
		}
		return $ret;
	}
	
}
?>