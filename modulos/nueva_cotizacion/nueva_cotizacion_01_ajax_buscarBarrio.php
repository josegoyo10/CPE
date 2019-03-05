<?php
include_once("../../includes/funciones/database.php");
include_once('../../includes/db_config.php');
include_once("../../includes/funciones/general.php");


tep_db_connect() or die('Unable to connect to database server!');

$arreglo =  split(',', $_REQUEST['id_ciudad']);

$id_ciudad = $arreglo[0];
$id_provincia = $arreglo[1];
$id_dept = $_REQUEST['id_dept'];


$query = "SELECT  DISTINCT N.LOCATION  AS id_comuna,  CONCAT( N.DESCRIPTION , ' - ',  L.DESCRIPTION) AS comu_nombre FROM cu_neighborhood N
				  LEFT JOIN cu_locality L ON L.ID = N.ID_LOCALITY 
				  AND L.ID_DEPARTMENT = $id_dept AND L.ID_CITY = $id_ciudad  
				  AND L.ID_PROVINCE = $id_provincia			  
				  LEFT JOIN cu_province PR ON PR.ID = N.ID_PROVINCE 
				  WHERE N.ID_DEPARTMENT = $id_dept 
				  AND N.ID_CITY = $id_ciudad 
				  AND N.ID_PROVINCE = $id_provincia  
		 		  ORDER BY  N.DESCRIPTION  ";		 		  		 		
		 		  
$resultado = mysql_query($query);
$TotalRow = mysql_num_rows($resultado);
echo '<select name="barrios" id="barrios" class="userinput" >';
if($TotalRow==0){
	echo '<option value="--"</option>';
}else{
	while($result = tep_db_fetch_array( $resultado ))
	{
		echo '<option value="'.$result['id_comuna'].'">'.$result['comu_nombre'].'</option>';
	}	
}
echo '</select>';
?>