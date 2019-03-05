<?php
$pag_ini = '../lista_Regalos/nueva_lista_00.php';
include "../../includes/aplication_top.php";
include "activewidgets.php";


include_idioma_mod( $ses_idioma, "nueva_lista_sumario_01" );

// *************************************************************************************
//Verifico que exista el id de local antes de continuar
if (!get_local_usr( $ses_usr_id )) {
	?>
	<SCRIPT language="JavaScript" >
		alert('Para ingresar a esta interfaz, es necesario que ud. tenga asignado un local.\nContáctese con el administrador');
		history.back();
	</SCRIPT>
	<?php
	tep_exit();
}
/***************************/
/** Inicio Acciones  **/
$local_id = get_local_usr( $ses_usr_id );
$USR_LOGIN = get_login_usr( $ses_usr_id );

if ($accion == "productos") {
// si no tiene id de os le asigo el rut del cliente por menos uno
	if (!$id_os){
		$where_aux= ($clie_rut*-1);
	}else{
	$where_aux=$id_os;
	}


$cuentaSVDE=0;
$cuentaPSoPEConDesp=0;
$cuentaPE =0;
$cuentaDespProv =0;
$query_OD="	select OD.osde_descuento, OD.osde_especificacion, (OD.cod_sap+0)  as cod_sap,OD.cod_barra,OD.osde_tipoprod,OD.osde_subtipoprod,OD.osde_descripcion,OD.osde_cantidad,OD.id_tipodespacho,OD.osde_instalacion,OD.osde_precio,OD.osde_descuento,TD.nombre,
					if(osde_descuento is not null, osde_descuento, 0) 'osde_descuento',
					if (osde_descuento<>0,ROUND((osde_precio-osde_descuento)*osde_cantidad),ROUND(osde_precio*osde_cantidad))'Total',
					if(((osde_precio-osde_descuento)*osde_cantidad) is Null,'$totalVacio','') 'totalVacio',
					if (OD.osde_tipoprod='SV', ' - ',  if (OD.osde_instalacion, 'SI', 'NO')) 'osde_instalacion',
					if(OD.osde_precio is null, '$precioVacio', '') 'precioVacio' ,
					if(OD.osde_descuento is null, '$descuento', '') 'descuento', 
					if(OD.osde_especificacion is not null, '$especificacion', '') 'especificacion' 
				from os_detalle OD inner join tipos_despacho TD on (TD.id_tipodespacho=OD.id_tipodespacho) 
				where id_os=".($where_aux+0)." 
				order by OD.osde_tipoprod ";
 if ( $rq = tep_db_query($query_OD) ){
        while( $res = tep_db_fetch_array( $rq ) ) {
			if( $res['osde_tipoprod']=='PE')
			$cuentaPE += 1; 
        	
			if (($res['osde_tipoprod']=='PS' || $res['osde_tipoprod']=='PE') && ($res['nombre']=='D. Programado' || $res['nombre']=='Rapido'))	
        		$cuentaPSoPEConDesp+=1;
            if ($res['osde_tipoprod']=='SV' && $res['osde_subtipoprod']=='DE')
                $cuentaSVDE+=1; 
            if ($res['id_tipodespacho']=='5' || $res['id_tipodespacho']=='6')
                $cuentaDespProv+=1;
        }
}


 $os_comentarios2 = ereg_replace("\r\n", " ", $os_comentarios);
 	
	if($cuentaPE > 0)
	{
 	?>
 	<script language="JavaScript"> 	
 	alert('No olvide comunicarse con el proveedor para verificar la existencia\n  de los productos de pedido especial incluidos en la Cotización.');
	</script>
	<?php
	}
	
	if($cuentaDespProv > 0){
	 	?>
 	<script language="JavaScript"> 	
 	alert('No olvide comunicarse con el proveedor para verificar la existencia de los productos \n\t despachados por el Proveedor e incluidos en la Cotización');
	</script>
	<?php
	}
    
	if ($cuentaPSoPEConDesp && !$cuentaSVDE){
        ?>
        <script language="JavaScript">
            //if (confirm("Uno o más productos de la cotización requieren un tipo de despacho.\nDebe Agregar servicio de despacho para estos productos\no cambiar el tipo de despacho a 'Retira Cliente'\n ¿Desea continuar de todas maneras?")) {
               document.location = 'nueva_cotizacion_03.php?id_os=<?=$id_os+0?>&accion=gs&clie_rut=<?=$clie_rut?>&select_proyecto3=<?=$select_proyecto3?>&os_fechaestimada=<?=$os_fechaestimada?>&os_comentarios=<?=$os_comentarios2?>&os_descripcion=<?=$os_descripcion?>';
            //}
        </script>
        <?php
    }else{
		?>
        <script language="JavaScript">
            document.location = 'nueva_cotizacion_03.php?id_os=<?=$id_os+0?>&accion=gs&clie_rut=<?=$clie_rut?>&select_proyecto3=<?=$select_proyecto3?>&os_fechaestimada=<?=$os_fechaestimada?>&os_comentarios=<?=$os_comentarios2?>&os_descripcion=<?=$os_descripcion?>';
        </script>
		<?
        tep_exit();
    }
 
 
}

/*Grabar siguiente*/
if ($accion == "gs") {
	if(!$id_os && $clie_rut){ //Nueva OS
		//Esta acción debe ser transaccional
		tep_db_query("SET AUTOCOMMIT=0");

		$success = true;
		// Se insertan los datos de la OS
		
		$os_fechaestimada2 = date('d/m/Y');
		
		$queryIOS =  "INSERT INTO os (id_estado,       id_proyecto,					id_local,					      id_direccion,	  clie_rut,	  os_fechacreacion, os_fechacotizacion,     os_fechaestimacion,	                                         os_fechaestimada,								      os_comentarios,    os_descripcion,	  usuario,							  USR_ID,            origen,                  USR_ORIGEN) 
					  SELECT		  'SA',		 " . ($select_proyecto3+0) . ", ".(get_local_usr( $ses_usr_id )+0).", d.id_direccion, c.clie_rut, now(),			now(),			        DATE_ADD(now(), INTERVAL ".(DIAS_VALID_COT + 0)." DAY), '" . fecha_php2db_new($os_fechaestimada2) . " 00:00:00', '$os_comentarios', NULL, '".get_login_usr( $ses_usr_id )."', ".($ses_usr_id+0).", 'C' ,'".get_login_origen( $ses_usr_id )."' 
					  FROM clientes c left join direcciones d on d.clie_rut = c.clie_rut 
					  WHERE c.clie_rut = " . ($clie_rut+0) . " and d.dire_defecto = 'p'";
		
		$success = $success && tep_db_query($queryIOS);		
		
        $ultimoID = tep_db_insert_id('');

		//Se reasigna el id de os temporal al id actual
		$queryIOS =  "UPDATE os_detalle SET id_os = $ultimoID WHERE id_os = " . (($clie_rut*-1)+0);
		$success = $success && tep_db_query($queryIOS);
		//Se borra la os temporal con el rut*-1 
		$queryIOS =  "DELETE FROM os WHERE id_os = " . (($clie_rut*-1)+0);
		$success = $success && tep_db_query($queryIOS);
        
		//Fin de la transacción
		if ($success)
			tep_db_query("commit");
		else
			tep_db_query("rollback");

		tep_db_query("SET AUTOCOMMIT=1");
	}
	else { // Antigua OS
		$os_fechaestimada2 = date('d/m/Y');
		$queryIOS =  "UPDATE os SET id_proyecto = " . ($select_proyecto3+0) . ", os_fechaestimada = '" . fecha_php2db_new($os_fechaestimada2) . " 00:00:00', os_comentarios = '$os_comentarios'  WHERE id_os = " . ($id_os+0);
		tep_db_query($queryIOS);
		$ultimoID = $id_os+0;
	}

	header ('Location: nueva_cotizacion_04.php?id_os='.($ultimoID+0));
	tep_exit();
}

// ************************************************************************************  \\
// Consulta los datos del Cliente.
	$Qry_cliente = "SELECT CL.clie_rut, CL.clie_nombre, CL.clie_paterno, CL.clie_materno
				    FROM list_regalos_enc L
				    JOIN clientes CL ON CL.clie_rut = L.clie_Rut
				    WHERE L.idLista=".($idLista+0);
	
	$res = tep_db_query($Qry_cliente);
	$resp = tep_db_fetch_array($res);
	
	$local_id = get_local_usr( $ses_usr_id );
	$USR_LOGIN = get_login_usr( $ses_usr_id );

 /* Obtiene los nombre de la persona que ingreso los datos en la Lista de Regalos*/
    $qnombre="SELECT U.usr_nombres,U.usr_apellidos FROM usuarios U INNER JOIN local_usr  as LU on (LU.usr_id=U.usr_id) WHERE id_local=".($local_id+0)." AND U.usr_login='$USR_LOGIN'";
    $ListAtendido = tep_db_query($qnombre);
    $ListAtendido = tep_db_fetch_array( $ListAtendido );
    $nombre=$ListAtendido['usr_nombres'];
    $apellido=$ListAtendido['usr_apellidos'];
    $nc=$nombre." ".$apellido;

// Consulta los datos de Localización de la Lista de Regalos
	$querysel="SELECT CL.clie_telefonocasa, CL.clie_telcontacto2, L.fec_Evento, L.clie_Rut, L.idLista, L.id_Estado, L.id_Local, L.descripcion, L.festejado, L.id_Evento, Ev.nombre, L.fec_creacion, E.esta_nombre ,Lc.nom_local, CL.clie_nombre, CL.clie_tipo, CL.clie_razonsocial, CL.clie_paterno, CL.clie_materno ,if('CP'=L.id_Estado, '', '') 'pagar', if (date(L.fec_creacion)>=date(now()), 1, 0) esfechavalida, if(L.id_Local=101, 1, 0) eslocalcorrecto 
				FROM list_regalos_enc L 
				LEFT JOIN estados E ON (E.id_estado=L.id_Estado) 
				LEFT JOIN locales Lc ON (Lc.id_local=L.id_Local) 
				LEFT JOIN direcciones D ON (L.id_Direccion=D.id_direccion) 
				LEFT JOIN clientes CL ON (L.clie_Rut=CL.clie_rut) 
				LEFT JOIN list_eventos Ev ON (L.id_Evento=Ev.idEvento) 
				WHERE L.idLista=".($idLista+0);
	$osSel = tep_db_query($querysel);
	$osSel = tep_db_fetch_array( $osSel );

// Consulta los datos de direccion principal del Cliente 
	$queryPRI = "SELECT  D.clie_rut, D.id_direccion, D.dire_nombre AS dire_nombre_pri , D.dire_direccion AS dire_direccion_pri, D.id_direccion , D.dire_defecto , D.dire_telefono AS dire_telefono_pri , D.dire_observacion AS dire_observacion_pri 
				 FROM direcciones D
			 	WHERE D.clie_rut = ".$resp['clie_rut']." and D.dire_defecto='p' ";
	$direna = tep_db_query($queryPRI);
	$direna = tep_db_fetch_array( $direna );

// Consulta el Barrio-Localidad de los datos del cliente
	$dirCli = consulta_localizacion($direna['clie_rut'],1);
	$dirCliente = getlocalizacion($dirCli);

// Consulta la direccción de Servicio de la Lista de Regalos.
	$queryDir="SELECT L.id_direccion, D.dire_telefono, D.dire_direccion, D.dire_observacion
				FROM list_regalos_enc L
				JOIN direcciones D ON D.id_direccion=L.id_direccion
				WHERE L.idLista =".($idLista+0);
	$osSelDir = tep_db_query($queryDir);
	$osSelDire = tep_db_fetch_array( $osSelDir );

	$dirServ = consulta_localizacion($osSelDire['id_direccion'],2);
	$dirServicio = getlocalizacion($dirServ);

/** Fin Acciones **/

$MiTemplate = new Template();
// asignamos degug maximo
$MiTemplate->debug = 0;
// root directory de los templates
$MiTemplate->set_root(DIRTEMPLATES);
// variables perdidas
$MiTemplate->set_unknowns("remove");


if (!$id_os && $clie_rut)
{ 
$id_os2 = $clie_rut*-1;
$MiTemplate->set_var("IDOS2", $id_os2);
}
else
$MiTemplate->set_var("IDOS2", $id_os);


//Agregamos la fecha actual
$MiTemplate->set_var("fecha_hoy",date("Ymd", time()));

$MiTemplate->set_var("id_direccion",$id_direccion);
$MiTemplate->set_var("id_proyecto",$id_proyecto);
$MiTemplate->set_var("clie_rut",$clie_rut);
$MiTemplate->set_var("ultimoID",$ultimoID);

// Datos del la Lista de Reglaos     
$MiTemplate->set_var("idLista", ($idLista?$idLista:"Datos no disponibles"));   
$MiTemplate->set_var("list_fec_cracion", fecha_db2php($osSel['fec_creacion']));
$MiTemplate->set_var("esta_nombre", ($osSel['esta_nombre']?$osSel['esta_nombre']:"Datos no disponibles") );
$MiTemplate->set_var('atendido', ($nc?$nc:"Datos no disponibles") );
$MiTemplate->set_var("nom_local", ($osSel['nom_local']?$osSel['nom_local']:"Datos no disponibles") );
$MiTemplate->set_var("list_fecEvento", fecha_db2php($osSel['fec_Evento']));
$MiTemplate->set_var("evento",($osSel['nombre']?$osSel['nombre']:"Datos no disponibles") );
$MiTemplate->set_var("festejado", ($osSel['festejado']?$osSel['festejado']:"Datos no disponibles") );
     
     // Datos de Cliente
$MiTemplate->set_var("rut_cliente", ($resp['clie_rut']?$resp['clie_rut']:"Datos no disponibles") );
$MiTemplate->set_var("clie_nombre", ($osSel['clie_nombre']?$osSel['clie_nombre']:"Datos no disponibles") );
$MiTemplate->set_var("clie_paterno", $osSel['clie_paterno'] );
$MiTemplate->set_var("clie_materno", $osSel['clie_materno'] );
$MiTemplate->set_var("dire_direccion_pri", ($direna['dire_direccion_pri']?$direna['dire_direccion_pri']:$osSel['clie_materno']) );
$MiTemplate->set_var("comu_nombre_pri",$dirCliente['barrio']." - ".$dirCliente['localidad']);
$MiTemplate->set_var("telefono_celular", ($osSel['clie_telcontacto2']?$osSel['clie_telcontacto2']:"Datos no disponibles") );
$MiTemplate->set_var("dire_telefono_pri", ($osSel['clie_telefonocasa']?$osSel['clie_telefonocasa']:"Datos no disponibles") );

     // Dirección de Servicio
$MiTemplate->set_var("dire_direccion", ($osSelDire['dire_direccion']?$osSelDire['dire_direccion']:"Datos no disponibles") );     
$MiTemplate->set_var("dire_telefono", ($osSelDire['dire_telefono']?$osSelDire['dire_telefono']:"Datos no disponibles") );
$MiTemplate->set_var("dire_observacion", ($osSelDire['dire_observacion']?$osSelDire['dire_observacion']:"Datos no disponibles") );
$MiTemplate->set_var("departamento", ($dirServicio['departamento']?$dirServicio['departamento']:"Datos no disponibles") );
$MiTemplate->set_var("ciudad", ($dirServicio['ciudad']?$dirServicio['ciudad']:"Datos no disponibles") );
$MiTemplate->set_var("comu_nombre", ($dirServicio['barrio']?$dirServicio['barrio']:"Datos no disponibles") );

$MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
$MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);
$MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));
$MiTemplate->set_var("TEXT_CAMPO_1", TEXT_CAMPO_1 );
$MiTemplate->set_var("TEXT_CAMPO_2", TEXT_CAMPO_2 );
$MiTemplate->set_var("TEXT_CAMPO_3", TEXT_CAMPO_3 );
$MiTemplate->set_var("TEXT_CAMPO_4", TEXT_CAMPO_4 );
$MiTemplate->set_var("TEXT_CAMPO_5", TEXT_CAMPO_5 );
$MiTemplate->set_var("TEXT_CAMPO_6", TEXT_CAMPO_6 );
$MiTemplate->set_var("TEXT_CAMPO_7", TEXT_CAMPO_7 );
$MiTemplate->set_var("TEXT_CAMPO_8", TEXT_CAMPO_8 );
$MiTemplate->set_var("TEXT_CAMPO_9", TEXT_CAMPO_9 );
$MiTemplate->set_var("TEXT_CAMPO_10", TEXT_CAMPO_10 );
$MiTemplate->set_var("TEXT_CAMPO_11", TEXT_CAMPO_11 );
$MiTemplate->set_var("TEXT_CAMPO_12", TEXT_CAMPO_12 );
$MiTemplate->set_var("TEXT_CAMPO_13", TEXT_CAMPO_13 );
$MiTemplate->set_var("TEXT_CAMPO_14", TEXT_CAMPO_14 );
$MiTemplate->set_var("TEXT_CAMPO_23", TEXT_CAMPO_23 );
$MiTemplate->set_var("TEXT_CAMPO_24", TEXT_CAMPO_24 );
$MiTemplate->set_var("TEXT_CAMPO_25", TEXT_CAMPO_25 );
$MiTemplate->set_var("TEXT_CAMPO_26", TEXT_CAMPO_26 );
$MiTemplate->set_var("TEXT_CAMPO_27", TEXT_CAMPO_27 );
$MiTemplate->set_var("TEXT_CAMPO_28", TEXT_CAMPO_28 );
$MiTemplate->set_var("TEXT_CAMPO_29", TEXT_CAMPO_29 );
$MiTemplate->set_var("TEXT_CAMPO_30", TEXT_CAMPO_30 );
$MiTemplate->set_var("TEXT_CAMPO_31", TEXT_CAMPO_31 );
$MiTemplate->set_var("TEXT_CAMPO_32", TEXT_CAMPO_32 );
$MiTemplate->set_var("TEXT_CAMPO_33", TEXT_CAMPO_33 );
$MiTemplate->set_var("TEXT_CAMPO_34", TEXT_CAMPO_34 );

// Agregamos el header
$MiTemplate->set_file("header","header_ident.html");

// Agregamos el main
$MiTemplate->set_file("main","lista_Regalos/edita_lista.htm");
        
$qnombre="select U.usr_nombres,U.usr_apellidos ,LO.cod_local from usuarios U inner join local_usr  as LU on (LU.usr_id=U.usr_id) join locales LO on (LO.id_local=LU.id_local)
where LU.id_local=".($local_id+0)." and U.usr_login='$USR_LOGIN'";
$rq = tep_db_query($qnombre);
$res = tep_db_fetch_array( $rq );
$nc=$res['usr_nombres']." ".$res['usr_apellidos'];
$codlocal=$res['cod_local'];
$MiTemplate->set_var("codlocal",$codlocal );

insertahistorial_ListaReg("Se edita la Lista de Regalos N°. ".$idLista.".", $USR_LOGIN, null, $idLista, null, $tipo = 'SYS');


// Agregamos el footer
include "../../includes/footer_cproy.php";

$MiTemplate->pparse("OUT_H", array("header"), true);
include "../../menu/menu.php";
$MiTemplate->parse("OUT_M", array("main","footer"), true);
$MiTemplate->p("OUT_M");

include "../../includes/application_bottom.php";
?>