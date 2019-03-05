<?php
// Esta página sólo se ejecuta como CGI
$SIN_PER = 1;
include "../../includes/aplication_top.php";
$Crut = $_REQUEST['Crut'];

$dirServ = consulta_localizacion($Crut,1);
$dirServicio = getlocalizacion($dirServ);

$Mquery = "SELECT CONCAT(C.clie_nombre,' ',C.clie_paterno,' ',C.clie_materno) AS nombre, D.dire_direccion AS Cdireccion, D.dire_telefono AS Ctelefono
			FROM direcciones D
			JOIN clientes C ON C.clie_rut = D.clie_rut
		   where D.clie_rut=".$Crut." and D.dire_defecto='p';";
writelog($Mquery);
$Mrq = tep_db_query($Mquery);

if($Mres = tep_db_fetch_array( $Mrq )){
	$dire_localizacion = $Mres['dire_localizacion'];
    echo $Mres["nombre"] . "|" . $Mres["Cdireccion"] . "|" . $Mres["Ctelefono"] . "|" . $dirServicio["barrio"]. "|" . $dirServicio["departamento"]. "|" . $dirServicio["ciudad"]. "|" . $dirServicio["localidad"]. "|" . $Mres["id_barrio"]. "|" . $dirServ;
	}
else
    echo "No Encontrado";

?>