<?php
$pag_ini = '../lista_Regalos/nueva_lista_00.php';
include "../../includes/aplication_top.php";
include "../nueva_cotizacion/activewidgets.php";

// include_idioma_mod( $ses_idioma, "nueva_cotizacion_01" );
// *************************************************************************************
$usr_local = get_local_usr( $ses_usr_id );

	/*
	* Implementación de cambiar todos los tipos de entrega de los productos  
	* de una catización, a un mismo tipo de entrega.
	*/

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
//Actualiza los precios de los productos
if ($accion == "relprice") {
	
	//Se actualizan los precios del listado
	$query = "	SELECT id_Local, id_Estado FROM list_regalos_enc WHERE idLista = ".($idLista+0);
	$rq_1 = tep_db_query($query);
	$res_1 = tep_db_fetch_array($rq_1);	
	
	//Se actualizan los precios en todos los estados.
		$query_2 = "SELECT Ld.idLista_det, Ld.cod_Ean, Ld.list_precio precant, P.prec_valor precnue
					FROM list_regalos_det Ld
          			LEFT JOIN productos Pr ON Ld.cod_Easy = Pr.cod_prod1
          			LEFT JOIN precios P ON Pr.cod_prod1 = P.cod_prod1 AND P.id_local = ".(get_local_usr( $ses_usr_id )+0)."
					WHERE Ld.idLista_enc = $idLista AND Pr.prod_subtipo <> 'DE';";
        $rq_2 = tep_db_query($query_2);
        while( $res_2 = tep_db_fetch_array( $rq_2 ) ) {
			$queryup =  "UPDATE list_regalos_det SET list_precio = " . (strlen($res_2['precnue'])?$res_2['precnue']:'null') . " WHERE idLista_enc = ".($idLista+0)." and idLista_det = ".($res_2['idLista_det']+0);
			tep_db_query($queryup);
			
		}

		// Se actualiza la info del local. Se deshabilita en la edicion de Listas de Regalos.
		/*
		if ($res_1['id_local'] != get_local_usr( $ses_usr_id )) {
			$queryup =  "UPDATE list_regalos_enc SET id_Local = " . (get_local_usr( $ses_usr_id )+0) . ", id_Usuario = " . ($ses_usr_id+0) . " WHERE idLista = ".($idLista+0);
			tep_db_query($queryup);
		}
		*/

        /* saca nombre del estado para poner en el trackin*/
        $querN="SELECT nom_local FROM locales WHERE id_local = " . (get_local_usr( $ses_usr_id )+0);
        $estaN = tep_db_query($querN);
        $estaN = tep_db_fetch_array( $estaN );
        $esta_nombre=$estaN['nom_local'];

		insertahistorial("Se actualizó la información de la OS $idLista para el local $esta_nombre");

		//Se actualiza la info de la fecha de cotización
		$queryup =  "UPDATE list_regalos_enc SET fec_creacion = now() WHERE idLista = ".($idLista+0);
		tep_db_query($queryup);
}

// Si la acción es 'Nuevo Producto' y no viene acción accion secundaria
if ($accion == "np" && !$accionsec) {
	$os_fechaestimada = '';

	//Verificamos el tipo decimal
	$my_textfield_codbarra = $textfield_codbarra +0;
	$query="SELECT ind_decimal, c.unid_med 
			FROM codbarra c 
			JOIN unidmed u on c.unid_med = u.unid_med 
			WHERE cod_barra = '$my_textfield_codbarra'"; 
	$tdq_1 = tep_db_query($query);
	$res_2 = tep_db_fetch_array( $tdq_1 );
	
	//Verificamos el estado de la Lista de Regalos
	$Qry_est = "SELECT id_Estado FROM list_regalos_enc WHERE idLista =".($idLista+0);
	$res_est = tep_db_query($Qry_est);
	$result_est = tep_db_fetch_array( $res_est );
	$estado = $result_est['id_Estado'];
	
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
	
	// Se insertan los productos al detalle de la Lista
	$ind_decimal = $res_2['ind_decimal'];
	if($ind_decimal == ''){
		$ind_decimal = 0;	
	}
	if ($textfield5_aux!='') {
		$query =  "INSERT INTO list_regalos_det (idLista_enc, cod_Ean, cod_Easy, descripcion, list_tipoprod, list_cantprod, list_precio, list_idTipodespacho, list_instalacion)
		           VALUES ('".($idLista+0)."', '".($textfield_codbarra+0)."',  '".($textfield2_aux+0)."', '".$textfield3_aux."', '".$textfield_tipoprod."', '".($textfield4+0)."', '".($textfield5_aux+0)."', '1', ".$select_instalacion.");";
	
	}
	else {
		$query =  "INSERT INTO list_regalos_det (idLista_enc, cod_Ean, cod_Easy, descripcion, list_tipoprod, list_cantprod, list_precio, list_idTipodespacho, list_instalacion)
		           VALUES ('".($idLista+0)."', '".($textfield_codbarra+0)."',  '".($textfield2_aux+0)."', '".$textfield3_aux."', '".$textfield_tipoprod."', '".($textfield4+0)."', '0', '1', ".$select_instalacion.");";

	}
	
	if (tep_db_query($query))
		$cambia_estado = 1;
		
	$ultimoID = tep_db_insert_id('');

	//Cambia el estado de la Lista de Regalos
	if ($estado = 'NP' && $cambia_estado){
		$Qry_est = "UPDATE list_regalos_enc SET id_Estado = 'CP' WHERE idLista = " .($idLista+0);
		tep_db_query($Qry_est);
	}
		
	if ($res_2['ind_decimal']==0 && ((($textfield4 + 0)*1000)%1000) > 0) {
		?>
		<SCRIPT LANGUAGE="JavaScript">
			alert('La cantidad ingresada para este producto sólo puede ser un valor entero');
		</SCRIPT>
		<?
		$query_esp = "UPDATE list_regalos_det SET list_cantprod = ".floor($textfield4+0)." 
					  WHERE idLista_det = " . ($ultimoID+0);
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

	$query="SELECT Ld.idLista_det, Ld.idLista_enc 
			FROM list_regalos_det Ld 
			WHERE Ld.idLista_enc = " .($idLista+0). "
			ORDER BY Ld.idLista_det desc
			limit $IndexSelected2, 1";
	$tdq_1 = tep_db_query($query);
	$res_1 = tep_db_fetch_array( $tdq_1 );
	
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
			
	// Se insertan los datos de la OS
	if ($textfield5_aux!='') {
		$query =  "UPDATE list_regalos_det SET list_idTipodespacho = '1', list_instalacion = ".($select_instalacion+0).", list_precio = ".($textfield5_aux+0).", list_cantprod = ".($textfield4+0) . " ,list_tipoprod = '". $textfield_tipoprod . "'" . 
			      " WHERE idLista_det = " . ($res_1['idLista_det']+0);
	}
	else {
		$query =  "UPDATE list_regalos_det SET list_idTipodespacho = '1', list_instalacion = ".($select_instalacion+0).", list_precio = null, list_cantprod = ".($textfield4+0) . " ,list_tipoprod = '". $textfield_tipoprod . "'" . 
			      " WHERE idLista_det = " . ($res_1['idLista_det']+0);
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
		$query_esp = "UPDATE list_regalos_det SET list_cantprod = ".floor($textfield4+0)." 
					  WHERE idLista_det = " . ($res_1['idLista_det']+0);
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
	$query="SELECT Ld.idLista_det, Ld.idLista_enc 
			FROM list_regalos_det Ld 
			WHERE Ld.idlista_enc = ".($idLista+0) . "
			ORDER BY Ld.idLista_det desc
			limit $IndexSelected, 1";
	$tdq_1 = tep_db_query($query);
	$res_1 = tep_db_fetch_array( $tdq_1 );

	// Se elimina el registro 
	$query =  "DELETE FROM list_regalos_det WHERE idLista_enc = ".($res_1['idLista_enc']+0)." AND idLista_det = ".($res_1['idLista_det']+0);
	tep_db_query($query);
	?>
	<SCRIPT LANGUAGE="JavaScript">
		parent.addprod.textfield2.focus();
	</SCRIPT>
	<?
}

// Si la acción es eliminar todos.
if ($accion == "elitod") {

	$query =  "DELETE FROM list_regalos_det WHERE idLista_enc = $idLista";
	tep_db_query($query);
	?>
	<SCRIPT LANGUAGE="JavaScript">
		parent.addprod.textfield2.focus();
	</SCRIPT>
	<?
}

// Si la accion es modificación.
if ($accion == "mod") {
	$query="SELECT Ld.*
			FROM list_regalos_det Ld 
			WHERE Ld.idLista_enc = " . ($idLista+0) . "
			ORDER BY Ld.idLista_det desc
			limit $IndexSelected, 1";

	$tdq_1 = tep_db_query($query);
	$res_1 = tep_db_fetch_array( $tdq_1 );

	$query_2="SELECT precio_mod, precio_req, valid_stock, p.stock_proveedor 
			  FROM productos p join tipo_subtipo t on t.prod_tipo = p.prod_tipo and t.prod_subtipo = p.prod_subtipo 
			  WHERE id_producto = " . ($res_1['cod_Easy']+0) ; 

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
	
		parent.addprod.textfield2.value = '<?=$res_1['cod_Ean']?>';
		parent.addprod.textfield2_aux.value = '<?=$res_1['cod_Easy']?>';
		parent.addprod.textfield_codbarra.value = '<?=$res_1['cod_Ean']?>';
		parent.addprod.textfield3.value = '<?=$res_1['descripcion']?>';
		parent.addprod.textfield3_aux.value = '<?=$res_1['descripcion']?>';
		for (i=0; i<parent.addprod.select_instalacion.length; ++i)
			if (parent.addprod.select_instalacion[i].value == '<?=$res_1['list_instalacion']?>')
				parent.addprod.select_instalacion.selectedIndex = i;
		parent.addprod.textfield5.value = '<?=$res_1['list_precio']?>';
		parent.addprod.textfield5_aux.value = '<?=$res_1['list_precio']?>';
		parent.addprod.textfield4.value = '<?=$res_1['list_cantprod']?>';
		parent.addprod.textfield6.value = '<?=($res_1['list_precio'])?($res_1['list_cantprod']*$res_1['list_precio']):0?>';
		//parent.addprod.textfield_idprod.value = '<?=$res_1['cod_Ean']?>';
		parent.addprod.textfield_tipoprod.value = '<?=$res_1['list_tipoprod']?>';
		//parent.addprod.textfield_subtipoprod.value = '<?=$res_1['osde_subtipoprod']?>';
		//parent.addprod.textfield_preccosto.value = '<?=$res_1['osde_preciocosto']?>';
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


// Agregamos el main
$MiTemplate->set_file("main","lista_Regalos/edita_lista02.htm");

// Esto es en caso de que no exista previamente la os.
if (!$id_os && $clie_rut) 
	$id_os = $clie_rut*-1;

	$MiTemplate->set_var("IDOS", $id_os);
	
$noDesp=" - ";

//Realiza el select del producto y envia estos datos a la Grilla
$query="SELECT	if(length(Ld.cod_Ean)=0, '(Sin código)', Ld.cod_Ean) 'Código Barra',
				if(length(Ld.descripcion)=0, '(Sin código)', Ld.descripcion) 'Descripción',
				if ((Ld.list_tipoprod='SV'|| Ld.descripcion='Despacho Normal'|| Ld.descripcion='Despacho Rapido'), ' $noDesp ', td.nombre) 'Despacho',
				if ((Ld.list_tipoprod='SV' || Ld.descripcion='Despacho Normal'|| Ld.descripcion='Despacho Rapido'), ' $noDesp ',  if (Ld.list_instalacion, 'SI', 'NO')) 'Instal',
        		if (Ld.list_precio is null, ' - ',  Ld.list_precio) 'Precio con Iva', Ld.list_cantprod 'Cantidad', 0 'Dscto',
				if (ROUND(Ld.list_cantprod*Ld.list_precio), ROUND(Ld.list_cantprod*Ld.list_precio), 0) 'SubTot'
		FROM list_regalos_det Ld
        LEFT JOIN tipos_despacho td ON Ld.list_idTipodespacho = td.id_tipodespacho
		WHERE Ld.idLista_enc = " . ($idLista+0) . "
		ORDER BY Ld.idLista_det desc";

// Activa la Grilla para agregar los datos del producto seleccionado
$prueba = activewidgets_grid("obj", tep_db_query($query));

$MiTemplate->set_var("PRODUCTOS",$prueba);


$query="SELECT	sum(ROUND(Ld.list_cantprod*Ld.list_precio)) 'total', count(*) 'cantreg'
		FROM list_regalos_det Ld
    	LEFT JOIN tipos_despacho td ON Ld.list_idTipodespacho = td.id_tipodespacho
		WHERE Ld.idLista_enc =" . ($idLista+0) ;
$rq_1 = tep_db_query($query);
$res_1 = tep_db_fetch_array( $rq_1 );
$MiTemplate->set_var("total",formato_precio($res_1['total']+0));
$MiTemplate->set_var("cantreg",$res_1['cantreg']);

//$MiTemplate->pparse("OUT_H", array("header"), true);
$MiTemplate->parse("OUT_M", array("main"), true);
$MiTemplate->p("OUT_M");

?>
