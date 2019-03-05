<?php
$pag_ini = '../nueva_cotizacion/nueva_cotizacion_00.php';
include "../../includes/aplication_top.php";
include "activewidgets.php";

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

$_SESSION['liqTrans'] = 0;
$_SESSION['permiso_cruce_coti'] = 0;

include_idioma_mod( $ses_idioma, "nueva_cotizacion_03" );
//include_idioma_mod( $ses_idioma, "monitor_cotizaciones" );

//echo "Cliente:".$clie_rut."<br>";
file_put_contents('cliente03rut.txt', $clie_rut);


$query_proyecto = "SELECT clie_rut FROM proyectos WHERE clie_rut = $clie_rut";
$proyecto = tep_db_query($query_proyecto);
$cedula = tep_db_fetch_array($proyecto);

if($cedula['clie_rut'] == $clie_rut)
{
}
else{
$query_proyecto = "INSERT INTO proyectos(clie_rut, proy_nombre) VALUES ($clie_rut, 'PRIMER PROYECTO')";
$proyecto = tep_db_query($query_proyecto);
}
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
$queryest="select id_estado from os WHERE id_os = " . ($id_os+0);
    $esta = tep_db_query($queryest);
    $esta = tep_db_fetch_array( $esta );

$local_id = get_local_usr( $ses_usr_id );
$USR_LOGIN = get_login_usr( $ses_usr_id );

/*si tiene os verifica estados*/
    if ($id_os){
        if (($esta['id_estado']=='SA')||($esta['id_estado']=='SI')){
        }
        else{
            header ('Location: nueva_cotizacion_04.php?id_os='.($id_os+0));
            tep_exit();
        }
    }

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

			if (($res['osde_tipoprod']=='PS' || $res['osde_tipoprod']=='PE') && ($res['nombre']=='D. Programado' || $res['nombre']=='Express' || $res['nombre']=='Prov.Des Domicilio'))
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
        file_put_contents('insertIOS.txt',$queryIOS);
		echo $queryIOS, "<br><br><br><br><br><br><br>";

		$success = $success && tep_db_query($queryIOS);

        $ultimoID = tep_db_insert_id('');
		insertahistorial("Se creó OS $ultimoID en estado En Trabajo", $ultimoID);

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

// Agregamos el header
$MiTemplate->set_file("header","header_ident.html");

// Agregamos el main
$MiTemplate->set_file("main","nueva_cotizacion/nueva_cotizacion_03.htm");

$qnombre="select U.usr_nombres,U.usr_apellidos ,LO.cod_local from usuarios U inner join local_usr  as LU on (LU.usr_id=U.usr_id) join locales LO on (LO.id_local=LU.id_local)
where LU.id_local=".($local_id+0)." and U.usr_login='$USR_LOGIN'";
$rq = tep_db_query($qnombre);
$res = tep_db_fetch_array( $rq );
$nc=$res['usr_nombres']." ".$res['usr_apellidos'];
$codlocal=$res['cod_local'];
$MiTemplate->set_var("codlocal",$codlocal );


$query = "SELECT * FROM os WHERE id_os = " . ($id_os+0);
query_to_set_var( $query, $MiTemplate, 0, '', '' );

$query = "SELECT * FROM os WHERE id_os = " . ($id_os+0);
$rq_1 = tep_db_query($query);
$res_1 = tep_db_fetch_array( $rq_1 );
$MiTemplate->set_var("os_fechaestimada",fecha_db2php($res_1['os_fechaestimada']));

$MiTemplate->set_var("IDOS",$id_os);
$MiTemplate->set_var("USR_LOCAL",get_local_usr( $ses_usr_id ));
/*para los proyectos*/
$query_1 = "SELECT clie_rut FROM os WHERE id_os = " . ($id_os+0);
$rq_1 = tep_db_query($query_1);
$res_1 = tep_db_fetch_array( $rq_1 );

    $MiTemplate->set_block("main", "Proyectos", "BLO_proy");
    $queryP = "select id_proyecto, proy_nombre ,if('$id_proyecto'=id_proyecto, 'selected', '') 'selected' from proyectos where clie_rut=".($clie_rut+0)." order by id_proyecto desc";
    if ($id_os)
        $queryP = "SELECT p.id_proyecto, p.proy_nombre, if(os.id_proyecto, 'selected', '') selected FROM proyectos p LEFT JOIN os ON os.id_proyecto = p.id_proyecto	and os.id_os = $id_os where p.clie_rut = " . $res_1['clie_rut'] . " order by id_proyecto desc";
    query_to_set_var( $queryP, $MiTemplate, 1, 'BLO_proy', 'Proyectos' );

/*para los tipos de despacho*/
$MiTemplate->set_block("main", "Despacho", "BLO_desp");
$queryP = "select * from tipos_despacho WHERE id_tipodespacho <> 4 order by id_tipodespacho ";
query_to_set_var( $queryP, $MiTemplate, 1, 'BLO_desp', 'Despacho' );

// Agregamos el footer
include "../../includes/footer_cproy.php";

$MiTemplate->pparse("OUT_H", array("header"), true);
include "../../menu/menu.php";
$MiTemplate->parse("OUT_M", array("main","footer"), true);
$MiTemplate->p("OUT_M");

include "../../includes/application_bottom.php";
?>