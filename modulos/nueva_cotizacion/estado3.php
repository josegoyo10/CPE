<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<link href="../nueva_cotizacion/estilos.css" rel="stylesheet" type="text/css">

<?php
$pag_ini = '../nueva_cotizacion/nueva_cotizacion_00.php';

//$SIN_PER = 1;
include "../../includes/aplication_top.php";
require_once('../../wsBopos/Bopos.php');


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
	
	
	$Consulta_Local = "SELECT cod_local FROM os inner join locales ON os.id_local = locales.id_local WHERE id_os = $id_os";
     
    $Resultado_Local = tep_db_query($Consulta_Local);
	$Cod_Local = tep_db_fetch_array($Resultado_Local);	
	$Codigo_Local = substr($Cod_Local['cod_local'], 1);	
	$cod_ean_os = calcula_num_os($id_os);		
	$codigo = PREFIJO_CENTRO_PROYECTOS . $Codigo_Local . $cod_ean_os;	
	$dig_verificacion = dvEAN13($codigo);	
	$codigo_ean = $codigo . $dig_verificacion;
		
	$valor = count($response['data']['exce']);		
	
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
	*/
	
	/* para sacar el nombre del estado de la cotizacion*/
	$querysel ="select id_estado from os where id_os=".($id_os+0);
    $estado= tep_db_query($querysel);
    $res_estado = tep_db_fetch_array( $estado );
	
    
    
	//Esta acción debe ser transaccional
	tep_db_query("SET AUTOCOMMIT=0");

	$success = true;
	//Se marca la OS como pagada
	
	$queryUP ="UPDATE os SET os_numboleta = '" . $os_numboleta . "', id_estado='SP',os_fechaboleta='". $os_fechaboleta ."', os_terminal_pos = $os_terminal_pos,  os_codbarras = $os_codbarras  WHERE id_os=".($id_os+0)." and clie_rut= ".($clie_rut+0)."";	
	$success = $success && tep_db_query($queryUP);
	insertahistorial("Cotización ha sido Pagada Manualmente, Número Boleta: $os_numboleta, Fecha Boleta; $os_fechaboleta");
	
	
	//Se genera las OT a partir de la OS
	$querysel = "
				SELECT os_detalle.*, os.id_local
				FROM os_detalle LEFT JOIN os on os_detalle.id_os = os.id_os 
				WHERE os.id_os = ".($id_os+0)."
				ORDER BY osde_tipoprod, id_tipodespacho";
    $rq= tep_db_query($querysel);

	$grupo_tdesp = 0;
	
	
    while (($res_1 = tep_db_fetch_array( $rq )) && $success) {
		//Por cada registro generamos una OT

		if ($res_1['osde_tipoprod'] == 'PE') {
			$querytrx ="INSERT INTO ot (id_estado, id_os,		   id_tipodespacho,					ot_tipo, ot_fechacreacion, ot_freactivacion) 
						VALUES		   ('EC',	   ".($id_os+0).", ".$res_1['id_tipodespacho'].", 'PE',	now(), now()) ";
			$success = $success && tep_db_query($querytrx);
			$last_id = tep_db_insert_id( '' );
			insertahistorial("Se creó OT $last_id de tipo PE a partir de OS $id_os", $id_os, $last_id);

			$querytrx ="UPDATE os_detalle SET ot_id = ". ($last_id+0) ." WHERE id_os_detalle = " . ($res_1['id_os_detalle']+0);
			$success = $success && tep_db_query($querytrx);

			$grupo_tdesp = 0;
		}
		if ($res_1['osde_tipoprod'] == 'SV' && $res_1['osde_subtipoprod'] != 'DE' && $res_1['osde_subtipoprod'] != 'AR' && $res_1['osde_subtipoprod'] != 'SE') {
			$querytrx ="INSERT INTO ot (id_estado, id_os,		   id_tipodespacho,					ot_tipo, ot_fechacreacion, ot_freactivacion) 
						VALUES		   ('VC',	   ".($id_os+0).", ".$res_1['id_tipodespacho'].", 'SV',	now(), now()) ";
			$success = $success && tep_db_query($querytrx);
			$last_id = tep_db_insert_id( '' );
			insertahistorial("Se creó OT $last_id de tipo SV a partir de OS $id_os", $id_os, $last_id);

			$querytrx ="UPDATE os_detalle SET ot_id = ". ($last_id+0) ." WHERE id_os_detalle = " . ($res_1['id_os_detalle']+0);
			$success = $success && tep_db_query($querytrx);

			$grupo_tdesp = 0;
		}
		if ($res_1['osde_tipoprod'] == 'SV' && $res_1['osde_subtipoprod'] == 'AR') {
			$querytrx ="INSERT INTO ot (id_estado, id_os,		   id_tipodespacho,					ot_tipo, ot_fechacreacion, ot_freactivacion) 
						VALUES		   ('AP',	   ".($id_os+0).", ".$res_1['id_tipodespacho'].", 'SV',	now(), now()) ";
			$success = $success && tep_db_query($querytrx);
			$last_id = tep_db_insert_id( '' );
			insertahistorial("Se creó OT $last_id de tipo SV Arriendo a partir de OS $id_os", $id_os, $last_id);

			$querytrx ="UPDATE os_detalle SET ot_id = ". ($last_id+0) ." WHERE id_os_detalle = " . ($res_1['id_os_detalle']+0);
			$success = $success && tep_db_query($querytrx);

			$grupo_tdesp = 0;
		}

		if ($res_1['osde_tipoprod'] == 'SV' && $res_1['osde_subtipoprod'] == 'SE') {
			$querytrx ="INSERT INTO ot (id_estado, id_os,		   id_tipodespacho,					ot_tipo, ot_fechacreacion, ot_freactivacion) 
						VALUES		   ('SG',	   ".($id_os+0).", ".$res_1['id_tipodespacho'].", 'SV',	now(), now()) ";
			$success = $success && tep_db_query($querytrx);
			$last_id = tep_db_insert_id( '' );
			insertahistorial("Se creó OT $last_id de tipo SV Seguro a partir de OS $id_os", $id_os, $last_id);

			$querytrx ="UPDATE os_detalle SET ot_id = ". ($last_id+0) ." WHERE id_os_detalle = " . ($res_1['id_os_detalle']+0);
			$success = $success && tep_db_query($querytrx);

			$grupo_tdesp = 0;
		}
		if ($res_1['osde_tipoprod'] == 'PS') {
			if($grupo_tdesp != $res_1['id_tipodespacho']) {
				$querytrx ="INSERT INTO ot (id_estado, id_os,		   ot_tipo, ot_fechacreacion, ot_freactivacion, id_tipodespacho) 
							VALUES		   ('PC',	   ".($id_os+0).", 'PS',	now(),			  now(),			".($res_1['id_tipodespacho']+0).") ";
				$success = $success && tep_db_query($querytrx);
				$last_id = tep_db_insert_id( '' );
				insertahistorial("Se creó OT $last_id de tipo PS a partir de OS $id_os", $id_os, $last_id);
			}

			$querytrx ="UPDATE os_detalle SET ot_id = ". ($last_id+0) ." WHERE id_os_detalle = " . ($res_1['id_os_detalle']+0);
			$success = $success && tep_db_query($querytrx);

			$grupo_tdesp = $res_1['id_tipodespacho'];
		}

		//Generamos la rebaja de Stock para el producto del listado sólo si está marcado en la tabla tipo_subtipo
		$query_2="SELECT valid_stock FROM productos p join tipo_subtipo t on t.prod_tipo = p.prod_tipo and t.prod_subtipo = p.prod_subtipo WHERE id_producto = " . ($res_1['id_producto']+0) ; 
		$tdq_2 = tep_db_query($query_2);
		$res_2 = tep_db_fetch_array( $tdq_2 );
		if ($res_2['valid_stock']) {
			$querytrx ="UPDATE productos SET stock_proveedor = stock_proveedor - " . ($res_1['osde_cantidad']+0) . " WHERE id_producto = " . ($res_1['id_producto']+0);
			$success = $success && tep_db_query($querytrx);

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
		opener.location.reload();
		window.close();
	</script>
	<?php
	tep_exit();
}

if($accion=="genera_Pago"){	
	insertahistorial("Se ha realizado el calculo del String de Pago Manualmente", $id_os, 0, "SYS");
	if($id_estado == 'SE')
	{
		$val_Tienda = valueTienda($id_os);
		$val_Terminal = valueTerminal($Terminal);
		$val_Transac = valueTransaccion($Transac);
		$val_F_pago = valueFpago($F_pago);
	
		$string_Pago = value_stringPago($val_F_pago, $val_Tienda, $val_Terminal, $val_Transac);
	
		$accion == "visualizar";
		$os_codbarras = $string_Pago;
		
		?>
	    <script language="JavaScript">
			location.href='estado3.php?id_estado=<?=$id_estado?>&id_os=<?=$id_os?>&clie_rut=<?=$clie_rut?>&os_codbarras=<?=$os_codbarras?>&accion=visualizar';
		</script>
	    <?php
	}	
}
/***** Fin Acciones *****/

/**** Funciones para Validar la Construccion del String de pago ****/
function valueTerminal($Terminal){
	 $tam = strlen($Terminal);
	 
	 if($tam != 3){
	 	return CompletaCerosI($Terminal, 3);
	 }	
	 else
	 {
	 	return $Terminal;
	 }
}

function valueTransaccion($Transac){
	$tam = strlen($Transac);
	 
	 if($tam != 4){
	 	return CompletaCerosI($Transac, 4);
	 }	
	 else
	 {
	 	return $Transac;
	 }
}

function  valueTienda($id_os){
	$qry="SELECT L.cod_local_pos FROM os O LEFT JOIN locales L ON (O.id_local = L.id_local) WHERE O.id_os='".$id_os."';" ; 
	$rq = tep_db_query($qry);
	$res = tep_db_fetch_array( $rq );
	
	$tam = strlen($res['cod_local_pos']);
	if($tam != 4){
	 	return CompletaCerosI($res['cod_local_pos'], 4);
	 }	
	 else
	 {
	 	return $res['cod_local_pos'];
	 }
}

function value_stringPago($F_pago, $val_Tienda, $val_Terminal, $val_Transac){
	$tam = ($val_Tienda."".$val_Terminal."".$val_Transac."".$F_pago);
	
	 return CompletaCerosD($tam, 32);
}

function valueFpago($F_pago){
	$sp_fecha = split("/", $F_pago);
    $fecha = $sp_fecha[2]."".$sp_fecha[1]."".$sp_fecha[0];
    
    return $fecha;
}


/******* Ingreso sin Accion *******/
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
	$MiTemplate->set_file("main","nueva_cotizacion/estado3.htm");
	
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