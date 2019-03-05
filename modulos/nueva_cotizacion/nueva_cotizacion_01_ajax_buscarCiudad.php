<?php
include_once("../../includes/funciones/database.php");
include_once('../../includes/db_config.php');
include_once("../../includes/funciones/general.php");


tep_db_connect() or die('Unable to connect to database server!');

$id_Dept = $_REQUEST['id_Dept'];
$query = "SELECT  DISTINCT C.DESCRIPTION AS nombre_ciudad, C.ID AS id_ciudad, C.ID_PROVINCE FROM cu_city C 
				  LEFT JOIN cu_department  D ON D.ID = C.ID_DEPARTMENT
				  LEFT JOIN cu_province PR ON PR.ID = C.ID_PROVINCE				  
				  WHERE  C.ID_DEPARTMENT = 	$id_Dept 				
				  ORDER BY C.DESCRIPTION ";		
				  
				  
$resultado = mysql_query($query);
$TotalRow = mysql_num_rows($resultado);
echo '<select name="ciudad" id="ciudad" onChange="recargar(this.id);" class="userinput">';
if($TotalRow==0){
	echo '<option value="--"</option>';
}else{
	while($result = tep_db_fetch_array( $resultado ))
	{
		echo '<option value="'.$result['id_ciudad'] . ',' .  $result['ID_PROVINCE'] .'">'.$result['nombre_ciudad'].'</option>';
	}	
}
echo '</select>';


?>