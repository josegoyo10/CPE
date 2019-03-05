<?php
require_once('Fletes.php');

/*
$xml="<despacho>
      <direccion>Cra 127</direccion>
      <idDepartamento>11</idDepartamento>
      <idMunicipio>001</idMunicipio>
      <idCentroPoblado>000</idCentroPoblado>
      <idLocalidad>11</idLocalidad>
      <idBarrio>00</idBarrio>
    </despacho>
    <centroSuministro>
      <idLocal>1</idLocal>
      <idDepartamento>11</idDepartamento>
      <idMunicipio>001</idMunicipio>
      <idCentroPoblado>000</idCentroPoblado>
      <idLocalidad>11</idLocalidad>
      <idBarrio>00</idBarrio>
    </centroSuministro>
    <entregaProductos>
      <lstTipoDespacho>
        <codigoTipo>2</codigoTipo>
        <peso>1500</peso>
      </lstTipoDespacho>
    </entregaProductos>
    <codEmpresaTransportadora>1</codEmpresaTransportadora>";
*/

/*$xml="<despacho>
                    <direccion>CLL 25 SUR # 68 - 19 APTO 102</direccion>
                    <idDepartamento>91</idDepartamento>
                    <idMunicipio>1</idMunicipio>
                    <idCentroPoblado>5</idCentroPoblado>
                    <idLocalidad>0</idLocalidad>
                    <idBarrio>0</idBarrio>
                </despacho>
          <centroSuministro>
                    <idLocal>22</idLocal>
                    <idDepartamento>05</idDepartamento>
                    <idMunicipio>001</idMunicipio>
                    <idCentroPoblado>000</idCentroPoblado>
                    <idLocalidad>015</idLocalidad>
                    <idBarrio>000</idBarrio>
                </centroSuministro>
          <entregaProductos>
                    <lstTipoDespacho>
                            <codigoTipo>2</codigoTipo>
                            <peso>0.0080000</peso>
                    </lstTipoDespacho>
          </entregaProductos>
                <codEmpresaTransportadora>0</codEmpresaTransportadora>";*/
   //funciona flete
   /*  $xml="<despacho>
                    <direccion>CALLE 70#28 71</direccion>
                    <idDepartamento>5</idDepartamento>
                    <idMunicipio>1</idMunicipio>
                    <idCentroPoblado>0</idCentroPoblado>
                    <idLocalidad>8</idLocalidad>
                    <idBarrio>16</idBarrio>
                </despacho>
          <centroSuministro>
                    <idLocal>22</idLocal>
                    <idDepartamento>05</idDepartamento>
                    <idMunicipio>001</idMunicipio>
                    <idCentroPoblado>000</idCentroPoblado>
                    <idLocalidad>015</idLocalidad>
                    <idBarrio>000</idBarrio>
                </centroSuministro>
          <entregaProductos>
                    <lstTipoDespacho>
                            <codigoTipo>2</codigoTipo>
                            <peso>0.5000000</peso>
                    </lstTipoDespacho>
          </entregaProductos>
                <codEmpresaTransportadora>0</codEmpresaTransportadora>";*/




  /*$xml = "<despacho>
                    <direccion>cll 65 a # 71 f 16</direccion>
                    <idDepartamento>11</idDepartamento>
                    <idMunicipio>1</idMunicipio>
                    <idCentroPoblado>0</idCentroPoblado>
                    <idLocalidad>10</idLocalidad>
                    <idBarrio>55</idBarrio>
                </despacho>
          <centroSuministro>
                    <idLocal>7</idLocal>
                    <idDepartamento>11</idDepartamento>
                    <idMunicipio>001</idMunicipio>
                    <idCentroPoblado>000</idCentroPoblado>
                    <idLocalidad>015</idLocalidad>
                    <idBarrio>018</idBarrio>
                </centroSuministro>
          <entregaProductos>
                    <lstTipoDespacho>
                            <codigoTipo>2</codigoTipo>
                            <peso>0.6820000</peso>
                    </lstTipoDespacho>
          </entregaProductos>
                <codEmpresaTransportadora>0</codEmpresaTransportadora>";*/

/*$xml="<despacho>
  	<direccion>dfgddfdgfddhdhdfg</direccion>
  	<idDepartamento>11</idDepartamento> 
  	<idMunicipio>1</idMunicipio> 
  	<idCentroPoblado>0</idCentroPoblado> 
  	<idLocalidad>11</idLocalidad> 
  	<idBarrio>0</idBarrio> 
  </despacho>
    <centroSuministro>
         <idLocal>1</idLocal> 
  	 <idDepartamento>11</idDepartamento> 
  	 <idMunicipio>1</idMunicipio> 
       <idCentroPoblado>000</idCentroPoblado> 
  	 <idLocalidad>11</idLocalidad> 
  	 <idBarrio>0</idBarrio> 
   </centroSuministro>
     <entregaProductos>
  	<lstTipoDespacho>
  		<codigoTipo>1</codigoTipo> 
  		<peso>750</peso> 
  	</lstTipoDespacho>
     </entregaProductos>
  <codEmpresaTransportadora>1</codEmpresaTransportadora>";*/

//XML de tienda EASY SOACHA E812 CODIGO LOCAL 10 NO FUNCIONA.
 /*$xml=" <despacho>
        <direccion>KR 1 6 A # 7 8 - 5 5 PI 6</direccion>
        <idDepartamento>11</idDepartamento>
        <idMunicipio>001</idMunicipio>
        <idCentroPoblado>000</idCentroPoblado>
        <idLocalidad>002</idLocalidad>
        <idBarrio>007</idBarrio>
        </despacho>
        <centroSuministro>
        <idLocal>10</idLocal>
        <idDepartamento>11</idDepartamento>
        <idMunicipio>001</idMunicipio>
        <idCentroPoblado>000</idCentroPoblado>
        <idLocalidad>002</idLocalidad>
        <idBarrio>007</idBarrio>
        </centroSuministro>
        <entregaProductos>
        <lstTipoDespacho>
        <codigoTipo>2</codigoTipo>
        <peso>0.03</peso>
        </lstTipoDespacho>
        </entregaProductos>
        <codEmpresaTransportadora>0</codEmpresaTransportadora>";*/

  // XML de tienda EASY NORTE 801 FUNCIONA.
/* $xml=" <despacho>
        <direccion>CL 9 3 # 1 4 - 5 5</direccion>
        <idDepartamento>11</idDepartamento>
        <idMunicipio>001</idMunicipio>
        <idCentroPoblado>000</idCentroPoblado>
        <idLocalidad>002</idLocalidad>
        <idBarrio>035</idBarrio>
        </despacho>
        <centroSuministro>
        <idLocal>3</idLocal>
        <idDepartamento>11</idDepartamento>
        <idMunicipio>001</idMunicipio>
        <idCentroPoblado>000</idCentroPoblado>
        <idLocalidad>002</idLocalidad>
        <idBarrio>035</idBarrio>
        </centroSuministro>
        <entregaProductos>
        <lstTipoDespacho>
        <codigoTipo>2</codigoTipo>
        <peso>0.03</peso>
        </lstTipoDespacho>
        </entregaProductos>
        <codEmpresaTransportadora>0</codEmpresaTransportadora>";*/


  //EASY 4 SUR CODLOCAL 22..NO FUNCIONA

    $xml="<despacho>
      <direccion>CL 9 3 # 1 4 - 5 5</direccion>
      <idDepartamento>11</idDepartamento>
      <idMunicipio>001</idMunicipio>
      <idCentroPoblado>000</idCentroPoblado>
      <idLocalidad>002</idLocalidad>
      <idBarrio>035</idBarrio>
      </despacho>
      <centroSuministro>
      <idLocal>22</idLocal>
      <idDepartamento>11</idDepartamento>
      <idMunicipio>001</idMunicipio>
      <idCentroPoblado>000</idCentroPoblado>
      <idLocalidad>002</idLocalidad>
      <idBarrio>035</idBarrio>
      </centroSuministro>
      <entregaProductos>
      <lstTipoDespacho>
      <codigoTipo>2</codigoTipo>
      <peso>0.05</peso>
      </lstTipoDespacho>
      </entregaProductos>
      <codEmpresaTransportadora>0</codEmpresaTransportadora>";









$service = new Fletes();
$response = $service->calcular($xml);
if ($response) {
	print_r ($response);
}
else {
	print "Error.";
}

?>