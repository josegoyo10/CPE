<?php
$pag_ini = '../nueva_cotizacion/nueva_cotizacion_00.php';
include "../../includes/aplication_top.php";
include "activewidgets.php";

// include_idioma_mod( $ses_idioma, "nueva_cotizacion_01" );

// *************************************************************************************
$usr_local = get_local_usr( $ses_usr_id );

	/*
	* Implementación de cambiar todos los tipos de entrega de los productos  
	* de una catización, a un mismo tipo de entrega.
	*/

	$cons_flete = "DELETE  FROM os_detalle WHERE id_os = $id_os AND osde_tipoprod = 'SV'  AND osde_subtipoprod = 'DE' ";
	$resultado = tep_db_query($cons_flete);
  
if ($accion == "entrega") {


	if (!$id_os && $clie_rut) 
		$id_os = $clie_rut*-1;
	
	$tipo_entrega = $_POST['tipo_entrega'];
	if($tipo_entrega == 4){
		$fecha = date('d/m/Y');
		$consulta = "UPDATE os_detalle  SET id_tipodespacho = $tipo_entrega, osde_fecha_entrega = '$fecha'  WHERE id_os = $id_os AND osde_tipoprod <> 'PE' AND osde_tipoprod <> 'SV' ";
		$resultado = tep_db_query($consulta);
	}
	else{
		if($tipo_entrega == 2)
		$consulta = "UPDATE os_detalle  SET id_tipodespacho = $tipo_entrega, osde_fecha_entrega = '' WHERE id_os = $id_os AND osde_tipoprod <> 'PE' AND osde_tipoprod <> 'SV' ";
		else
		$consulta = "UPDATE os_detalle  SET id_tipodespacho = $tipo_entrega, osde_fecha_entrega = '' WHERE id_os = $id_os AND osde_tipoprod <> 'SV' ";
		
		$resultado = tep_db_query($consulta);
	}	
}


/** Inicio Acciones CGI **/
if ($accion == "relprice") {
	

	//Se actualizan los precios del listado
	if (!$id_os && $clie_rut) 
		$id_os = $clie_rut*-1;

	$query = "	SELECT id_local, id_estado FROM os WHERE id_os = ".($id_os+0);
	$rq_1 = tep_db_query($query);
	$res_1 = tep_db_fetch_array($rq_1);	
	

	if ($res_1['id_estado']=="SA") { //Sólo se actualiza precios si está en estado abierto
		$query_2 = "SELECT od.id_os_detalle, od.id_producto, osde_precio precant, prec_valor precnue, osde_preciocosto costoant, prec_costo costonue, osde_fecha_entrega 
					FROM os_detalle od
					  LEFT JOIN productos pr on od.id_producto = pr.id_producto
					  LEFT JOIN precios p on pr.cod_prod1 = p.cod_prod1 AND p.id_local = ".(get_local_usr( $ses_usr_id )+0)."
					WHERE od.id_os = $id_os AND p.estadoactivo = 'C' AND prod_subtipo <> 'DE' ";
        $rq_2 = tep_db_query($query_2);
        while( $res_2 = tep_db_fetch_array( $rq_2 ) ) {
			$queryup =  "UPDATE os_detalle SET osde_precio = " . (strlen($res_2['precnue'])?$res_2['precnue']:'null') . ", osde_preciocosto = " . (strlen($res_2['costonue'])?$res_2['costonue']:'null') . " WHERE id_os = ".($id_os+0)." and id_os_detalle = ".($res_2['id_os_detalle']+0);
			tep_db_query($queryup);
			
		}

		// Se actualiza la info del local
		if ($res_1['id_local'] != get_local_usr( $ses_usr_id )) {
			$queryup =  "UPDATE os SET id_local = " . (get_local_usr( $ses_usr_id )+0) . ", usuario = '" . get_login_usr( $ses_usr_id ) . "', USR_ID = " . ($ses_usr_id+0) . " WHERE id_os = ".($id_os+0);
			tep_db_query($queryup);
		}

        /* saca nombre del estado para poner en el trackin*/
        $querN="select nom_local from locales where id_local = " . (get_local_usr( $ses_usr_id )+0);
        $estaN = tep_db_query($querN);
        $estaN = tep_db_fetch_array( $estaN );
        $esta_nombre=$estaN['nom_local'];

		insertahistorial("Se actualizó la información de la OS $id_os para el local $esta_nombre");

		//Se actualiza la info de la fecha de cotización
		$queryup =  "UPDATE os SET os_fechacotizacion = now(), os_fechaestimacion = DATE_ADD(now(), INTERVAL ".(DIAS_VALID_COT + 0)." DAY) WHERE id_os = ".($id_os+0);
		tep_db_query($queryup);
	}
}

// Si la acción es 'Nuevo Producto' y no viene acción accion secundaria
if ($accion == "np" && !$accionsec) {
	$os_fechaestimada = '';
	
	//Si no hay id_os, se crea la id_os = 0
	if (!$id_os && $clie_rut) {
		//Si no existe la os con el rut del cliente (*-1), entonces se crea una nueva 
		$query = "SELECT id_os FROM os WHERE id_os = " . (($clie_rut*-1)+0);
		if (!tep_db_num_rows(tep_db_query($query))) {
			//Se crea el registro con el RUT del cliente como id_os pero con signo negativo
			$queryinsert =  " INSERT INTO os (id_os,id_estado,id_local,							  id_direccion,	  clie_rut,	  usuario,							  USR_ID,id_proyecto, origen,USR_ORIGEN) 
							  SELECT		  -(c.clie_rut), 'SA',	".(get_local_usr( $ses_usr_id )+0).", d.id_direccion, c.clie_rut, '".get_login_usr( $ses_usr_id )."', ".($ses_usr_id+0).",1, 'C'  ,'".get_login_origen( $ses_usr_id )."'
							  FROM clientes c left join direcciones d on d.clie_rut = c.clie_rut 
							  WHERE c.clie_rut = " . ($clie_rut+0) . " and d.dire_defecto = 'P'";

			tep_db_query($queryinsert);
		}
		//se reasigna el id_os con el RUT * -1
		$id_os = $clie_rut*-1;
	}

	//Verificamos el tipo decimal
	$my_textfield_codbarra = $textfield_codbarra +0;
	$query="SELECT ind_decimal, c.unid_med 
			FROM codbarra c 
			JOIN unidmed u on c.unid_med = u.unid_med 
			WHERE cod_barra = '$my_textfield_codbarra'"; 
	$tdq_1 = tep_db_query($query);
	$res_2 = tep_db_fetch_array( $tdq_1 );

// Si el tipo de despacho es para un PE o PS, evalua la fecha asignada para la entrega según el tipo de despacho.
	if( ($textfield_tipoprod == 'PS' || $textfield_tipoprod == 'PE') && (($select_despacho == '5' || $select_despacho == '6')) )
	{
		$fecha = date('d/m/Y');
		$os_fechaestimada = suma_fechas($fecha, DIAS_PROD_PE);

		$consulta_fecha = "SELECT numero_dias FROM fecha_entrega WHERE cod_producto = $textfield_idprod";
		$resultado_fecha = tep_db_query($consulta_fecha);
		$numero_dias = tep_db_fetch_array($resultado_fecha);		
			if($numero_dias[0] == ''){	
				$os_fechaestimada = suma_fechas($fecha, DIAS_PROD_PE);
			}	
			else{
				$os_fechaestimada = suma_fechas($fecha, $numero_dias[0]);
			}
	}
// Si el tipo de Producto es PE aplica la fecha de despacho Asignada.
	if($textfield_tipoprod == 'PE')
	{
		$fecha = date('d/m/Y');
		$os_fechaestimada = suma_fechas($fecha, DIAS_PROD_PE);
		
		$consulta_fecha = "SELECT numero_dias FROM fecha_entrega WHERE cod_producto = $textfield_idprod";
		$resultado_fecha = tep_db_query($consulta_fecha);
		$numero_dias = tep_db_fetch_array($resultado_fecha);	
			if($numero_dias[0] == ''){	
				$os_fechaestimada = suma_fechas($fecha, DIAS_PROD_PE);
			}	
			else{
				$os_fechaestimada = suma_fechas($fecha, $numero_dias[0]);
			}
	}
	
	//Validación de producto PS como PE por exceder volumen maximo de entrega, se agregar fecha de entrega a los Pedidos Stock
	//Se combia el tipo de producto de PS a PE en este caso.	
	$consulta_cantidad = "SELECT cantidadventaxvolumen FROM ventaxvolumenps WHERE cantidadventaxvolumen <= $textfield4 AND cod_sap = $textfield2_aux ";
	$resultado_cantidad = tep_db_query($consulta_cantidad);
	$numero_cantidad = tep_db_fetch_array($resultado_cantidad);
	
	if($numero_cantidad['cantidadventaxvolumen'] != '')
	{
		$fecha = date('d/m/Y');
		
		$consulta_fecha = "SELECT numero_dias FROM ventaxvolumenps WHERE cod_sap = $textfield2_aux ";
		$resultado_fecha = tep_db_query($consulta_fecha);
		$numero_dias = tep_db_fetch_array($resultado_fecha);		
		$os_fechaestimada = suma_fechas($fecha, $numero_dias[0]);
		$textfield_tipoprod = 'PE';	
		$select_despacho = 1;
		?>
		<SCRIPT LANGUAGE="JavaScript">
			alert('La cantidad ingresada para este producto excede la cantidad\nMáxima por tal motivo se cambia la Fecha de Entrega\nY se trata este producto como Pedido Especial (PE)');
		</SCRIPT>
		<?	
	}
	
	if($select_despacho == 4){
	$os_fechaestimada = date('d/m/Y');
	}
	
	if($textfield_subtipoprod != 'DE'){
	?>
	<SCRIPT LANGUAGE="JavaScript">	
		parent.addprod.retira_cliente.value = 1;		
	</SCRIPT>
	<?
	}
	
	// Se insertan los datos de la OS
	$ind_decimal = $res_2['ind_decimal'];
	if($ind_decimal == ''){
		$ind_decimal = 0;	
	}
	if ($textfield5_aux!='') {
		$query =  "INSERT INTO os_detalle (ot_id,  id_origen,      id_tipodespacho,		     id_producto,			    id_os,		   osde_tipoprod,		  osde_subtipoprod,		  osde_instalacion,			    osde_precio,			 osde_cantidad,      osde_descuento, osde_preciocosto,			       cod_sap,			osde_descripcion,  cod_barra,		          ind_dec,                    osde_fecha_entrega) 
				   VALUES(                 null,   4,			".($select_despacho+0).", ".($textfield_idprod+0).", ".($id_os+0).", '$textfield_tipoprod', '$textfield_subtipoprod', ".($select_instalacion+0).", ".($textfield5_aux+0).",	".($textfield4+0).",           0,	" . ($textfield_preccosto+0).", '$textfield2_aux', '$textfield3_aux', '$textfield_codbarra', ".$ind_decimal. ",'" . $os_fechaestimada . "')";
	}
	else {
		$query =  "INSERT INTO os_detalle (ot_id,id_origen,id_tipodespacho,		     id_producto,			    id_os,		   osde_tipoprod,		  osde_subtipoprod,		  osde_instalacion,			    osde_precio,			 osde_cantidad,      osde_descuento, osde_preciocosto,			 cod_sap,			osde_descripcion,  cod_barra,			ind_dec,   osde_fecha_entrega) 
				   VALUES(null,4,					".($select_despacho+0).", ".($textfield_idprod+0).", ".($id_os+0).", '$textfield_tipoprod', '$textfield_subtipoprod', ".($select_instalacion+0).", null,						".($textfield4+0).", 0,			 ".($textfield_preccosto+0).", '$textfield2_aux', '$textfield3_aux', '$textfield_codbarra', ".$ind_decimal. ",'" . $os_fechaestimada . "')";

}
	
	/*
	if ($textfield5_aux!='') {
		$query =  "INSERT INTO os_detalle (ot_id,  id_origen,      id_tipodespacho,		     id_producto,			    id_os,		   osde_tipoprod,		  osde_subtipoprod,		  osde_instalacion,			    osde_precio,			 osde_cantidad,      osde_descuento, osde_preciocosto,			       cod_sap,			osde_descripcion,  cod_barra,		              osde_fecha_entrega) 
				   VALUES(                 null,   4,			".($select_despacho+0).", ".($textfield_idprod+0).", ".($id_os+0).", '$textfield_tipoprod', '$textfield_subtipoprod', ".($select_instalacion+0).", ".($textfield5_aux+0).",	".($textfield4+0).",           0,	" . ($textfield_preccosto+0).", '$textfield2_aux', '$textfield3_aux', '$textfield_codbarra', '" . $os_fechaestimada . "')";
	}
	else {
		$query =  "INSERT INTO os_detalle (ot_id,id_origen,id_tipodespacho,		     id_producto,			    id_os,		   osde_tipoprod,		  osde_subtipoprod,		  osde_instalacion,			    osde_precio,			 osde_cantidad,      osde_descuento, osde_preciocosto,			 cod_sap,			osde_descripcion,  cod_barra,			         osde_fecha_entrega) 
				   VALUES(null,4,					".($select_despacho+0).", ".($textfield_idprod+0).", ".($id_os+0).", '$textfield_tipoprod', '$textfield_subtipoprod', ".($select_instalacion+0).", null,						".($textfield4+0).", 0,			 ".($textfield_preccosto+0).", '$textfield2_aux', '$textfield3_aux', '$textfield_codbarra', '" . $os_fechaestimada . "')";
	}
	*/
	//echo $query, "<br>";

	tep_db_query($query);
	$ultimoID = tep_db_insert_id('');

	if ($res_2['ind_decimal']==0 && ((($textfield4 + 0)*1000)%1000) > 0) {
		?>
		<SCRIPT LANGUAGE="JavaScript">
			alert('La cantidad ingresada para este producto sólo puede ser un valor entero');
		</SCRIPT>
		<?
		$query_esp = "UPDATE os_detalle SET osde_cantidad = ".floor($textfield4+0)." 
					  WHERE id_os_detalle = " . ($ultimoID+0);
		tep_db_query($query_esp);
	}

	?>
	<SCRIPT LANGUAGE="JavaScript">		
		parent.addprod.textfield2.value = '';
		parent.busca_codigo(parent.addprod.textfield2);
		parent.addprod.textfield2.focus();
	</SCRIPT>
	<?
}

// Si la accion en 'Nuevo Producto' y La accion secundaria 'Modificacion'.
if ($accion == "np" && $accionsec=="mod") {

	$os_fechaestimada = '';
	//Si no hay id_os, se crea la id_os = 0
	if (!$id_os && $clie_rut) 
		$id_os = $clie_rut*-1;

	$query="SELECT osd.id_os_detalle, osd.id_os_detalle
			FROM os_detalle osd 
			WHERE osd.id_os = " . ($id_os+0) . "
			ORDER BY osd.id_os_detalle desc
			limit $IndexSelected2, 1";
	$tdq_1 = tep_db_query($query);
	$res_1 = tep_db_fetch_array( $tdq_1 );
	
	
	$consultaPE = "SELECT prod_tipo FROM  productos WHERE cod_prod1 = $textfield2_aux";
	$resultadoPE = tep_db_query($consultaPE);
	$tipoProducto = tep_db_fetch_array($resultadoPE);
			
// Si el tipo de Producto es Stock y tiene tipo de despacho desde proveedor asigna fecha de Despacho.
	if( ($textfield_tipoprod == 'PS' || $textfield_tipoprod == 'PE') && ($select_despacho == '5' || $select_despacho == '6') )
	{
		$fecha = date('d/m/Y');
		
		$consulta_fecha = "SELECT v.numero_dias
							FROM ventaxvolumenps v
							JOIN productos p ON v.cod_sap = p.cod_prod1
							WHERE v.cod_sap=".$textfield_idprod;
		$resultado_fecha = tep_db_query($consulta_fecha);
		$numero_dias = tep_db_fetch_array($resultado_fecha);		
			if($numero_dias[0] == ''){	
				$os_fechaestimada = suma_fechas($fecha, DIAS_PROD_PE);
			}	
			else{
				$os_fechaestimada = suma_fechas($fecha, $numero_dias[0]);
			}
	}
	
// Si el tipo de Producto es PE aplica la fecha de despacho Asignada.
	if($textfield_tipoprod == 'PE')
	{
		$fecha = date('d/m/Y');
		
		$consulta_fecha = "SELECT v.numero_dias
							FROM ventaxvolumenps v
							JOIN productos p ON v.cod_sap = p.cod_prod1
							WHERE v.cod_sap=".$textfield_idprod;
		$resultado_fecha = tep_db_query($consulta_fecha);
		$numero_dias = tep_db_fetch_array($resultado_fecha);	
			if($numero_dias[0] == ''){	
				$os_fechaestimada = suma_fechas($fecha, DIAS_PROD_PE);
			}	
			else{
				$os_fechaestimada = suma_fechas($fecha, $numero_dias[0]);
			}
	}
	

	$consulta_cantidad = "SELECT cantidadventaxvolumen FROM ventaxvolumenps WHERE cantidadventaxvolumen <= $textfield4 AND cod_sap = $textfield2_aux ";
	$resultado_cantidad = tep_db_query($consulta_cantidad);
	$numero_cantidad = tep_db_fetch_array($resultado_cantidad);	
	
	if($numero_cantidad['cantidadventaxvolumen'] != '')
	{
		$fecha = date('d/m/Y');
		
		$consulta_fecha = "SELECT numero_dias FROM ventaxvolumenps WHERE cod_sap = $textfield2_aux ";
		$resultado_fecha = tep_db_query($consulta_fecha);
		$numero_dias = tep_db_fetch_array($resultado_fecha);		
		$os_fechaestimada = suma_fechas($fecha, DIAS_PROD_PE);
		$textfield_tipoprod = 'PE';
		$select_despacho = 1;
		?>
		<SCRIPT LANGUAGE="JavaScript">
			alert('La cantidad ingresada para este producto excede la cantidad\nMáxima por tal motivo se cambia la Fecha de Entrega\nY se trata este producto como Pedido Especial (PE)');
		</SCRIPT>
		<?
		
	}
	else{		
		$consultaPE = "SELECT prod_tipo FROM  productos WHERE cod_prod1 = $textfield2_aux";
		$resultadoPE = tep_db_query($consultaPE);
		$tipoProducto = tep_db_fetch_array($resultadoPE);
		
		if($tipoProducto['prod_tipo']== 'PS')
		$textfield_tipoprod = 'PS';
	}	
		
	
	if($select_despacho == 4){
	$os_fechaestimada = date('d/m/Y');
	}
	
	// Se insertan los datos de la OS
	if ($textfield5_aux!='') {
		$query =  "UPDATE os_detalle SET osde_fecha_entrega = '$os_fechaestimada', id_tipodespacho = ".($select_despacho+0).", osde_instalacion = ".($select_instalacion+0).", osde_precio = ".($textfield5_aux+0).", osde_cantidad = ".($textfield4+0) . " ,osde_tipoprod = '". $textfield_tipoprod . "'" . 
			      " WHERE id_os_detalle = " . ($res_1['id_os_detalle']+0);
	}
	else {
		$query =  "UPDATE os_detalle SET osde_fecha_entrega = '$os_fechaestimada', id_tipodespacho = ".($select_despacho+0).", osde_instalacion = ".($select_instalacion+0).", osde_precio = null, osde_cantidad = ".($textfield4+0). " ,osde_tipoprod = '". $textfield_tipoprod . "'" . 
				   " WHERE id_os_detalle = " . ($res_1['id_os_detalle']+0);
	}
	tep_db_query($query);

	$my_textfield_codbarra = $textfield_codbarra +0;
	$query="SELECT ind_decimal, c.unid_med 
			FROM codbarra c 
			JOIN unidmed u on c.unid_med = u.unid_med 
			WHERE cod_barra = '$my_textfield_codbarra'"; 

	$tdq_1 = tep_db_query($query);
	$res_2 = tep_db_fetch_array( $tdq_1 );

	if ($res_2['ind_decimal']==0 && ((($textfield4 + 0)*1000)%1000) > 0) {
		?>
		<SCRIPT LANGUAGE="JavaScript">
			alert('La cantidad ingresada para este producto sólo puede ser un valor entero');
		</SCRIPT>
		<?
		$query_esp = "UPDATE os_detalle SET osde_cantidad = ".floor($textfield4+0)." 
					  WHERE id_os_detalle = " . ($res_1['id_os_detalle']+0);
		tep_db_query($query_esp);
	}

	?>

	<SCRIPT LANGUAGE="JavaScript">		
		parent.addprod.textfield2.disabled = false;
		parent.addprod.tipocod[0].disabled = false;
		parent.addprod.tipocod[1].disabled = false;
		parent.addprod.textfield2.value = '';
		parent.addprod.accionsec.value = '';
		parent.busca_codigo(parent.addprod.textfield2);
		parent.addprod.textfield2.focus();
		parent.addprod.Button2.value = 'Agregar ';
	</SCRIPT>
	<?
}

// Si la acción es elmiminar.
if ($accion == "eli") {
	//Si no hay id_os, se asume el rut * -1
	if (!$id_os && $clie_rut) 
		$id_os = $clie_rut*-1;

	$query="SELECT osd.id_os_detalle
			FROM os_detalle osd 
			WHERE osd.id_os = " . ($id_os+0) . "
			ORDER BY osd.id_os_detalle desc
			limit $IndexSelected, 1";
	$tdq_1 = tep_db_query($query);
	$res_1 = tep_db_fetch_array( $tdq_1 );

	// Se elimina el registro 
	$query =  "delete from os_detalle where id_os_detalle = " . ($res_1['id_os_detalle']+0);
	tep_db_query($query);
	?>
	<SCRIPT LANGUAGE="JavaScript">
		parent.addprod.textfield2.focus();
	</SCRIPT>
	<?
}

// Si la acción es eliminar todos.
if ($accion == "elitod") {
	//Si no hay id_os, se asume el rut * -1
	if (!$id_os && $clie_rut) 
		$id_os = $clie_rut*-1;

	$query =  "delete from os_detalle where id_os = $id_os";
	tep_db_query($query);
	?>
	<SCRIPT LANGUAGE="JavaScript">
		parent.addprod.textfield2.focus();
	</SCRIPT>
	<?
}

// Si la accion es modificación.
if ($accion == "mod") {
	//Si no hay id_os, se asume el rut * -1
	if (!$id_os && $clie_rut) 
		$id_os = $clie_rut*-1;

	$query="SELECT osd.*
			FROM os_detalle osd 
			WHERE osd.id_os = " . ($id_os+0) . "
			ORDER BY osd.id_os_detalle desc
			limit $IndexSelected, 1";
	
	$tdq_1 = tep_db_query($query);
	$res_1 = tep_db_fetch_array( $tdq_1 );	
	
	$query_2="SELECT precio_mod, precio_req, valid_stock, p.stock_proveedor 
			  FROM productos p join tipo_subtipo t on t.prod_tipo = p.prod_tipo and t.prod_subtipo = p.prod_subtipo 
			  WHERE id_producto = " . ($res_1['id_producto']+0) . " AND p.estadoactivo = 'C' ";
	
	$tdq_2 = tep_db_query($query_2);
	$res_2 = tep_db_fetch_array( $tdq_2 );
	
	
	$consulta_cantidad = "SELECT cantidadventaxvolumen FROM ventaxvolumenps WHERE cantidadventaxvolumen <= " . $res_1['osde_cantidad'] . " AND cod_sap = " . $res_1['cod_sap'];
	$resultado_cantidad = tep_db_query($consulta_cantidad);
	$numero_cantidad = tep_db_fetch_array($resultado_cantidad);	
	
	$cantidadPSmayor = 1;
	if($numero_cantidad['cantidadventaxvolumen'] != '')
	{	
	$cantidadPSmayor = 0;
	}
	
	?>

	<SCRIPT LANGUAGE="JavaScript">
	
		parent.addprod.textfield2.value = '<?=$res_1['cod_barra']?>';
		parent.addprod.textfield2_aux.value = '<?=$res_1['cod_sap']?>';
		parent.addprod.textfield_codbarra.value = '<?=$res_1['cod_barra']?>';
		parent.addprod.textfield3.value = '<?=$res_1['osde_descripcion']?>';
		parent.addprod.textfield3_aux.value = '<?=$res_1['osde_descripcion']?>';
		for (i=0; i<parent.addprod.select_despacho.length; ++i)
			if (parent.addprod.select_despacho[i].value == '<?=$res_1['id_tipodespacho']?>')
				parent.addprod.select_despacho.selectedIndex = i;
		for (i=0; i<parent.addprod.select_instalacion.length; ++i)
			if (parent.addprod.select_instalacion[i].value == '<?=$res_1['osde_instalacion']?>')
				parent.addprod.select_instalacion.selectedIndex = i;
		parent.addprod.textfield5.value = '<?=$res_1['osde_precio']?>';
		parent.addprod.textfield5_aux.value = '<?=$res_1['osde_precio']?>';
		parent.addprod.textfield4.value = '<?=$res_1['osde_cantidad']?>';
		parent.addprod.textfield6.value = '<?=($res_1['osde_precio'])?($res_1['osde_cantidad']*$res_1['osde_precio']):0?>';
		parent.addprod.textfield_idprod.value = '<?=$res_1['id_producto']?>';
		parent.addprod.textfield_tipoprod.value = '<?=$res_1['osde_tipoprod']?>';
		parent.addprod.textfield_subtipoprod.value = '<?=$res_1['osde_subtipoprod']?>';
		parent.addprod.textfield_preccosto.value = '<?=$res_1['osde_preciocosto']?>';
		parent.addprod.textfield_precio_req.value='<?=$res_2['precio_req']?>';
		parent.addprod.textfield_precio_mod.value='<?=$res_2['precio_mod']?>';
		parent.addprod.textfield_valid_stock.value='<?=$res_2['valid_stock']?>';
		parent.addprod.textfield_stock_prov.value='<?=$res_2['stock_proveedor']?>';		
		parent.addprod.textfield5.disabled = ('<?=$res_2['precio_mod']?>' == '1')?false:true;
		
		parent.addprod.textfield2.disabled = true;
		parent.addprod.tipocod[0].checked = true;
		parent.addprod.tipocod[0].disabled = true;
		parent.addprod.tipocod[1].disabled = true;		
		parent.addprod.textfield4.focus();
		parent.addprod.textfield4.select();
		parent.addprod.Button2.value = 'Modificar';
		parent.addprod.accionsec.value = 'mod';
		parent.addprod.IndexSelected2.value = '<?=$IndexSelected?>';
		
		<?php
		if($res_1['osde_tipoprod'] == 'PE' && $cantidadPSmayor)
		{
		?>				
		//parent.addprod.botonfecha.disabled = true;
		<?php
		}
		else{
		?>
		//parent.addprod.botonfecha.disabled = false;
		<?php 			
		}
		?>
		
	</SCRIPT>

	<?php
}

/** Fin Acciones CGI **/

$MiTemplate = new Template();



		
// asignamos degug maximo
$MiTemplate->debug = 0;
// root directory de los templates
$MiTemplate->set_root(DIRTEMPLATES);
// variables perdidas
$MiTemplate->set_unknowns("remove");

// Agregamos el header
//$MiTemplate->set_file("header","header_sc.html");

// Agregamos el main
$MiTemplate->set_file("main","nueva_cotizacion/nueva_cotizacion_0302.htm");

// Esto es en caso de que no exista previamente la os.
if (!$id_os && $clie_rut) 
	$id_os = $clie_rut*-1;

	$MiTemplate->set_var("IDOS", $id_os);
	
$noDesp=" - ";

//Realiza el select del producto y envia estos datos a la Grilla
$query="
		SELECT DISTINCT	if(length(osd.cod_barra)=0, '(Sin código)', osd.cod_barra) 'Código Barra', 
				if(length(osd.osde_descripcion)=0, '(Sin código)', osd.osde_descripcion) 'Descripción', 
				if ((osd.osde_tipoprod='SV'|| osd.osde_descripcion='Despacho Normal'|| osd.osde_descripcion='Despacho Express'), ' $noDesp ', td.nombre) 'Despacho', 
				if ((osd.osde_tipoprod='SV' || osd.osde_descripcion='Despacho Normal'|| osd.osde_descripcion='Despacho Express'), ' $noDesp ',  if (osde_instalacion, 'SI', 'NO')) 'Instal',
    			if (osd.osde_precio is null, ' - ',  osd.osde_precio) 'Precio con Iva',
				osd.osde_cantidad 'Cantidad',
				osd.osde_descuento*osd.osde_cantidad 'Dscto',
				if (ROUND(osd.osde_cantidad*(osd.osde_precio-osd.osde_descuento)), ROUND(osd.osde_cantidad*(osd.osde_precio-osd.osde_descuento)), 0) 'SubTot',
				if (trim(osde_especificacion) != '', '*', '') 'Espec'
		FROM os_detalle osd 
			LEFT JOIN tipos_despacho td ON 
				osd.id_tipodespacho = td.id_tipodespacho
				INNER JOIN productos P  ON P.cod_prod1 = osd.cod_sap
				INNER JOIN codbarra CB  ON CB.cod_prod1 = osd.cod_sap
				INNER JOIN precios PR  ON PR.cod_prod1 = P.cod_prod1
		WHERE osd.id_os = " . ($id_os+0) . "  AND P.estadoactivo = 'C' AND CB.estadoactivo = 'C' AND PR.estadoactivo = 'C' 
		ORDER BY osd.id_os_detalle desc ";

// Activa la Grilla para agregar los datos del producto seleccionado
$prueba = activewidgets_grid("obj", tep_db_query($query));

$MiTemplate->set_var("PRODUCTOS",$prueba);


$query="
		SELECT	sum(ROUND(osd.osde_cantidad*(osd.osde_precio-osd.osde_descuento))) 'total', count(*) 'cantreg'
		FROM os_detalle osd 
			LEFT JOIN tipos_despacho td ON 
				osd.id_tipodespacho = td.id_tipodespacho 
		WHERE osd.id_os = " . ($id_os+0) ;
$rq_1 = tep_db_query($query);
$res_1 = tep_db_fetch_array( $rq_1 );
$MiTemplate->set_var("total",formato_precio($res_1['total']+0));
$MiTemplate->set_var("cantreg",$res_1['cantreg']);

// Agregamos el footer
//$MiTemplate->set_file("footer","footer_sc.html");

//$MiTemplate->pparse("OUT_H", array("header"), true);
$MiTemplate->parse("OUT_M", array("main"), true);
$MiTemplate->p("OUT_M");

?>
