<?php
function consulta_VistaMSSQL($ARR){
$db = mssql_connect("172.20.4.92:1433","appweb","4p2r$1ppw2b3279", true) or die ("No conecta con SQLSERVER");
$d=mssql_select_db("BDFlejes", $db); 

$sql = "Select prd_sap From LNBlanca";

$res = mssql_query($sql); 
$ARR = array();

	while ($row = mssql_fetch_array($res)) {
		$ARR[] = "'".$row[0]."',";
	}

return($ARR);
mssql_close(); 
}

function consulta_VistaMSSQL_Proveedor($ARR){
$db = mssql_connect("172.20.4.92:1433","appweb","4p2r$1ppw2b3279", true) or die ("No conecta con SQLSERVER");
$d=mssql_select_db("DBFlejesProd", $db); 

$sql = "SELECT DISTINCT prov_nombre, prov_rut FROM LNBlanca ORDER BY prov_nombre";

$res = mssql_query($sql); 
$ARR = array();

	while ($row = mssql_fetch_array($res)) {
		$ARR[] = "'".$row[1]."',";
	}

return($ARR);
mssql_close();  
}
?>



