<?php
// $pag_ini = '../monitor_cotizaciones/monitor_cotizaciones_00.php';
$SIN_PER = 1;
include "../../includes/aplication_top.php";

#include_idioma_mod( $ses_idioma, "nueva_cotizacion_00");
include_idioma_mod( $ses_idioma, "nueva_cotizacion_sumario_01");


// *************************************************************************************

/*Acciones********************/
$local_id = get_local_usr( $ses_usr_id );
$USR_LOGIN = get_login_usr( $ses_usr_id );

 /*  saca los nombre de la persona que ingreso los datos en la OS*/
    $qnombre="select U.usr_nombres,U.usr_apellidos from usuarios U inner join local_usr  as LU on (LU.usr_id=U.usr_id) where id_local=".($local_id+0)." and U.usr_login='$USR_LOGIN'";
    $osAtendido = tep_db_query($qnombre);
    $osAtendido = tep_db_fetch_array( $osAtendido );
    $nombre=$osAtendido['usr_nombres'];
    $apellido=$osAtendido['usr_apellidos'];
    $nc=$nombre." ".$apellido;

if ($accion=="dup") {
	//Esta acción debe ser transaccional
	tep_db_query("SET AUTOCOMMIT=0");

	$success = true;
	//Se duplica la os encabezado
	$queryins=" INSERT INTO os (id_estado,	id_proyecto, id_local, id_direccion, clie_rut, os_fechacreacion, os_fechacotizacion, os_fechaestimada, os_comentarios, os_descripcion,						  usuario,							   USR_ID,								os_fechaestimacion, origen ,USR_ORIGEN) 
				SELECT 'SA', id_proyecto, ".(get_local_usr( $ses_usr_id )+0).", id_direccion, clie_rut, now(),			 now(),				 os_fechaestimada, os_comentarios, CONCAT('(Copia de) ' ,os_descripcion), '".get_login_usr( $ses_usr_id )."', ".($ses_usr_id+0).", DATE_ADD(now(), INTERVAL ".(DIAS_VALID_COT + 0)." DAY), 'C'  ,'".get_login_origen( $ses_usr_id )."'
				FROM os
                WHERE id_os = ".($id_os+0) ; 
	$success = $success && tep_db_query($queryins);
	$ultimoID = tep_db_insert_id('');
	
	//Se duplica la os detalle
	$queryins=" INSERT INTO os_detalle (id_tipodespacho, id_os,				osde_tipoprod, osde_subtipoprod, osde_instalacion, osde_precio, osde_cantidad, osde_preciocosto, cod_sap, osde_especificacion, osde_descripcion, osde_descuento, id_producto,	   cod_barra, id_origen ) 
				SELECT id_tipodespacho, ".($ultimoID+0).", osde_tipoprod, osde_subtipoprod, osde_instalacion, prec_valor,  osde_cantidad, prec_costo,cod_sap, osde_especificacion, osde_descripcion, 0,			   od.id_producto, cod_barra, id_origen 
				FROM os_detalle od LEFT JOIN precios p ON od.cod_sap = p.cod_prod1 AND id_local = ".(get_local_usr( $ses_usr_id )+0)." 
				WHERE id_os = ".($id_os+0) ; 
	$success = $success && tep_db_query($queryins);

	insertahistorial("Se ha creado la OS $ultimoID como copia de la OS ".($id_os+0));
	insertahistorial("Se ha creado la OS $ultimoID como copia de la OS ".($id_os+0).".Esta OS ha quedado en estado En Trabajo", $ultimoID);

	//Fin de la transacción*/
	if ($success) {
		tep_db_query("commit");

		$fecha = date('d/m/Y');
		
		$consulta = "SELECT id_os_detalle, osde_tipoprod, osde_subtipoprod,  id_producto, cod_sap FROM os_detalle WHERE id_os = $ultimoID AND osde_tipoprod = 'PE'";		
		$resultado = tep_db_query($consulta);
		
		while($valor = tep_db_fetch_array($resultado))
		{				
		
		if($valor['osde_subtipoprod'] == 'PS')
		{
		$consulta_fecha = "SELECT numero_dias FROM ventaxvolumenps WHERE cod_sap = " . $valor['cod_sap'];
		$resultado_fecha = tep_db_query($consulta_fecha);
		$numero_dias = tep_db_fetch_array($resultado_fecha);		
		$os_fechaestimada = suma_fechas($fecha, $numero_dias[0]);	
		}	
		else{			
		$consulta_fecha = "SELECT numero_dias FROM fecha_entrega WHERE cod_producto = " . $valor['id_producto'];
		$resultado_fecha = tep_db_query($consulta_fecha);
		$numero_dias = tep_db_fetch_array($resultado_fecha);		
		$os_fechaestimada = suma_fechas($fecha, $numero_dias[0]);				
		}
		
		$consulta2 = "UPDATE os_detalle SET osde_fecha_entrega = '$os_fechaestimada' WHERE id_os_detalle = " . $valor['id_os_detalle'];		
		$resultado2 = tep_db_query($consulta2);
				
		}
		
		//Redireccionamos a la OS nueva
		header ('Location: nueva_cotizacion_sumario_01.php?id_os='.$ultimoID);
	}
	else
		tep_db_query("rollback");

	tep_db_query("SET AUTOCOMMIT=1");
}

/****************************/

/*para sacar el estadode la OS , activo o no los aref*/
$queryestado="select S.id_estado,E.esta_nombre from estados E inner join os S on (S.id_estado=E.id_estado) where S.id_os=".($id_os+0)." ";
$oses = tep_db_query($queryestado);
$oses = tep_db_fetch_array( $oses );

/******/
/*DATOS DEL CLIENTE*/
$querysel="SELECT S.os_cotizaciones_cruzadas, CL.clie_telefonocasa, CL.clie_telcontacto2, S.os_fechaestimacion,S.clie_rut, S.id_os,S.id_estado,S.id_local,S.os_descripcion,S.os_comentarios,S.id_proyecto,S.os_fechacotizacion, E.esta_nombre ,L.nom_local, P.proy_nombre, CL.clie_nombre, CL.clie_tipo,CL.clie_razonsocial,CL.clie_paterno,CL.clie_materno ,if('SM'=S.id_estado, '$pagar', '') 'pagar', if (date(os_fechaestimacion)>=date(now()), 1, 0) esfechavalida, if(S.id_local=".(get_local_usr( $ses_usr_id )+0).", 1, 0) eslocalcorrecto 
			FROM os S inner join estados E on (E.id_estado=S.id_estado) 
			inner join locales L on (L.id_local=S.id_local) 
			inner join proyectos P on (S.id_proyecto=P.id_proyecto) 
			inner join direcciones D on (S.id_direccion=D.id_direccion) 
			inner join clientes CL on (S.clie_rut=CL.clie_rut) where id_os=".($id_os+0);

$osSel = tep_db_query($querysel);
$osSel = tep_db_fetch_array( $osSel );


// Consulta los dirección del cliente 
$queryPRI = "SELECT  OS.id_direccion,OS.clie_rut,D.id_direccion,D.dire_nombre AS dire_nombre_pri ,D.dire_direccion AS dire_direccion_pri,D.id_direccion ,D.dire_defecto ,N.DESCRIPTION AS comu_nombre_pri ,D.dire_telefono AS dire_telefono_pri ,D.dire_observacion AS dire_observacion_pri 
			FROM os OS 
			INNER JOIN direcciones D ON (OS.clie_rut= D.clie_rut) 
			LEFT JOIN  cu_neighborhood N ON  (N.ID = D.id_comuna ) AND N.LOCATION = dire_localizacion
			WHERE OS.id_os = $id_os and D.dire_defecto='p' ";

$direna = tep_db_query($queryPRI);
$direna = tep_db_fetch_array( $direna );

// Consulta el Barrio-Localidad de los datos del cliente
$dirCli = consulta_localizacion($direna['clie_rut'],1);
$dirCliente = getlocalizacion($dirCli);

	
// Consulta la direccción de Servicio de la cotización.
$queryDir="SELECT O.id_direccion, D.dire_telefono, D.dire_direccion, D.dire_observacion
			FROM os O
			JOIN direcciones D ON D.id_direccion=O.id_direccion
			WHERE id_os = $id_os";

$osSelDir = tep_db_query($queryDir);
$osSelDire = tep_db_fetch_array( $osSelDir );

$dirServ = consulta_localizacion($osSelDire['id_direccion'],2);
$dirServicio = getlocalizacion($dirServ);


$pag_ini = '../nueva_cotizacion/nueva_cotizacion_sumario_01.php';

include_once('../../includes/aplication_top.php');
include_idioma_mod( $ses_idioma, "nueva_cotizacion_sumario_01");

$MiTemplate = new Template();
// asignamos degug maximo
$MiTemplate->debug = 0;
// root directory de los templates
$MiTemplate->set_root(DIRTEMPLATES);
// variables perdidas
$MiTemplate->set_unknowns("remove");

$MiTemplate->set_var("id_os",$id_os);
$MiTemplate->set_var("clie_rut",$clie_rut);
$MiTemplate->set_var("id_proyecto",$id_proyecto);
$MiTemplate->set_var("accion",$accion);

$MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
$MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);
$MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));
//$MiTemplate->set_var("TEXT",ver_contenido_editable( 1 ));

      $impre="javascript:popUpWindowModal('../nueva_cotizacion/printframe.php?id_os=$id_os', 50, 100, 696, 600);";
	  $impre_borra="javascript:popUpWindowModal('../nueva_cotizacion/printframe2.php?id_os=$id_os', 100, 100, 710, 500);";

    if (($osSel['id_estado']=='SI')||($osSel['id_estado']=='SA')){
        $MiTemplate->set_var("impre_borra",$impre_borra );
        $code='Imprimir Códigos de Barra';
        $MiTemplate->set_var("code",$code );
		$cod = "javascript:alert('No puede imprimir códigos de barra mientras la Cotización esté Pendiente/En Trabajo');";
        $MiTemplate->set_var("codigo",$cod);   
    }else{
        $MiTemplate->set_var("impre",$impre);
        $cod="javascript:popUpWindowModal('../nueva_cotizacion/printcbar.php?id_os=$id_os&dire_des={dire_des}', 100, 100, 710, 500);";
        $MiTemplate->set_var("code",'Imprimir Códigos de Barra');  
        $MiTemplate->set_var("codigo",$cod);        
        }
    if ($osSel['id_estado']=='SE'){
//        $pagar='Pagar Cotizaci&oacute;n';
		$pagar='<a href="#" onClick="Paga_Cot();">Pagar Cotizaci&oacute;n</a>';
        $MiTemplate->set_var("pagar",$pagar );
    }else{
        $pagar='Pagar Cotizaci&oacute;n';
        $MiTemplate->set_var("pagar",$pagar );
    }


     if (($osSel['id_estado']=='SA')||($osSel['id_estado']=='SI')){
        $editar='<a href="../nueva_cotizacion/nueva_cotizacion_03.php?accion=editar&id_os='.($id_os+0).'">Editar Cotizaci&oacute;n</a>';
        $duplica='Duplicar cotización';
    }else{
        $editar='Editar Cotizaci&oacute;n';
		if ($local_id)
        $duplica='<a href="#"  onClick="duplica();">Duplicar Cotizaci&oacute;n</a>';
		else 
        $duplica='<a href="#"  onClick="msg();">Duplicar Cotizaci&oacute;n</a>';
    }
        $MiTemplate->set_var("editar_estado",$editar );
        $MiTemplate->set_var("duplica",$duplica );

     $MiTemplate->set_var("telefono_celular", $osSel['clie_telcontacto2']);
     $MiTemplate->set_var("dire_telefono_pri",$osSel['clie_telefonocasa']);
     
     
     $MiTemplate->set_var("dire_direccion_pri",$direna['dire_direccion_pri']);
     $MiTemplate->set_var("comu_nombre_pri",$dirCliente['barrio']." - ".$dirCliente['localidad']);
     $MiTemplate->set_var("dire_observacion_pri",$direna['dire_observacion']);
     
     $MiTemplate->set_var("os_fechaestimacion",fecha_db2php($osSel['os_fechaestimacion']));
     $MiTemplate->set_var("esfechavalida",$osSel['esfechavalida']);
     $MiTemplate->set_var("eslocalcorrecto",$osSel['eslocalcorrecto']);
     $eslocalcorrecto= $osSel['eslocalcorrecto'];
     $MiTemplate->set_var("digito",$digito);
     $MiTemplate->set_var("clie_rut",$osSel['clie_rut']);
     $MiTemplate->set_var("id_estado",$osSel['id_estado']);
     $MiTemplate->set_var("esta_nombre",$osSel['esta_nombre']);
     $MiTemplate->set_var("nom_local",$osSel['nom_local']);
     $MiTemplate->set_var("os_cotizaciones_cruzadas",$osSel['os_cotizaciones_cruzadas']);
     $MiTemplate->set_var("os_comentarios",$osSel['os_comentarios']);
     $MiTemplate->set_var("os_fechacotizacion",fecha_db2php($osSel['os_fechacotizacion']));
     $MiTemplate->set_var("proy_nombre",$osSel['proy_nombre']);
     $MiTemplate->set_var("clie_nombre",$osSel['clie_nombre']);
     $MiTemplate->set_var("clie_paterno",$osSel['clie_paterno']);
     $MiTemplate->set_var("clie_materno",$osSel['clie_materno']);
     
      /*DIRECCIÓN DE SERVICO*/
     $MiTemplate->set_var("dire_direccion", $osSelDire['dire_direccion']);     
     $MiTemplate->set_var("dire_telefono", $osSelDire['dire_telefono']);
     $MiTemplate->set_var("dire_observacion", $osSelDire['dire_observacion']);
     $MiTemplate->set_var("departamento", $dirServicio['departamento']);
     $MiTemplate->set_var("ciudad", $dirServicio['ciudad']);
     $MiTemplate->set_var("comu_nombre",$dirServicio['barrio']." - ".$dirServicio['localidad']);
     /*####################*/
     

	if ($osSel['clie_tipo']=='e'){
     $MiTemplate->set_var("empresa","Empresa");
     $MiTemplate->set_var("clie_razonsocial",$osSel['clie_razonsocial']);	 
	}
/* para ver el tipo de estado*/
     $MiTemplate->set_var("nombre_estado",$osSel['esta_nombre']);
     $MiTemplate->set_var("id_estado_s",$osSel['id_estado']);
/*para los productos*/


$MiTemplate->set_var("NOMBRE_PAGINA",NOMBRE_PAGINA);
$MiTemplate->set_var("TEXT_CAMPO_1",TEXT_CAMPO_1);
$MiTemplate->set_var("TEXT_CAMPO_2",TEXT_CAMPO_2);
$MiTemplate->set_var("TEXT_CAMPO_3",TEXT_CAMPO_3);
$MiTemplate->set_var("TEXT_CAMPO_4",TEXT_CAMPO_4);
$MiTemplate->set_var("TEXT_CAMPO_5",TEXT_CAMPO_5);
$MiTemplate->set_var("TEXT_CAMPO_6",TEXT_CAMPO_6);
$MiTemplate->set_var("TEXT_CAMPO_7",TEXT_CAMPO_7);
$MiTemplate->set_var("TEXT_CAMPO_8",TEXT_CAMPO_8);
$MiTemplate->set_var("TEXT_CAMPO_9",TEXT_CAMPO_9);
$MiTemplate->set_var("TEXT_CAMPO_10",TEXT_CAMPO_10);

$MiTemplate->set_var("TEXT_CAMPO_11",TEXT_CAMPO_11);
$MiTemplate->set_var("TEXT_CAMPO_12",TEXT_CAMPO_12);
$MiTemplate->set_var("TEXT_CAMPO_13",TEXT_CAMPO_13);
$MiTemplate->set_var("TEXT_CAMPO_14",TEXT_CAMPO_14);
$MiTemplate->set_var("TEXT_CAMPO_15",TEXT_CAMPO_15);
$MiTemplate->set_var("TEXT_CAMPO_16",TEXT_CAMPO_16);
$MiTemplate->set_var("TEXT_CAMPO_17",TEXT_CAMPO_17);
$MiTemplate->set_var("TEXT_CAMPO_18",TEXT_CAMPO_18);
$MiTemplate->set_var("TEXT_CAMPO_19",TEXT_CAMPO_19);
$MiTemplate->set_var("TEXT_CAMPO_20",TEXT_CAMPO_20);
$MiTemplate->set_var("TEXT_CAMPO_21",TEXT_CAMPO_21);
$MiTemplate->set_var("TEXT_CAMPO_22",TEXT_CAMPO_22);
$MiTemplate->set_var("TEXT_CAMPO_23",TEXT_CAMPO_23);
$MiTemplate->set_var("TEXT_CAMPO_24",TEXT_CAMPO_24);




// Agregamos el header
$MiTemplate->set_file("header","header_ident.html");

// Agregamos el main
$MiTemplate->set_file("main","nueva_coti/nueva_cotizacion_sumario_01.htm");

/* para los productos*/
$MiTemplate->set_file("productos","nueva_cotizacion/os_detalle.htm");

/* para el nombre del que atendio*/
$qnombre="select U.USR_NOMBRES, U.USR_APELLIDOS  from usuarios U inner join os OS on (OS.usr_id=U.usr_id) where id_os=".($id_os+0)."";
$osAtendido = tep_db_query($qnombre);
$osAtendido = tep_db_fetch_array( $osAtendido );
$MiTemplate->set_var('atendido', $osAtendido['USR_NOMBRES'] . " " . $osAtendido['USR_APELLIDOS']);

/* para los productos*/
$instn="No";
$insts="Sí";
$precioVacio=" - ";
$totalVacio=" 0 ";
$total0='0';
$especificacion="<br> <u>Especificaciones</u>:&nbsp;";
$notienedespacho=0;

/* Validar Precios Bajos */
/*Para obtener el estadode la OS , activo o no los aref*/
$queryestado="select  S.id_estado,E.esta_nombre from estados E inner join os S on (S.id_estado=E.id_estado) where S.id_os=".($id_os+0)." ";
$oses = tep_db_query($queryestado);
$oses = tep_db_fetch_array( $oses );

	//Obtiene el estado de la Cotizacion si esta en cotizacion Puede ingresar al proceso de verificacion de menor precio.
	$MiTemplate->set_block("main", "Precio_Total", "PBLModulos");
	if($oses[id_estado] == 'SE'){
	
	//Obtiene la fecha de creacion de la cotizacion .
	$query_fecha_coti = "Select os_fechaestimacion From os Where id_os=".($id_os+0)."";
	$rq = tep_db_query($query_fecha_coti);
	$res = tep_db_fetch_array( $rq );
	
	$fecha_coti= $res['os_fechaestimacion'];
	$fecha_act= (date(("Y-m-d").' '."h:m:s"));

	//Compara fechas para verificar cotizacion en el rango valido para dar menor precio.
	$valido = '';
	$coti_valida = (compara_fecha($fecha_coti, $fecha_act, $valido));

	if($coti_valida == 1 && $eslocalcorrecto == 1 ){
		$MiTemplate->set_var('pruebas', "Usted Puede obtener precios bajos, esta en Estado COTI Y ENTRE LOS 3 DIAS");
		/* Precios y total parciado aplicando menor valor a Productos*/
	    $query_OD="select DISTINCT (P.peso/1000) AS peso, OD.osde_fecha_entrega, OD.osde_especificacion, (OD.cod_sap+0) as cod_sap,OD.cod_barra,OD.osde_tipoprod,OD.osde_descripcion,OD.osde_cantidad,OD.id_tipodespacho,
						 OD.osde_instalacion, TD.nombre as tipo_nombre ,OD.osde_descuento, OD.id_os_detalle, PR.prec_valor, OD.osde_precio,
						 if(osde_descuento is not null, osde_descuento, 0) 'osde_descuento', 
						 if (osde_descuento<>0,ROUND((osde_precio-osde_descuento)*osde_cantidad),ROUND(osde_precio*osde_cantidad))'Total', 
						 if (OD.osde_tipoprod='SV', '-', TD.nombre) 'tipo_nombre', 
						 if( ((osde_precio-osde_descuento)*osde_cantidad) is Null,' 0 ','') 'totalVacio', 
						 if (OD.osde_tipoprod='SV', ' - ', 
						 if (OD.osde_instalacion, 'SI', 'NO')) 'osde_instalacion', 
						 if(OD.osde_precio is null, ' - ', '') 'precioVacio' ,if(OD.osde_especificacion <>'', 'Especificaciones: ', '') 'especificacion' 
					FROM os_detalle OD inner join tipos_despacho TD on (TD.id_tipodespacho=OD.id_tipodespacho) 
					LEFT JOIN productos P ON OD.cod_sap = P.cod_prod1
					LEFT JOIN precios PR ON   PR.cod_prod1 = OD.cod_sap AND PR.id_local = '".$local_id."'
					where id_os=".($id_os+0)."  order by OD.id_os_detalle desc";
	    if ( $rq = tep_db_query($query_OD) ){
	            while( $res = tep_db_fetch_array( $rq ) ) {
	            
				/* Verificamos  si el precio más bajo corresponde a la OS, o al cargado en precios */
	            if($res['osde_precio'] >= $res['prec_valor']){
	            	$new_Precio= $res['prec_valor'];
	            	$new_Total= ($res['prec_valor'] * $res['osde_cantidad']);
	                $idOS_detalle= $res['id_os_detalle'];
	                
	                if($res['osde_tipoprod'] == 'PS')
	                {
	                $queryOD ="UPDATE os_detalle OD INNER JOIN productos P ON OD.id_producto = P.id_producto 
	       						SET osde_precio='".$new_Precio."' where id_os_detalle=".($idOS_detalle+0)."";
   		   			tep_db_query($queryOD);    
	                }
	                
	            	if($res['osde_tipoprod'] == 'PS')
	                {
	   		   		$MiTemplate->set_var('osde_precio', formato_precio($new_Precio));
	                $MiTemplate->set_var('Total', formato_precio($new_Total));
	                }
	                else{
	                	$MiTemplate->set_var('osde_precio', formato_precio($res['osde_precio']));
	                	$MiTemplate->set_var('Total', formato_precio($res['Total']));
	                }
	                	   		   		
		            $MiTemplate->set_var('precioVacio', '');	       		            
		            $MiTemplate->set_var('totalVacio', '');   

		            writelog("Se han validado correctamente precios bajos para la Cotizacion". ($idOS_detalle+0). " aplicando los precios más bajos.");
	            }         	
		        else{
			        if ($res['osde_precio']==''){
		                $MiTemplate->set_var('precioVacio', $precioVacio);
		                $MiTemplate->set_var('osde_precio', '');
		            }else{
		                $MiTemplate->set_var('osde_precio', formato_precio($res['osde_precio']));
		                $MiTemplate->set_var('precioVacio', '');
		            }
		            if ($res['Total']==''){
		                $MiTemplate->set_var('totalVacio', $totalVacio);
		                $MiTemplate->set_var('Total', '');
		            }else{
		                $MiTemplate->set_var('totalVacio', '');
		                $MiTemplate->set_var('Total', formato_precio($res['Total']));
		            }   
		        } 
	      
	                $MiTemplate->set_var('tipo_nombre', $res['tipo_nombre']);
	                $MiTemplate->set_var('osde_instalacion', $res['osde_instalacion']);
	                $MiTemplate->set_var('osde_descripcion', $res['osde_descripcion']);                
	                $MiTemplate->set_var('cod_barra', $res['cod_barra']);
	                $MiTemplate->set_var('cod_sap', $res['cod_sap']);
	                $MiTemplate->set_var('osde_tipoprod', $res['osde_tipoprod']);
	                $MiTemplate->set_var('osde_fecha_entrega', $res['osde_fecha_entrega']);
	                $MiTemplate->set_var('peso', round($res['peso'],DECIMALES_PESO));
	                $MiTemplate->set_var('especificacion', $res['especificacion']);
	                $MiTemplate->set_var('osde_especificacion', $res['osde_especificacion']);
	                $MiTemplate->set_var('osde_cantidad', $res['osde_cantidad']);
	                $MiTemplate->parse("PBLModulos", "Precio_Total", true);            
	        }       
	    }
	}
	
	// Si el estado es "Cotizacion" y la cotizacion NO esta en el rango de "Fechas validas", aplica el valor actual del precio a los productos
	else if($coti_valida == 0 && $eslocalcorrecto == 1 ){		
		
		$MiTemplate->set_var('pruebas', "Usted No Puede obtener precios bajos, esta en Estado COTI, PERO CON COTIZACION VENCIDA");
		/* Precios y total parciado aplicando menor valor a Productos*/
	    $query_OD="select DISTINCT (P.peso/1000) AS peso, OD.osde_fecha_entrega, OD.osde_especificacion, (OD.cod_sap+0) as cod_sap,OD.cod_barra,OD.osde_tipoprod,OD.osde_descripcion,OD.osde_cantidad,OD.id_tipodespacho,
						 OD.osde_instalacion, TD.nombre as tipo_nombre ,OD.osde_descuento, OD.id_os_detalle, PR.prec_valor, OD.osde_precio,
						 if(osde_descuento is not null, osde_descuento, 0) 'osde_descuento', 
						 if (osde_descuento<>0,ROUND((osde_precio-osde_descuento)*osde_cantidad),ROUND(osde_precio*osde_cantidad))'Total', 
						 if (OD.osde_tipoprod='SV', '-', TD.nombre) 'tipo_nombre', 
						 if( ((osde_precio-osde_descuento)*osde_cantidad) is Null,' 0 ','') 'totalVacio', 
						 if (OD.osde_tipoprod='SV', ' - ', 
						 if (OD.osde_instalacion, 'SI', 'NO')) 'osde_instalacion', 
						 if(OD.osde_precio is null, ' - ', '') 'precioVacio' ,if(OD.osde_especificacion <>'', 'Especificaciones: ', '') 'especificacion' 
					FROM os_detalle OD inner join tipos_despacho TD on (TD.id_tipodespacho=OD.id_tipodespacho) 
					LEFT JOIN productos P ON OD.cod_sap = P.cod_prod1
					LEFT JOIN precios PR ON   PR.cod_prod1 = OD.cod_sap AND PR.id_local = '".$local_id."'
					where id_os=".($id_os+0)."  order by OD.id_os_detalle desc";
	    	    
	     
	    if ( $rq = tep_db_query($query_OD) ){
	            while( $res = tep_db_fetch_array( $rq ) ) {
				/* Cargamos el preci correspondiente al precio actual del producto */
	            	$new_Precio= $res['prec_valor'];
	            	$new_Total= ($res['prec_valor'] * $res['osde_cantidad']);
	                $idOS_detalle= $res['id_os_detalle'];
	                
	                if($res['osde_tipoprod'] == 'PS')
	                {
	                $queryOD ="UPDATE os_detalle OD INNER JOIN productos P ON OD.id_producto = P.id_producto 
	       						SET osde_precio='".$new_Precio."' where id_os_detalle=".($idOS_detalle+0)."";
   		   			tep_db_query($queryOD);
	                }    
					
	                
	   		   		$MiTemplate->set_var('osde_precio', formato_precio($new_Precio));
		            $MiTemplate->set_var('precioVacio', '');	       
		            $MiTemplate->set_var('Total', formato_precio($new_Total));
		            $MiTemplate->set_var('totalVacio', '');   

		            writelog("Se han validado correctamente precios bajos para la Cotizacion" .($idOS_detalle+0). ", aplicando los precios actuales de los productos, ya que la cotizacion está vencida.");         	
			        if ($res['prec_valor']==''){
			        if($res['osde_tipoprod'] == 'PS')
		                {
		   		   		$MiTemplate->set_var('osde_precio', formato_precio($res['prec_valor']));		                
		                }
		                else{
		                	$MiTemplate->set_var('osde_precio', formato_precio($res['osde_precio']));		                	
		                }			        				        	
		                
		            }else{
		            	if($res['osde_tipoprod'] == 'PS')
		                {
		   		   		$MiTemplate->set_var('osde_precio', formato_precio($res['prec_valor']));		                
		                }
		                else{
		                	$MiTemplate->set_var('osde_precio', formato_precio($res['osde_precio']));		                	
		                }		                
		                $MiTemplate->set_var('precioVacio', '');
		            }
		            
		            
		            if ($res['Total']==''){
		            	if($res['osde_tipoprod'] == 'PS')
		                {		   		   		
		                $MiTemplate->set_var('Total', formato_precio($totalVacio));
		                }
		                else{		                	
		                	$MiTemplate->set_var('Total', formato_precio($res['Total']));
		                }		                
		                $MiTemplate->set_var('Total', '');
		            }else{
		            	if($res['osde_tipoprod'] == 'PS')
		                {		   		   		
		                $MiTemplate->set_var('Total', formato_precio($res['Total']));
		                }
		                else{		                	
		                	$MiTemplate->set_var('Total', formato_precio($res['Total']));
		                }
		                $MiTemplate->set_var('totalVacio', '');
		                //$MiTemplate->set_var('Total', formato_precio($res['Total']));
		            }   
	      
	                $MiTemplate->set_var('tipo_nombre', $res['tipo_nombre']);
	                $MiTemplate->set_var('osde_instalacion', $res['osde_instalacion']);
	                $MiTemplate->set_var('osde_descripcion', $res['osde_descripcion']);                
	                $MiTemplate->set_var('cod_barra', $res['cod_barra']);
	                $MiTemplate->set_var('cod_sap', $res['cod_sap']);
	                $MiTemplate->set_var('osde_tipoprod', $res['osde_tipoprod']);
	                $MiTemplate->set_var('osde_fecha_entrega', $res['osde_fecha_entrega']);
	                $MiTemplate->set_var('peso', round($res['peso'],DECIMALES_PESO));
	                $MiTemplate->set_var('especificacion', $res['especificacion']);
	                $MiTemplate->set_var('osde_especificacion', $res['osde_especificacion']);
	                $MiTemplate->set_var('osde_cantidad', $res['osde_cantidad']);
	                $MiTemplate->parse("PBLModulos", "Precio_Total", true);            
	        }       
	    } 
	}
	
 	else{
		$MiTemplate->set_var('pruebas', "Usted no Puede obtener precios bajos, esta en Estado COTI PERO MAYOR A 3 DIAS");
		/* Precios y total parciado sin aplicar menor valor a Productos*/
	    $query_OD="select DISTINCT (P.peso/1000) AS peso, OD.osde_fecha_entrega, OD.osde_especificacion, (OD.cod_sap+0)  as cod_sap,OD.cod_barra,OD.osde_tipoprod,OD.osde_descripcion,OD.osde_cantidad,OD.id_tipodespacho,OD.osde_instalacion,OD.osde_precio,TD.nombre as tipo_nombre ,OD.osde_descuento,
				    if(osde_descuento is not null, osde_descuento, 0) 'osde_descuento',
				    if (osde_descuento<>0,ROUND((osde_precio-osde_descuento)*osde_cantidad),ROUND(osde_precio*osde_cantidad))'Total',
				    if (OD.osde_tipoprod='SV', '-', TD.nombre) 'tipo_nombre',  
				    if( ((osde_precio-osde_descuento)*osde_cantidad) is Null,'$totalVacio','') 'totalVacio',
				    if (OD.osde_tipoprod='SV', ' - ',  if (OD.osde_instalacion, 'SI', 'NO')) 'osde_instalacion',
				    if(OD.osde_precio is null, '$precioVacio', '') 'precioVacio' ,if(OD.osde_especificacion <>'', '$especificacion', '') 'especificacion' 
				    FROM os_detalle OD inner join tipos_despacho TD on (TD.id_tipodespacho=OD.id_tipodespacho) 
				    LEFT JOIN productos P ON  OD.cod_sap = P.cod_prod1
				    where id_os=".($id_os+0)." AND OD.osde_tipoprod = 'PS'  order by OD.id_os_detalle desc ";
				    
		writelog("No se han validado precios bajos para la Orden de Trabajo" .($idOS_detalle+0). ", ya que no esta en estado cotizacion.");
	    if ( $rq = tep_db_query($query_OD) ){
	            while( $res = tep_db_fetch_array( $rq ) ) {
	            if ($res['osde_precio']==''){
	                $MiTemplate->set_var('precioVacio', $precioVacio);
	                $MiTemplate->set_var('osde_precio', '');
	            }else{
	                $MiTemplate->set_var('osde_precio', formato_precio($res['osde_precio']));
	                $MiTemplate->set_var('precioVacio', '');
	            }
	            if ($res['Total']==''){
	                $MiTemplate->set_var('totalVacio', $totalVacio);
	                $MiTemplate->set_var('Total', '');
	            }else{
	                $MiTemplate->set_var('totalVacio', '');
	                $MiTemplate->set_var('Total', formato_precio($res['Total']));
	            }
	                $MiTemplate->set_var('tipo_nombre', $res['tipo_nombre']);
	                $MiTemplate->set_var('osde_instalacion', $res['osde_instalacion']);
	                $MiTemplate->set_var('osde_descripcion', $res['osde_descripcion']);                
	                $MiTemplate->set_var('cod_barra', $res['cod_barra']);
	                $MiTemplate->set_var('cod_sap', $res['cod_sap']);
	                $MiTemplate->set_var('osde_tipoprod', $res['osde_tipoprod']);
	                $MiTemplate->set_var('osde_fecha_entrega', $res['osde_fecha_entrega']);
	                $MiTemplate->set_var('peso', round($res['peso'],DECIMALES_PESO));
	                $MiTemplate->set_var('especificacion', $res['especificacion']);
	                $MiTemplate->set_var('osde_especificacion', $res['osde_especificacion']);
	                $MiTemplate->set_var('osde_cantidad', $res['osde_cantidad']);
	                $MiTemplate->parse("PBLModulos", "Precio_Total", true);   	               
	        }           
	    }
	  }  
	 }
	else{ 
		$MiTemplate->set_var('pruebas', "Usted no Puede obtener precios bajos, No esta en Estado COTI PERO MAYOR A 3 DIAS");
		/* Precios y total parciado sin aplicar menor valor a Productos*/
	    $query_OD="select DISTINCT (P.peso/1000) AS peso, OD.osde_fecha_entrega, OD.osde_especificacion, (OD.cod_sap+0)  as cod_sap,OD.cod_barra,OD.osde_tipoprod,OD.osde_descripcion,OD.osde_cantidad,OD.id_tipodespacho,OD.osde_instalacion,OD.osde_precio,TD.nombre as tipo_nombre ,OD.osde_descuento,
				    if(osde_descuento is not null, osde_descuento, 0) 'osde_descuento',
				    if (osde_descuento<>0,ROUND((osde_precio-osde_descuento)*osde_cantidad),ROUND(osde_precio*osde_cantidad))'Total',
				    if (OD.osde_tipoprod='SV', '-', TD.nombre) 'tipo_nombre',  
				    if( ((osde_precio-osde_descuento)*osde_cantidad) is Null,'$totalVacio','') 'totalVacio',
				    if (OD.osde_tipoprod='SV', ' - ',  if (OD.osde_instalacion, 'SI', 'NO')) 'osde_instalacion',
				    if(OD.osde_precio is null, '$precioVacio', '') 'precioVacio' ,if(OD.osde_especificacion <>'', '$especificacion', '') 'especificacion' 
				    FROM os_detalle OD inner join tipos_despacho TD on (TD.id_tipodespacho=OD.id_tipodespacho) 
				    LEFT JOIN productos P ON  OD.cod_sap = P.cod_prod1
				    where id_os=".($id_os+0)." order by OD.id_os_detalle desc ";
				    
		writelog("No se han validado precios bajos para la Orden de Trabajo" .($idOS_detalle+0). ", ya que no esta en estado cotizacion.");	
	    if ( $rq = tep_db_query($query_OD) ){
	            while( $res = tep_db_fetch_array( $rq ) ) {
	            if ($res['osde_precio']==''){
	                $MiTemplate->set_var('precioVacio', $precioVacio);
	                $MiTemplate->set_var('osde_precio', '');
	            }else{
	                $MiTemplate->set_var('osde_precio', formato_precio($res['osde_precio']));
	                $MiTemplate->set_var('precioVacio', '');
	            }
	            if ($res['Total']==''){
	                $MiTemplate->set_var('totalVacio', $totalVacio);
	                $MiTemplate->set_var('Total', '');
	            }else{
	                $MiTemplate->set_var('totalVacio', '');
	                $MiTemplate->set_var('Total', formato_precio($res['Total']));
	            }
	                $MiTemplate->set_var('tipo_nombre', $res['tipo_nombre']);
	                $MiTemplate->set_var('osde_instalacion', $res['osde_instalacion']);
	                $MiTemplate->set_var('osde_descripcion', $res['osde_descripcion']);                
	                $MiTemplate->set_var('cod_barra', $res['cod_barra']);
	                $MiTemplate->set_var('cod_sap', $res['cod_sap']);
	                $MiTemplate->set_var('osde_tipoprod', $res['osde_tipoprod']);
	                $MiTemplate->set_var('osde_fecha_entrega', $res['osde_fecha_entrega']);
	                $MiTemplate->set_var('peso', round($res['peso'],DECIMALES_PESO));
	                $MiTemplate->set_var('especificacion', $res['especificacion']);
	                $MiTemplate->set_var('osde_especificacion', $res['osde_especificacion']);
	                $MiTemplate->set_var('osde_cantidad', $res['osde_cantidad']);
	                $MiTemplate->parse("PBLModulos", "Precio_Total", true);   	               
	        }           
	    }
	} 

    /*para obtener el total*/
      $query_TG = "select osde_precio,osde_cantidad, if(osde_descuento is not null, osde_descuento, 0) 'osde_descuento' ,if (osde_descuento<>0,ROUND((osde_precio-osde_descuento)*osde_cantidad),ROUND(osde_precio*osde_cantidad))'Total' from os_detalle where id_os=".($id_os+0)." ";
     
      $suma=0;
      $vari='si';
        if ( $rq = tep_db_query($query_TG) ){
            while( $res = tep_db_fetch_array( $rq ) ) {
            $suma=$suma+$res['Total'];
            }
          
        }
       $MiTemplate->set_var('TG',formato_precio($suma));

// Agregamos el footer
include "../../includes/footer_cproy.php";

$MiTemplate->pparse("OUT_H", array("header"), true);
include "../../menu/menu.php";


$MiTemplate->parse("OUT_M", array("main","footer"), true);
$MiTemplate->p("OUT_M");

include "../../includes/application_bottom.php";
?>
