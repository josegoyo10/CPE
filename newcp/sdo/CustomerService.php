<?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');
require_once includePath('sdo/WebServiceClient.php');

/**
 * Service Access Object for Customer Web Services.
 *
 * @author Frank D. Martinez <mnesarco@gmail.com>
 */
class CustomerService extends WebServiceClient {

    var $endpointURI = '/UniqueClientMMWeb/sca/ClienteUnicoIFExport1';
    var $namespace = 'http://ClienteUnicoMediation/intefaces/ClienteUnicoIF';

    public static function GetInstance() {
        $inst = new CustomerService;
        $inst->endpointURL =
            'http://'. WS_ESB_HOST . ':' . WS_ESB_PORT . $inst->endpointURI;
        return $inst;
    }

    public function findById($id) {
        $id = intval($id);
        $body =
        "<input1>" .
            htmlspecialchars("<request><customer><Source>CP</Source><IdCustomer>$id</IdCustomer></customer></request>") .
        "</input1>";
        $response = $this->sendLiteralMessage('SearchById', $body);
        if ($response) {
            $xml = simplexml_load_string($response);
            $customers = $xml->xpath('//response/customer');
            if ($customers) {
                $customer = new stdClass;
                foreach ($customers[0]->children() as $e) {
                    $attr = $e->getName();
                    $customer->$attr = utf8_decode((string) $e);
                }
                if (isset($customer->FirstName) && trim($customer->FirstName) != '') {
                    return $customer;
                }
                else {
                    return false;
                }
            }
            else {
                return false;
            }
        }
        else {
            return false;
        }
    }

    public function create($customer) {
        $body = $this->encode($customer);
        $response = $this->sendMessage('CreateClient', $body);
        return $response['output'];
    }

    public function update($customer) {
        $body = $this->encode($customer);
        $response = $this->sendMessage('UpdateClient', $body);
        return $response['output'];
    }

    private function encode($customer) {
        $gender = $customer->gender == 1 ? 'F' : 'M';
        return '<input>' . htmlspecialchars("<request>
            <customer>
                <IdCustomer>$customer->id</IdCustomer>
                <IdCategory>$customer->categoryId</IdCategory>
                <IdTypeContribuyente>2</IdTypeContribuyente>
                <TypeCustomer>
                    <IdTypeCustomer>6</IdTypeCustomer>
                </TypeCustomer>
                <IdDoc>$customer->doctype</IdDoc>
                <FirstName>$customer->firstname</FirstName>
                <Surname1>$customer->surname1</Surname1>
                <Surname2>$customer->surname2</Surname2>
                <Address>$customer->address</Address>
                <Phone>$customer->homePhone</Phone>
                <Phone2>$customer->mobile</Phone2>
                <Fax>$customer->fax</Fax>
                <Email>$customer->email</Email>
                <Location>$customer->locationId</Location>
                <AgeRange>0</AgeRange>
                <Quota>0</Quota>
                <Gender>$gender</Gender>
                <Occupation></Occupation>
                <ReteIca>false</ReteIca>
                <ReteFuente>false</ReteFuente>
                <ReteIva>false</ReteIva>
                <State>0</State>
                <ExenIva>false</ExenIva>
                <OtrIva>false</OtrIva>
                <Status>A</Status>
            </customer>
        </request>") . '</input>';
    }

}
?>
