<?php
include_once("../../includes/funciones/database.php");
include_once('../../includes/db_config.php');
include_once("../../includes/funciones/general.php");


tep_db_connect() or die('Unable to connect to database server!');

$id_os2 = $_REQUEST['id_os2'];

$query = "SELECT  COUNT(osde_subtipoprod) AS valor  FROM  os_detalle 
		  WHERE id_os = $id_os2 AND osde_subtipoprod  <> 'DE' ";
  
$resultado = mysql_query($query);
$numero = tep_db_fetch_array($resultado);

if($numero['valor'] >= 1)
{
	echo "1";
}
else{
	echo "2";
}



	if($numero['valor'] >= 1)
	{
		$query = "SELECT  COUNT(id_tipodespacho) AS valor  FROM  os_detalle 
				  WHERE id_os = $id_os2 AND  id_tipodespacho IN (1,2)  AND osde_tipoprod IN ('PS', 'PE' ) ";  
		$resultado = mysql_query($query);
		$valor = tep_db_fetch_array($resultado);
		
		
		if($valor['valor'] == 0)
		{			
			$query = "SELECT  COUNT(id_tipodespacho) AS valor  FROM  os_detalle 
			WHERE id_os = $id_os2 AND osde_subtipoprod = 'DE'";
			$resultado = mysql_query($query);
			$num = tep_db_fetch_array($resultado);

			if($num['valor'] == 0)			
				echo "1";
			else
				echo "3";
		}
		else{			
			echo "1";
		}
	}
	
?>