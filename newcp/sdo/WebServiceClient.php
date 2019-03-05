<?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');
require_once includePath('lib/nusoap/nusoap.php');

/**
 * Description of WebServiceClient
 *
 * @author Frank D. Martinez <mnesarco@gmail.com>
 */
class WebServiceClient {

    var $endpointURL = null;
    var $namespace = null;
    var $encoding = 'UTF-8';

	protected function sendMessage($method, $body, $soapAction = '') {
		$xml = "<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\"
			xmlns:ns1=\"$this->namespace\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\"
			xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\">";
		$xml .= '<soapenv:Body>';
		$xml .= "<ns1:$method>";
		$xml .= $body;
		$xml .= "</ns1:$method>";
		$xml .= '</soapenv:Body>';
		$xml .= '</soapenv:Envelope>';
		$client = new soapclient($this->endpointURL, true);
		$client->soap_defencoding = $this->encoding;
		$client->endpointType = 'soap';
		$ret = $client->send($xml, $soapAction);
        return 	$ret;
	}

	protected function sendLiteralMessage($method, $body, $soapAction = '') {
		$xml = "<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\"
			xmlns:ns1=\"$this->namespace\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\"
			xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\">";
		$xml .= '<soapenv:Body>';
		$xml .= "<ns1:$method>";
		$xml .= $body;
		$xml .= "</ns1:$method>";
		$xml .= '</soapenv:Body>';
		$xml .= '</soapenv:Envelope>';
		$client = new soapclient($this->endpointURL, true);
		$client->soap_defencoding = $this->encoding;
		$client->endpointType = 'soap';
		$client->send($xml, $soapAction);
        $ret = htmlspecialchars_decode($client->responseData);
        $ret = str_replace('&#xD;', "\n", $ret);
        return 	$ret;
	}
}
?>
