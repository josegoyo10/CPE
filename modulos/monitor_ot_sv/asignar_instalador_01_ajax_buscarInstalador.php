<?php
include_once("../../includes/funciones/database.php");
include_once('../../includes/db_config.php');
include_once("../../includes/funciones/general.php");


tep_db_connect() or die('Unable to connect to database server!');

$id_Emp = $_REQUEST['id_Emp'];
$query = "SELECT  distinct i.id_instalador, i.inst_rut,i.inst_nombre, i.inst_paterno, i.inst_materno, i.inst_telefono, i.direccion, i.email
		  FROM  instaladores i WHERE id_empresa_instaladora =  $id_Emp ";
  
				  
$resultado = mysql_query($query);
$TotalRow = mysql_num_rows($resultado);
echo '<select name="select_inst" id="select_inst"  >';
if($TotalRow==0){
	echo '<option value="" >No se Encontro Instalador</option>';
}else{
	while($result = tep_db_fetch_array( $resultado ))
	{
		echo '<option  value="' .$result['id_instalador'] . '">' . $result['inst_rut'] . '&nbsp;&nbsp;' . $result['inst_nombre'] . '&nbsp;' . $result['inst_paterno']  . '</option>';
	}	
}
echo '</select>';


?>