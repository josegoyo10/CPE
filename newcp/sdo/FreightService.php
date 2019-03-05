<?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');
require_once includePath('sdo/WebServiceClient.php');
require_once includePath('business/Location.php');

/**
 * Servicio de cálculo de fletes.
 *
 * @author Frank D. Martinez <mnesarco@gmail.com>
 */
class FreightService extends WebServiceClient {

    var $endpointURI = '/CalculoFleteMMWeb/sca/CalculoFleteMediationIFExport1';
    var $namespace = 'http://CalculoFleteMediation/intefaces/CalculoFleteMediationIF';

    /**
     * Devuelve una instancia configurada para invocar el servicio.
     * @return FreightService
     */
    public static function GetInstance() {
        $inst = new FreightService;
        $inst->endpointURL =
            'http://'. WS_ESB_HOST . ':' . WS_ESB_PORT . $inst->endpointURI;
        return $inst;
    }

    /**
     * Calcular tarifa de flete.
     * @param Location $sourceLocation Ubicación origen
     * @param Location $targetLocation Ubicación destino
     * @param string $address Dirección destino
     * @param int $dispatchType tipo de despacho: 1=Express, 2=Programado
     * @param float $weight Pero en Kg
     * @param int $storeId Id de la tienda
     * @param int $carrier Tipo de cálculo: 0=Tarifa cliente, 1=Costo
     * @return array datos del flete
     */
    public function getFreightFare(
        Location $sourceLocation, Location $targetLocation,
        $address, $dispatchType, $weight, $storeId, $carrier = 0) {

        $body="<request>
            <despacho>
                <direccion>$address</direccion>
                <idDepartamento>$targetLocation->departmentId</idDepartamento>
                <idMunicipio>$targetLocation->provinceId</idMunicipio>
                <idCentroPoblado>$targetLocation->cityId</idCentroPoblado>
                <idLocalidad>$targetLocation->localityId</idLocalidad>
                <idBarrio>$targetLocation->neighbourhoodId</idBarrio>
            </despacho>
            <centroSuministro>
                <idLocal>$storeId</idLocal>
                <idDepartamento>$sourceLocation->departmentId</idDepartamento>
                <idMunicipio>$sourceLocation->provinceId</idMunicipio>
                <idCentroPoblado>$sourceLocation->cityId</idCentroPoblado>
                <idLocalidad>$sourceLocation->localityId</idLocalidad>
                <idBarrio>$sourceLocation->neighbourhoodId</idBarrio>
            </centroSuministro>
            <entregaProductos>
                <lstTipoDespacho>
                    <codigoTipo>$dispatchType</codigoTipo>
                    <peso>$weight</peso>
                </lstTipoDespacho>
            </entregaProductos>
            <codEmpresaTransportadora>$carrier</codEmpresaTransportadora>
        </request>";
        
        $response = $this->sendMessage('CalcularFlete', $body);
        if (isset($response['response']['data']['lstValorFlete']) && is_array($response['response']['data']['lstValorFlete'])) {
            return $response['response']['data'];
        }
        else if (isset($response['response']['exception']['message'])) {
            UDebug::Log('FreightService::getFreightFare(...) ' . $response['response']['exception']['message']);
            return null;
        }
        else {
            UDebug::Log('FreightService::getFreightFare(...) returned nothing');
            return null;
        }

    }

}
?>
