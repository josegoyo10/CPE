<?
$pag_ini = '../consulta_precios/index.php';
include "../../includes/aplication_top.php";
include_once("../../includes/funciones/generalws.php");
require_once('../../wsposcp/wsposclient.php');
/**********************************************************************************************/


/****************************************************************/

function leearchivo(){
/*$wml_desp="<req>
  <local>E510</local>
  <texto>BARNIZ*pino*extra*OREGON</texto>
</req>";
  <local>E510</local>
  <texto>BARNIZ*pino*extra*OREGON*1/4 G</texto>
</req>";

$wml_desp="<req>
  <local>E6666</local>
  <texto>BARNIZ*pino*extra*OREGON*1/4 G</texto>
</req>";

$wml_desp="<req>
  <local>E6666</local>
  <texto>there</texto>
</req>";

$wml_desp="<req>
  <local></local>
  <texto>BARNIZ*pino*extra*OREGON*1/4 G</texto>
</req>";

$wml_desp="<req>
  <local>0095</local>
  <texto>CHAPA</texto></req>";


$wml_desp="<req>
  <sap>101008</sap>
</req>";
$resultado_desp = busca_sap($wml_desp,$msgerror);
$wml_desp="<req>
  <local>E510</local>
  <texto>500C 18X1520X2420MM</texto></req>";
*/

/*$wml_desp="<req>
  <encabezado>
    <local>E510</local>
    <rut>10906867</rut>
  </encabezado>
  <detalle>
    <precio>3500350</precio>
    <cantidad>12345678</cantidad>
    <ean>2082001013966</ean>
  </detalle>
  <detalle>
    <precio>3500</precio>
    <cantidad>1.00</cantidad>
    <ean>1000110560</ean>
  </detalle>
  <detalle>
    <precio>3500</precio>
    <cantidad>1.00</cantidad>
    <ean>1001001013005</ean>
  </detalle>
</req>";
*/
/*$wml_desp="<req>
  <encabezado>
    <local>E502</local>
    <rut>77104350</rut>
  </encabezado>
  <detalle>
    <precio>00000099</precio>
    <cantidad>12345678.03</cantidad>
    <ean>7805560001084</ean>
  </detalle>
  <detalle>
    <precio>3500</precio>
    <cantidad>1.23</cantidad>
    <ean>2082001013966</ean>
  </detalle>
  <detalle>
    <precio>1500</precio>
    <cantidad>500</cantidad>
    <ean>8851022390615</ean>
  </detalle>
  <detalle>
    <precio>1500</precio>
    <cantidad>500</cantidad>
   <ean>2082000061029</ean>
  </detalle>
  <detalle>
    <precio>1500</precio>
    <cantidad>500</cantidad>
   <ean>2000002177340</ean>
  </detalle>
</req>";*/
//$resultado_desp = envia_nueva_os($wml_desp,$msgerror);

/*
$wml_desp="<req>
  <local>E502</local>
  <texto>LATA*</texto>
</req> ";
*/


$wml_desp="<req>
  <sap>1010100</sap>
</req>";
$resultado_desp = busca_sap($wml_desp,$msgerror);

//$resultado_desp =envia_nueva_os($wml_desp,$msgerror);
writeeventws($resultado_desp);

echo $resultado_desp ;






}

/**********************************************************************************************/
switch($req){
	default:
		leearchivo();
		break;
}

/**********************************************************************************************/

include "../../includes/application_bottom.php";
?>
