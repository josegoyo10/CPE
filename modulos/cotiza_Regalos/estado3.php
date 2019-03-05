<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link href="../estilos.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/JavaScript" src="../popup.js"></script>
</head>
<?php
$pag_ini = '../lista_Regalos/nueva_lista_00.php';

include "../../includes/aplication_top.php";
require_once('../../wsBopos/Bopos.php');
$USR_LOGIN = get_nombre_usr( $ses_usr_id );
$local = get_local_usr( $ses_usr_id );
if(isset($_SESSION['ses_usr_id']))
{
	
} 

else{	
	header("Location: ../../start/sin_perm_01.php");	
}


// *************************************************************************************
if($accion=="visualizar")
{
	?>		
	<SCRIPT language="javascript" type="text/javascript">	
	function cerrar() {
	window.close();
	}
	</SCRIPT>
		
	<body onLoad="setTimeout('cerrar()',120*1000)">
	
	<?php
	
	$service = new Bopos();
	$response = $service->validar($os_codbarras);

	if ($response) {
		//print_r ($response);
	}
	else {
		print "Error de WS.";
	}
	
	
	echo "<table width='450' height='30' border='0' cellpadding='0' cellspacing='0'>";
  	
	echo "<tr>";
    	echo "<td width='390' class='titulonormal'>Detalle de Transacción<td>";
	echo "</tr>";
	
	echo "</table>";
	
	
	
	$Consulta_Local = "SELECT L.cod_local FROM list_os_enc O INNER JOIN locales L ON (O.OS_local = L.id_local) WHERE O.idLista_OS_enc = $id_os";  
    $Resultado_Local = tep_db_query($Consulta_Local);
	$Cod_Local = tep_db_fetch_array($Resultado_Local);	
	
	$Codigo_Local = substr($Cod_Local['cod_local'], 1);	
	$cod_ean_os = calcula_num_os($id_os);			
	$codigo = PREFIJO_LISTA_REGALOS . $Codigo_Local . $cod_ean_os;	
	$dig_verificacion = dvEAN13($codigo);	
	$codigo_ean = $codigo . $dig_verificacion;
	
	$valor = count($response['data']['exce']);		
	
	//echo $response['data']['idCotizacion'], "<br>";
	//echo $codigo, "<br>";
	
	//if($response['data']['idCotizacion'] == $codigo)
	if(true)
	{

		if($valor != 1 && $response['data']['operador'])
		{	
		
		echo "<table width='344' border='1' class='userinput' cellpadding='1' cellspacing='0'>";
		echo "<tr>";	
			echo "<td>Código Operador</td><td>", $response['data']['operador'],'&nbsp;', "</td>";
		echo "</tr>";                         
		
		echo "<tr>";
		    echo "<td>Número Caja</td><td>", $response['data']['caja'],'&nbsp;', "</td>";
		echo "</tr>";                              
		
		echo "<tr>";
		    echo "<td>Número Almacén</td><td>", $response['data']['almacen'],'&nbsp;', "</td>";
		echo "</tr>";
		
		echo "<tr>";
		    echo "<td>Fecha Venta</td><td>", $response['data']['fecExec'],'&nbsp;', "</td>";
		echo "</tr>";
		echo "</table>";
		
	    echo "<br>";    
	    $tamano =  count($response['data']['productos']['producto']);
	            
			echo "<table width='344' border='1' class='userinput' cellpadding='1' cellspacing='0'>";
			  echo "<tr>";
			    echo "<td>C&oacute;digo Sap </td>";
			    echo "<td>Cantidad</td>";
			    echo "<td>Valor</td>";
			  echo "</tr>";
			  
			  for($i = 0; $i < $tamano; $i++ )
			  {
			  echo "<tr>";
			    echo "<td>" . $response['data']['productos']['producto'][$i]['codSap'] . "</td>";
			    echo "<td>" . $response['data']['productos']['producto'][$i]['cantidadCompra'] . "</td>";
			    echo "<td>" . $response['data']['productos']['producto'][$i]['valorProd'] . "</td>";
			  echo "</tr>";
			  }
			echo "</table>";
	    
		echo "<br>"; 
		
		echo "<table width='344' border='1' class='userinput' cellpadding='1' cellspacing='0'>";
		echo "<tr>";
		echo "<td>Total</td><td>", $response['data']['montoTotalTrans'],'&nbsp;', "</td>";	
		echo "</tr>";
		
		echo "<tr>";
		echo "<td>No. Identificación Cliente:</td><td>", $response['data']['idCliente'],'&nbsp;', "</td>";
		echo "</tr>";
	
		echo "<tr>";
	    echo "<td>No. de Factura:</td><td>", $response['data']['numFactura'],'&nbsp;', "</td>";
		echo "</tr>";
		
		echo "<tr>";
	    echo "<td>No. de Cotización:</td><td>", $response['data']['idCotizacion'],'&nbsp;', "</td>";
		echo "</tr>";
		
		
		
		echo "</table>";
		
		echo "<br>"; 
		
	    
	        
		$os_fechaboleta = substr($response['data']['fecExec'], 0, 10);	
		
		echo "<FORM NAME='estado' METHOD='POST' ACTION='estado3.php' onSubmit='' >";
		echo "<input type='hidden' name='clie_rut' value='$clie_rut'>";
		echo "<input type='hidden' name='id_os' value='$id_os'>";
		echo "<input type='hidden' name='id_estado' value='$id_estado'>";
	    echo "<input type='hidden' name='accion' value='update'>";
	    
	    echo "<input type='hidden' name='os_numboleta' value='" . $response['data']['numFactura'] . "'>";
	    echo "<input type='hidden' name='os_fechaboleta' value='" . $os_fechaboleta . "'>";
	    echo "<input type='hidden' name='os_terminal_pos' value='" . $response['data']['caja'] . "'>";
	    echo "<input type='hidden' name='os_codbarras' value='$codigo_ean'>";
	    
	 	echo "<table width='380'  border='0' align='center' cellpadding='1' cellspacing='0' class='textonormal'>";
	    echo "<tr>";
	 	echo "<td colspan='2'><input type='submit' name='Button' value='Pagar Cotización' >"; 
	    	echo "<input type='button' name='Button' value='Cancelar' onClick='window.close();'></td>";
	        echo "</tr>";
		  	echo "</table>";
		  
		echo "</form>";
		
		}
		else{
			

	
			echo "<table width='344' border='0' class='userinput' cellpadding='1' cellspacing='0'>";
				echo "<tr>";
			echo "<td>NO SE PUDO RECUPERAR LA INFORMACIÓN DE LA COTIZACIÓN", "</td>";			
				echo "</tr>";
			echo "</table>";
			
			echo "<br>";
				
			echo "<table width='380'  border='0' align='center' cellpadding='2' cellspacing='2' class='textonormal'>";
			
			echo "<tr>";
				echo "<input type='button' name='Button' value='Cerrar' onClick='window.close();'></td>";
	        echo "</tr>";
		  	
	        echo "</table>";
		  	
		}	
	
	}
	else{
		
		
		echo "<table width='344' border='0' class='userinput' cellpadding='1' cellspacing='0'>";
				echo "<tr>";
			echo "<td>EL NÚMERO DE COTIZACIÓN NO COINCIDE, VERIFIQUE LOS DATOS DE LA COTIZACIÓN", "</td>";			
				echo "</tr>";
			echo "</table>";
			
			echo "<br>";
				
			echo "<table width='380'  border='0' align='center' cellpadding='2' cellspacing='2' class='textonormal'>";
			
			echo "<tr>";
				echo "<input type='button' name='Button' value='Cerrar' onClick='window.close();'></td>";
	        echo "</tr>";
		  	
	        echo "</table>";
	        	        
	        
	}
    
     
 
	
}



// *************************************************************************************
if($accion=="update"){	      
	/*	
	echo "clie_rut: ", $clie_rut, "<br>";
	echo "id_os: ", $id_os, "<br>";
	echo "id_estado: ", $id_estado, "<br><br>";
	echo "os_numboleta: ", $os_numboleta, "<br>";
	echo "os_fechaboleta: ", $os_fechaboleta, "<br>";
	echo "os_terminal_pos: ", $os_terminal_pos, "<br>";
	echo "os_codbarras: ", $os_codbarras, "<br>";
	echo "Local: ",$local,"<br>";
	*/
	
	/* para sacar el nombre del estado de la cotizacion*/
	$querysel ="SELECT OS_estado, idLista_enc FROM list_os_enc WHERE idLista_OS_enc=".($id_os+0);
    $estado= tep_db_query($querysel);
    $res_estado = tep_db_fetch_array( $estado );

	//Esta acción debe ser transaccional
	tep_db_query("SET AUTOCOMMIT=0");

	$success = true;
	//Se marca la OS como pagada
	
	$queryUP ="UPDATE list_os_enc SET num_boleta = '" . $os_numboleta . "', OS_estado='SP', OS_fecPago='". $os_fechaboleta ."' WHERE idLista_OS_enc=".($id_os+0)." ";	
	$success = $success && tep_db_query($queryUP);
	
	$queryAC ="UPDATE list_regalos_enc SET id_Estado = 'CC' WHERE idLista=".($res_estado['idLista_enc']+0)." ";
	$success = $success && tep_db_query($queryAC);
	
	insertahistorial_ListaReg("Se ha epagado la Cotización No.".$id_os.",con número de boleta ".$os_numboleta.", correspondiente a la  Lista de Regalo N°.".$res_estado['idLista_enc'].".", $USR_LOGIN, null, $res_estado['idLista_enc'], $id_os, $tipo = 'SYS');
	
	//Se genera las OT a partir de la OS
	$querysel = "SELECT LD.idLista_OS_det, RD.list_tipoprod, LD.OS_cantidad, RE.id_Local AS origLista, LE.OS_local AS origCotiza  
				 FROM list_os_det LD 
				 LEFT JOIN list_regalos_det RD ON RD.idlista_det = LD.idLista_det
				 LEFT JOIN list_regalos_enc RE ON RE.idLista = RD.idLista_enc
				 LEFT JOIN list_os_enc LE ON (LE.idLista_OS_enc = LD.idLista_OS_enc) 
				 WHERE LD.idLista_OS_enc=".($id_os+0)."
				 ORDER BY RD.list_tipoprod,LD.idLista_OS_det; ";
    $rq= tep_db_query($querysel);
	$grupo_tdesp = 0;

    while (($res_1 = tep_db_fetch_array( $rq )) && $success) {
		//Por cada registro generamos una OT
    		if ($res_1['list_tipoprod'] == 'PE') {
    			if($res_1['origLista'] == $res_1['origCotiza']){
					$querytrx ="INSERT INTO `list_ot` (`ot_idEstado`, `ot_listTipo`,`ot_listFeccrea`,`ot_listNumImp`,`ot_listTipopago`, `ot_listTiendaPago`) VALUES 
	 						    ('EC', '".$res_1['list_tipoprod']."', now(), '0', '1', '".$local."');";
					$success = $success && tep_db_query($querytrx);
					$last_id = tep_db_insert_id( '' );
    			}
    			else{
    				$querytrx ="INSERT INTO `list_ot` (`ot_idEstado`, `ot_listTipo`,`ot_listFeccrea`,`ot_listNumImp`,`ot_listTipopago`, `ot_listTiendaPago`) VALUES 
	 						    ('PT', '".$res_1['list_tipoprod']."', now(), '0', '1', '".$local."');";
					$success = $success && tep_db_query($querytrx);
					$last_id = tep_db_insert_id( '' );
    			}
				
				insertahistorial("Se creó OT $last_id de tipo PS a partir de OS $id_os", $id_os, $last_id);

				$querytrx ="UPDATE list_os_det SET OS_idOT = ". ($last_id+0) .", OS_cantPick='0' WHERE idLista_OS_det = " . ($res_1['idLista_OS_det']+0);
				$success = $success && tep_db_query($querytrx);
	
				$grupo_tdesp = 0;
    		}
			
    		if ($res_1['list_tipoprod'] == 'SV') {
				$querytrx ="INSERT INTO `list_ot` (`ot_idEstado`, `ot_listTipo`,`ot_listFeccrea`,`ot_listNumImp`,`ot_listTipopago`, `ot_listTiendaPago`) VALUES 
 						    ('VC', '".$res_1['list_tipoprod']."', now(), '0', '1', '".$local."');";
				$success = $success && tep_db_query($querytrx);
				$last_id = tep_db_insert_id( '' );
				insertahistorial("Se creó OT $last_id de tipo SV Seguro a partir de OS $id_os", $id_os, $last_id);
	
				$querytrx ="UPDATE list_os_det SET OS_idOT = ". ($last_id+0) .", OS_cantPick='0' WHERE idLista_OS_det = " . ($res_1['idLista_OS_det']+0);
				$success = $success && tep_db_query($querytrx);
	
				$grupo_tdesp = 0;
			}

			if ($res_1['list_tipoprod'] == 'PS') {
				if($grupo_tdesp != 1) {
					if($res_1['origLista'] == $res_1['origCotiza']){
						$querytrx ="INSERT INTO `list_ot` (`ot_idEstado`, `ot_listTipo`,`ot_listFeccrea`,`ot_listNumImp`,`ot_listTipopago`, `ot_listTiendaPago`) VALUES 
		 						('PC', '".$res_1['list_tipoprod']."', now(), '0', '1', '".$local."');";
						$success = $success && tep_db_query($querytrx);
						$last_id = tep_db_insert_id( '' );
						insertahistorial("Se creó OT $last_id de tipo PE a partir de OS $id_os", $id_os, $last_id);
					}
					else{
						$querytrx ="INSERT INTO `list_ot` (`ot_idEstado`, `ot_listTipo`,`ot_listFeccrea`,`ot_listNumImp`,`ot_listTipopago`, `ot_listTiendaPago`) VALUES 
		 						('PT', '".$res_1['list_tipoprod']."', now(), '0', '1', '".$local."');";
						$success = $success && tep_db_query($querytrx);
						$last_id = tep_db_insert_id( '' );
						insertahistorial("Se creó OT $last_id de tipo PE a partir de OS $id_os", $id_os, $last_id);
					}
				}
					
				$querytrx ="UPDATE list_os_det SET OS_idOT = ". ($last_id+0) .", OS_cantPick='0' WHERE idLista_OS_det = " . ($res_1['idLista_OS_det']+0);
				$success = $success && tep_db_query($querytrx);
	
				$grupo_tdesp = 1;
			}
	}
	
	//Fin de la transacción
	if ($success)
		tep_db_query("commit");
	else
		tep_db_query("rollback");

	tep_db_query("SET AUTOCOMMIT=1");
	?>
	<script language="javascript" type="text/javascript" >		
		window.close();
	</script>
	<?php
	tep_exit();
}
/***** Fin Acciones *****/



if($accion != "visualizar")
{
	$MiTemplate = new Template();
	// asignamos degug maximo
	$MiTemplate->debug = 0;
	// root directory de los templates
	$MiTemplate->set_root(DIRTEMPLATES);
	// variables perdidas
	$MiTemplate->set_unknowns("remove");
	
	/* para sacar el nombre del estado de la cotizacion*/
	$querysel ="select esta_nombre,id_estado from estados where id_estado='$id_estado'";
	    $estano= tep_db_query($querysel);
	    $estano = tep_db_fetch_array( $estano );
	
	/*****************************/
	
	$MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
	$MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);
	$MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));
	//$MiTemplate->set_var("TEXT",ver_contenido_editable( 1 ));
	
	
	// Agregamos el main
	$MiTemplate->set_file("main","cotiza_Regalos/estado3.htm");
	
	$MiTemplate->set_var("clie_rut",$clie_rut);
	$MiTemplate->set_var("accion",$accion);
	$MiTemplate->set_var("id_os",$id_os);
	$MiTemplate->set_var("id_estado",$id_estado);
	$MiTemplate->set_var("esta_nombre",$estano['esta_nombre']);
	
	$MiTemplate->pparse("OUT_H", array("header"), true);
	$MiTemplate->parse("OUT_M", array("main","footer"), true);
	$MiTemplate->p("OUT_M");
	
	
	include "../../includes/application_bottom.php";

}

?>