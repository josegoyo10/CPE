<?php
//$pag_ini = '../nueva_cotizacion/nueva_cotizacion_00.php';
$SIN_PER = 1;
include ("../../includes/aplication_top.php");
include "activewidgets.php";

// include_idioma_mod( $ses_idioma, "nueva_cotizacion_01" );

// *************************************************************************************

/*include_idioma_mod( $ses_idioma, "start" );*/

/* acciones*/

$querysel="select CONCAT(NE.DESCRIPTION, ' - ',  LO.DESCRIPTION) AS comu_nombre, CL.clie_telcontacto2, CI.DESCRIPTION AS nombre_ciudad, DE.DESCRIPTION AS nombre_departamento, S.os_cotizaciones_cruzadas, CL.clie_tipo,CL.clie_razonsocial,S.os_fechaestimacion,CL.clie_rut, CL.clie_reteica, CL.clie_fuente, CL.clie_tipo_contribuyente, S.os_descripcion,S.os_comentarios ,S.id_os ,S.clie_rut ,CL.clie_nombre,CL.clie_paterno,CL.clie_materno,CL.clie_telefonocasa,L.nom_local,S.os_fechacotizacion ,E.esta_nombre,P.proy_nombre,D.dire_direccion,D.dire_observacion,D.dire_telefono, cod_local_pos 
from os S inner join clientes CL on (CL.clie_rut=S.clie_rut) 
Inner join locales L on (L.id_local=S.id_local) 
inner join estados E on (S.id_estado=E.id_estado) 
inner join proyectos P on (P.id_proyecto=S.id_proyecto) 
inner join direcciones D on (D.id_direccion=S.id_direccion) 
LEFT JOIN cu_department DE ON DE.ID = D.id_departamento 
LEFT JOIN cu_city CI ON CI.ID = D.id_ciudad AND CI.ID = ( SELECT id_ciudad FROM direcciones WHERE id_direccion = ( SELECT id_direccion FROM os WHERE id_os = $id_os) ) 
AND CI.ID_PROVINCE = ( SELECT id_provincia FROM direcciones WHERE id_direccion = ( SELECT id_direccion FROM os WHERE id_os = $id_os) )
AND CI.ID_DEPARTMENT = ( SELECT id_departamento FROM direcciones WHERE id_direccion = ( SELECT id_direccion FROM os WHERE id_os = $id_os) )
LEFT JOIN cu_locality LO ON  LO.ID =  D.id_localidad AND LO.ID_CITY = ( SELECT id_ciudad FROM direcciones WHERE id_direccion = ( SELECT id_direccion FROM os WHERE id_os = $id_os) ) 
AND LO.ID_PROVINCE = ( SELECT id_provincia FROM direcciones WHERE id_direccion = ( SELECT id_direccion FROM os WHERE id_os = $id_os) )
AND LO.ID_DEPARTMENT = ( SELECT id_departamento FROM direcciones WHERE id_direccion = ( SELECT id_direccion FROM os WHERE id_os = $id_os) )
LEFT JOIN cu_neighborhood  NE  ON NE.LOCATION = D.dire_localizacion AND NE.LOCATION = ( SELECT dire_localizacion FROM direcciones WHERE id_direccion = ( SELECT id_direccion FROM os WHERE id_os = $id_os) )
WHERE id_os = $id_os ";

$osSel = tep_db_query($querysel);
$osSel = tep_db_fetch_array( $osSel );

$clie_tipo_contribuyente = $osSel['clie_tipo_contribuyente'];
$clie_cedula = $osSel['clie_rut'];
$clie_fuente = $osSel['clie_fuente'];
$clie_reteica = $osSel['clie_reteica'];


/* si viene SIN direccion de despacho , como en sumario*/
    $querydiredes=  "SELECT D.dire_direccion AS dire_direccion_des, D.dire_observacion AS dire_observacion_des, D.dire_telefono AS dire_telefono_des, NE.DESCRIPTION  AS comu_nombre_des 
    				FROM direcciones D 
    				LEFT JOIN cu_neighborhood  NE  ON NE.LOCATION = D.dire_localizacion          
    				WHERE D.dire_defecto='p' AND clie_rut = $clie_cedula";    
    
    	
    $diredes = tep_db_query($querydiredes);
    $diredes = tep_db_fetch_array( $diredes );    


/* si viene con una direccionde despacho, se da en al caso de cerra e imprimir la cotizacion*/

    
if($dire_des){
    $querydiredes="select D.dire_direccion as dire_direccion_des,D.dire_observacion as dire_observacion_des,D.dire_telefono as dire_telefono_des,CO.comu_nombre as comu_nombre_des from direcciones D inner join comuna CO on (D.id_comuna=CO.id_comuna) where D.id_direccion =".($dire_des+0)."";
    $diredes = tep_db_query($querydiredes);
    $diredes = tep_db_fetch_array( $diredes );
}


/***********************/


$MiTemplate = new Template();
// asignamos degug maximo
$MiTemplate->debug = 0;
// root directory de los templates
$MiTemplate->set_root(DIRTEMPLATES);
// variables perdidas
$MiTemplate->set_unknowns("remove");

$MiTemplate->set_var("id_os",$id_os);
$MiTemplate->set_var("dire_des",$dire_des);

$MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
$MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);
$MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));
	 if ($osSel['clie_tipo']=='e'){
     $MiTemplate->set_var("empresa","Empresa");
     $MiTemplate->set_var("clie_razonsocial",$osSel['clie_razonsocial']);	 
	 }
     
     $MiTemplate->set_var("os_fechaestimacion",fecha_db2php($osSel['os_fechaestimacion']));
     $MiTemplate->set_var("esta_nombre",$osSel['esta_nombre']);
     $MiTemplate->set_var("nom_local",$osSel['nom_local']);
     	$MiTemplate->set_var("os_cotizaciones_cruzadas",$osSel['os_cotizaciones_cruzadas']);     	
     $MiTemplate->set_var("os_descripcion",$osSel['os_descripcion']);
     $MiTemplate->set_var("os_comentarios",$osSel['os_comentarios']);
     $MiTemplate->set_var("os_fechacotizacion",fecha_db2php($osSel['os_fechacotizacion']));
     $MiTemplate->set_var("proy_nombre",$osSel['proy_nombre']);
     $MiTemplate->set_var("clie_nombre",$osSel['clie_nombre']);
     $MiTemplate->set_var("clie_telefonocasa",$osSel['clie_telefonocasa']);
     $MiTemplate->set_var("telefono_celular",$osSel['clie_telcontacto2']);
     
     
     $MiTemplate->set_var("clie_paterno",$osSel['clie_paterno']);
     $MiTemplate->set_var("clie_materno",$osSel['clie_materno']);
     $MiTemplate->set_var("clie_rut",$osSel['clie_rut']);
     $MiTemplate->set_var("dire_direccion",$osSel['dire_direccion']);     
     $MiTemplate->set_var("dire_telefono",$osSel['dire_telefono']);
     	$MiTemplate->set_var("departamento",$osSel['nombre_departamento']);
     	$MiTemplate->set_var("ciudad", $osSel['nombre_ciudad']);
     $MiTemplate->set_var("comu_nombre", $osSel['comu_nombre']);
     $MiTemplate->set_var("dire_telefono_des",$osSel['dire_telefono']);
     
     $MiTemplate->set_var("dire_direccion_des",$osSel['dire_direccion']);
     
     $MiTemplate->set_var("comu_nombre_des",$osSel['comu_nombre']);
     $MiTemplate->set_var("dire_observacion_des",$osSel['dire_observacion']);




     $MiTemplate->set_var("dire_telefono_pri",$diredes['dire_telefono_des']);
     $MiTemplate->set_var("dire_direccion_pri",$diredes['dire_direccion_des']);
     $MiTemplate->set_var("comu_nombre_pri",$diredes['comu_nombre_des']);
     $MiTemplate->set_var("dire_observacion_des",$diredes['dire_observacion_des']);

     $fecha_ini = date('m/Y');
     $dia = date('d') + 1;
     $fecha = $dia . "/" . $fecha_ini;
     
     $MiTemplate->set_var("fecha_entrega", $fecha);	
	
     $Consulta_Local = "SELECT cod_local , os_fechacreacion FROM os inner join locales ON os.id_local = locales.id_local WHERE id_os = $id_os";
     
    $Resultado_Local = tep_db_query($Consulta_Local);
	$Cod_Local = tep_db_fetch_array($Resultado_Local);

	$Codigo_Local = substr($Cod_Local['cod_local'], 1);

	$Fecha_cracion_os = $Cod_Local['os_fechacreacion'];
	list($year, $month, $day, $hour, $minute, $sec) = split('[- :]', $Fecha_cracion_os); 
	$cotizTimestamp=mktime($hour, $minute,$sec, $month, $day, $year);

    // Coloca Prefijo
	$Consulta_Prefijo = "SELECT prefijo FROM origenusr ori 
                        INNER JOIN os o ON ori.id_origen = o.USR_ORIGEN WHERE id_os= $id_os";
	$Resultado_Prefijo = tep_db_query($Consulta_Prefijo);
	$Cod_prefijo = tep_db_fetch_array($Resultado_Prefijo);

	$prefijo = $Cod_prefijo['prefijo'];	
	$cod_ean_os = calcula_num_os($id_os);			
	// Coloca Prefijo
	$codigo = $prefijo . $Codigo_Local . $cod_ean_os;
	
	$dig_verificacion = dvEAN13($codigo);	
	$codigo_ean = $codigo . $dig_verificacion;

	$MiTemplate->set_var("cod_barra_os",gencode_EAN13($codigo_ean, 150, 60));

// Agregamos el header
/*$MiTemplate->set_file("header","header_ident.html");*/

// Agregamos el main
$MiTemplate->set_file("main","nueva_cotizacion/print1.htm");

/* para el nombre del que atendio*/
$qnombre="select U.USR_NOMBRES, U.USR_APELLIDOS from usuarios U inner join os OS on (OS.usr_id=U.usr_id) where id_os=".($id_os+0)."";
$osAtendido = tep_db_query($qnombre);
$osAtendido = tep_db_fetch_array( $osAtendido );
$MiTemplate->set_var('atendido', $osAtendido['USR_NOMBRES'] . " " . $osAtendido['USR_APELLIDOS']);

/* para los productos*/
$MiTemplate->set_file("productos","nueva_cotizacion/os_detalle.htm");
/* para los productos*/
$instn="No";
$insts="Sí";
$precioVacio=" - ";
$totalVacio=" 0 ";
$especificacion="<br> <u>Especificaciones</u>:&nbsp;";
     $MiTemplate->set_block("main", "Precio_Total", "PBLModulos");
/* precios y total parciado*/
    $query_OD="SELECT (P.peso/1000) AS peso, OD.osde_fecha_entrega, OD.osde_especificacion, (OD.cod_sap+0)  as cod_sap,OD.cod_barra,OD.osde_tipoprod,OD.osde_descripcion,OD.osde_cantidad,OD.id_tipodespacho,OD.osde_instalacion,OD.osde_precio,TD.nombre as tipo_nombre ,OD.osde_descuento,
    if(osde_descuento is not null, osde_descuento, 0) 'osde_descuento',
    if(osde_descuento<>0,ROUND((osde_precio-osde_descuento)*osde_cantidad),ROUND(osde_precio*osde_cantidad))'Total',
    if(((osde_precio-osde_descuento)*osde_cantidad) is Null,'$totalVacio','')'totalVacio' ,
    if(OD.osde_tipoprod='SV', '-', TD.nombre) 'tipo_nombre',  
    if(OD.osde_tipoprod='SV', ' - ',  if (OD.osde_instalacion, 'SI', 'NO')) 'osde_instalacion',
    if(OD.osde_precio is null, '$precioVacio', '') 'precioVacio'   ,if(OD.osde_especificacion is not null, '$especificacion', '') 'especificacion' 
    FROM os_detalle OD inner join tipos_despacho TD on (TD.id_tipodespacho=OD.id_tipodespacho) 
    INNER JOIN productos P ON  OD.id_producto = P.id_producto
    where id_os=".($id_os+0)." order by OD.id_os_detalle desc ";
    
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
            
            if ($res['peso'] == ''){
            	$MiTemplate->set_var('peso', '-');
            }
            else{
            	$MiTemplate->set_var('peso', round($res['peso'],DECIMALES_PESO));
            }
            	$desc_Prod = Format_descProduct($res['osde_descripcion']);
            	
                $MiTemplate->set_var('tipo_nombre', $res['tipo_nombre']);
                $MiTemplate->set_var('osde_instalacion', $res['osde_instalacion']);
                $MiTemplate->set_var('cod_barra', $res['cod_barra']);
                $MiTemplate->set_var('cod_sap', $res['cod_sap']);
                $MiTemplate->set_var('osde_tipoprod', $res['osde_tipoprod']);
                $MiTemplate->set_var('osde_descripcion', $desc_Prod);
                $MiTemplate->set_var('especificacion', $res['especificacion']);
                $MiTemplate->set_var('osde_especificacion', $res['osde_especificacion']);
                $MiTemplate->set_var('osde_fecha_entrega', $res['osde_fecha_entrega']);                
                
                $MiTemplate->set_var('osde_cantidad', $res['osde_cantidad']);
                $MiTemplate->parse("PBLModulos", "Precio_Total", true);   
       }           
    }
    /*para obtener el total*/
      $query_TG = "select osde_precio,osde_cantidad, if(osde_descuento is not null, osde_descuento, 0) 'osde_descuento' ,if (osde_descuento<>0,ROUND((osde_precio-osde_descuento)*osde_cantidad),ROUND(osde_precio*osde_cantidad))'Total' from os_detalle where id_os=".($id_os+0)." ";
      $suma=0;
        if ( $rq = tep_db_query($query_TG) ){
            while( $res = tep_db_fetch_array( $rq ) ) {
            $suma=$suma+$res['Total'];
            }           
        }
       $MiTemplate->set_var('TG', formato_precio($suma));

       /* Consulta del primer mensaje sobre fletes. */
       $consulta = "SELECT var_valor FROM glo_variables WHERE var_llave = 'TEXT_UNO_COTIZA'";
       $resultado = tep_db_query($consulta);
       $result = tep_db_fetch_array($resultado);
       $MiTemplate->set_var('Mensaje_1', $result['var_valor']);
       
       /* Consulta del Segundo mensaje sobre vigencia de la cotización. */
       $consulta = "SELECT var_valor FROM glo_variables WHERE var_llave = 'TEXT_DOS_COTIZA'";
       $resultado = tep_db_query($consulta);
       $result = tep_db_fetch_array($resultado);
       $MiTemplate->set_var('Mensaje_2', $result['var_valor']);
       
        /* Consulta del tercer mensaje sobre giro de cheques */
 		if($cotizTimestamp < FECHACAMBIORAZSOC){
		   $consulta = "SELECT var_valor FROM glo_variables WHERE var_llave = 'TEXT_TRES_COTIZA'";
		}else{
		   $consulta = "SELECT var_valor FROM glo_variables WHERE var_llave = 'TEXT_TRES_COTIZA_CENCOSUD'";
		}
		$resultado = tep_db_query($consulta);
        $result = tep_db_fetch_array($resultado);
        $MiTemplate->set_var('Mensaje_3', $result['var_valor']); 
       
       
       /* Consulta del cuarto mensaje sobre giro de cheques */
       $consulta = "SELECT var_valor FROM glo_variables WHERE var_llave = 'TEXT_CUATRO_COTIZA'";
       $resultado = tep_db_query($consulta);
       $result = tep_db_fetch_array($resultado);
       $MiTemplate->set_var('Mensaje_4', $result['var_valor']);
       
       
        /* Consulta del quinto mensaje sobre giro de cheques */
       $consulta = "SELECT var_valor FROM glo_variables WHERE var_llave = 'TEXT_CINCO_COTIZA'";
       $resultado = tep_db_query($consulta);
       $result = tep_db_fetch_array($resultado);
       $MiTemplate->set_var('Mensaje_5', $result['var_valor']);
       
       
        /* Consulta del sexto mensaje sobre  */
       $consulta = "SELECT var_valor FROM glo_variables WHERE var_llave = 'TEXT_SEIS_COTIZA'";
       $resultado = tep_db_query($consulta);
       $result = tep_db_fetch_array($resultado);
       $MiTemplate->set_var('Mensaje_6', $result['var_valor']);
       
        /* Consulta del sexto mensaje sobre  */
       $consulta = "SELECT var_valor FROM glo_variables WHERE var_llave = 'TEXT_SIETE_COTIZA'";
       $resultado = tep_db_query($consulta);
       $result = tep_db_fetch_array($resultado);
       $MiTemplate->set_var('Mensaje_7', $result['var_valor']);
       
       
        /* Consulta del sexto mensaje sobre  */
       $consulta = "SELECT var_valor FROM glo_variables WHERE var_llave = 'TEXT_OCHO_COTIZA'";
       $resultado = tep_db_query($consulta);
       $result = tep_db_fetch_array($resultado);
       $MiTemplate->set_var('Mensaje_8', $result['var_valor']);
       
       
        /* Consulta del sexto mensaje sobre  */
       $consulta = "SELECT var_valor FROM glo_variables WHERE var_llave = 'TEXT_NUEVE_COTIZA'";
       $resultado = tep_db_query($consulta);
       $result = tep_db_fetch_array($resultado);
       $MiTemplate->set_var('Mensaje_9', $result['var_valor']);
       
        /* Consulta del sexto mensaje sobre  */
       $consulta = "SELECT var_valor FROM glo_variables WHERE var_llave = 'TEXT_DIEZ_COTIZA'";
       $resultado = tep_db_query($consulta);
       $result = tep_db_fetch_array($resultado);
       $MiTemplate->set_var('Mensaje_10', $result['var_valor']);
       
       
        /* Consulta del sexto mensaje sobre  */
       $consulta = "SELECT var_valor FROM glo_variables WHERE var_llave = 'TEXT_ONCE_COTIZA'";
       $resultado = tep_db_query($consulta);
       $result = tep_db_fetch_array($resultado);
       $MiTemplate->set_var('Mensaje_11', $result['var_valor']);
       
        /* Consulta del sexto mensaje sobre  */
       $consulta = "SELECT var_valor FROM glo_variables WHERE var_llave = 'TEXT_DOCE_COTIZA'";
       $resultado = tep_db_query($consulta);
       $result = tep_db_fetch_array($resultado);
       $MiTemplate->set_var('Mensaje_12', $result['var_valor']);
       
       
        /* Consulta del sexto mensaje sobre  */
       $consulta = "SELECT var_valor FROM glo_variables WHERE var_llave = 'TEXT_TRECE_COTIZA'";
       $resultado = tep_db_query($consulta);
       $result = tep_db_fetch_array($resultado);
       $MiTemplate->set_var('Mensaje_13', $result['var_valor']);
       
       
        /* Consulta del sexto mensaje sobre  */
       $consulta = "SELECT var_valor FROM glo_variables WHERE var_llave = 'TEXT_CATORCE_COTIZA'";
       $resultado = tep_db_query($consulta);
       $result = tep_db_fetch_array($resultado);
       $MiTemplate->set_var('Mensaje_14', $result['var_valor']);
       
       
        /* Consulta del sexto mensaje sobre  */
       $consulta = "SELECT var_valor FROM glo_variables WHERE var_llave = 'TEXT_QUINCE_COTIZA'";
       $resultado = tep_db_query($consulta);
       $result = tep_db_fetch_array($resultado);
       $MiTemplate->set_var('Mensaje_15', $result['var_valor']);
       
       
        /* Consulta del sexto mensaje sobre  */
       $consulta = "SELECT var_valor FROM glo_variables WHERE var_llave = 'TEXT_DIECISEIS_COTIZA'";
       $resultado = tep_db_query($consulta);
       $result = tep_db_fetch_array($resultado);
       $MiTemplate->set_var('Mensaje_16', $result['var_valor']);
       
       
        /* Consulta del sexto mensaje sobre  */
       $consulta = "SELECT var_valor FROM glo_variables WHERE var_llave = 'TEXT_DIECISIETE_COTIZA'";
       $resultado = tep_db_query($consulta);
       $result = tep_db_fetch_array($resultado);
       $MiTemplate->set_var('Mensaje_17', $result['var_valor']);
       
            
       
       /* Consulta del sexto mensaje sobre Proyecto de Instalación */
       $consulta = "SELECT var_valor FROM glo_variables WHERE var_llave = 'TEXT_DIECIOCHO_COTIZA'";
       $resultado = tep_db_query($consulta);
       $result = tep_db_fetch_array($resultado);
       $MiTemplate->set_var('Mensaje_18', $result['var_valor']);
       
       
        /* Consulta del sexto mensaje sobre Proyecto de Instalación */
       $consulta = "SELECT var_valor FROM glo_variables WHERE var_llave = 'TEXT_DIECINUEVE_COTIZA'";
       $resultado = tep_db_query($consulta);
       $result = tep_db_fetch_array($resultado);
       $MiTemplate->set_var('Mensaje_19', $result['var_valor']);
       
       
		/* Consulta del sexto mensaje sobre Proyecto de Instalación  */
       $consulta = "SELECT var_valor FROM glo_variables WHERE var_llave = 'TEXT_VEINTE_COTIZA'";
       $resultado = tep_db_query($consulta);
       $result = tep_db_fetch_array($resultado);
       $MiTemplate->set_var('Mensaje_20', $result['var_valor']);
       
       
       /* Consulta del sexto mensaje sobre Proyecto de Instalación  */
       $consulta = "SELECT var_valor FROM glo_variables WHERE var_llave = 'TEXT_VEINTIUNO_COTIZA'";
       $resultado = tep_db_query($consulta);
       $result = tep_db_fetch_array($resultado);
       $MiTemplate->set_var('Mensaje_21', $result['var_valor']);
       
              
       /* Consulta del sexto mensaje sobre Proyecto de Instalación  */
       $consulta = "SELECT var_valor FROM glo_variables WHERE var_llave = 'TEXT_VEINTIDOS_COTIZA'";
       $resultado = tep_db_query($consulta);
       $result = tep_db_fetch_array($resultado);
       $MiTemplate->set_var('Mensaje_22', $result['var_valor']);
       
       
       /* Consulta del sexto mensaje sobre Proyecto de Instalación  */
       $consulta = "SELECT var_valor FROM glo_variables WHERE var_llave = 'TEXT_VEINTITRES_COTIZA'";
       $resultado = tep_db_query($consulta);
       $result = tep_db_fetch_array($resultado);
       $MiTemplate->set_var('Mensaje_23', $result['var_valor']);
       
       
       /* Consulta del sexto mensaje sobre Proyecto de Instalación  */
       $consulta = "SELECT var_valor FROM glo_variables WHERE var_llave = 'TEXT_VEINTICUATRO_COTIZA'";
       $resultado = tep_db_query($consulta);
       $result = tep_db_fetch_array($resultado);
       $MiTemplate->set_var('Mensaje_24', $result['var_valor']);
       
       
       /* Consulta del texto mensaje sobre Condiciones Comerciales  */
       $consulta = "SELECT var_valor FROM glo_variables WHERE var_llave = 'TEXT_VEINTICINCO_COTIZA'";
       $resultado = tep_db_query($consulta);
       $result = tep_db_fetch_array($resultado);
       $MiTemplate->set_var('Mensaje_25', $result['var_valor']);
       
       
           /* Consulta del texto mensaje sobre Condiciones Comerciales  */
	   	if($cotizTimestamp < FECHACAMBIORAZSOC){
		      $consulta = "SELECT var_valor FROM glo_variables WHERE var_llave = 'TEXT_VEINTISEIS_COTIZA'";
		}else{
		       $consulta = "SELECT var_valor FROM glo_variables WHERE var_llave = 'TEXT_VEINTISEIS_COTIZA_CENCOSUD'";
		}
	   $resultado = tep_db_query($consulta);
       $result = tep_db_fetch_array($resultado);
       $MiTemplate->set_var('Mensaje_26', $result['var_valor']); 
       
       
       /* Consulta del texto mensaje sobre Condiciones Comerciales  */
 	   	if($cotizTimestamp < FECHACAMBIORAZSOC){
		       $consulta = "SELECT var_valor FROM glo_variables WHERE var_llave = 'TEXT_VEINTISIETE_COTIZA'";
		}else{
		       $consulta = "SELECT var_valor FROM glo_variables WHERE var_llave = 'TEXT_VEINTISIETE_COTIZA_CENCOSUD'";
		}
       $resultado = tep_db_query($consulta);
       $result = tep_db_fetch_array($resultado);
       $MiTemplate->set_var('Mensaje_27', $result['var_valor']); 
       
       
       /* Consulta del texto mensaje sobre Condiciones Comerciales  */
       $consulta = "SELECT var_valor FROM glo_variables WHERE var_llave = 'TEXT_VEINTIOCHO_COTIZA'";
       $resultado = tep_db_query($consulta);
       $result = tep_db_fetch_array($resultado);
       $MiTemplate->set_var('Mensaje_28', $result['var_valor']);
       
       
       
       
       
       
      /*CALIDAD TRIBUTARIA EASY*/
      
       $consulta_cal_tri = "SELECT var_valor FROM glo_variables WHERE var_llave = 'CALIDAD_TRIBUTARIA'";
       $resultado = tep_db_query($consulta_cal_tri);
       $result = tep_db_fetch_array($resultado);
       $calidad_tributaria_easy = $result['var_valor'];
              
       
      /* Consulta de Iva */
	  $idlocal = get_localID($ses_usr_id);	 
	   
      $consulta_IVA = 	"SELECT  ( SUM(osde_precio * osde_cantidad)) AS valor, iva  
						FROM  precios P INNER JOIN  os_detalle D ON   P.id_producto = D.id_producto 
						WHERE id_os = $id_os AND id_local = $idlocal GROUP BY  iva";
	
	
      $resultado = tep_db_query($consulta_IVA);      
      
      $arreglo = array();
      $valor_total_iva = 0;
            
      while($result = tep_db_fetch_array($resultado))
      {            
      $base_iva = (($result['valor'] * 100)) / (100+$result['iva']);
      $arreg['Descripcion_iva'] = $result['iva'];
	  $arreg['Base_iva'] = round($base_iva, 0);
      $arreg['Valor_iva'] = $result['valor']- round($base_iva, 0);      
      $valor_total_iva = $valor_total_iva + $arreg['Valor_iva'];
      $arreglo[] = $arreg; 
      }      
      
		
       $MiTemplate->set_block("main", "Impuestos", "PBLModulos2");
       foreach($arreglo as $lista => $valor_iva)
       {

	       	if($valor_iva['Descripcion_iva'] != '')
	       	{
	       	$MiTemplate->set_var('Descripcion_iva', formato_precio($valor_iva['Descripcion_iva']));
	       	$MiTemplate->set_var('Base_iva', formato_precio($valor_iva['Base_iva']));
	       	$MiTemplate->set_var('Valor_iva', formato_precio($valor_iva['Valor_iva']));
	       	}
	       	else{
	       	$MiTemplate->set_var('Descripcion_iva', 0);
	       	$MiTemplate->set_var('Base_iva', 0);
	       	$MiTemplate->set_var('Valor_iva', 0);       	
	       	}
	       	       
       $MiTemplate->parse("PBLModulos2", "Impuestos", true);
       }
       $MiTemplate->set_var('Total_iva', formato_precio($valor_total_iva));

       
       
		$consulta_Flete = 	"SELECT SUM(osde_precio * osde_cantidad) AS flete  FROM os_detalle WHERE osde_tipoprod = 'SV'  AND  osde_subtipoprod = 'DE'  AND    id_os =  $id_os ";
      	$resultado_Flete = tep_db_query($consulta_Flete);    
      	$resul_Flete = tep_db_fetch_array($resultado_Flete);
       
       
       $MiTemplate->set_var('Total_fletes', formato_precio($resul_Flete['flete']));
       
       
	  /* Consulta de Renta */       
      
	if($clie_tipo_contribuyente != 'RS' && $calidad_tributaria_easy == 'REGIMEN_COMUN' && $clie_fuente == 1)
	{
		
      $consulta_RENTA = "SELECT  ( SUM(osde_precio * osde_cantidad)) AS valor, retefuente  
						FROM  precios P INNER JOIN  os_detalle D ON   P.id_producto = D.id_producto 
						WHERE id_os= $id_os AND id_local = $idlocal  GROUP BY  retefuente ";      
	  $resultado = tep_db_query($consulta_RENTA);      
      
      $arreglo2 = array();
            	      
      while($result = tep_db_fetch_array($resultado))
      {	  
      $base_renta = (($result['valor'] * 100)) / (100+$result['retefuente']);
      $arreg['Descripcion_renta'] = $result['retefuente'];
	  $arreg['Base_renta'] = round($base_renta, 0);
      $arreg['Valor_renta'] = $result['valor']- round($base_renta, 0);      
      
      $arreglo2[] = $arreg; 
      }      
            
      $contar = 0;
      
       $MiTemplate->set_block("main", "Impuestos_renta", "PBLModulos3");
       foreach($arreglo2 as $lista => $valor_renta)
       {

	       	if($valor_renta['Descripcion_renta'] != '')
	       	{
	       	$MiTemplate->set_var('Descripcion_renta', formato_precio($valor_renta['Descripcion_renta']));
	       	$MiTemplate->set_var('Base_renta', formato_precio($valor_renta['Base_renta']));
	       	$MiTemplate->set_var('Valor_renta', formato_precio($valor_renta['Valor_renta']));
	       	$contar++;
	       	}
	       	else{
	       	$MiTemplate->set_var('Descripcion_renta', 0);
	       	$MiTemplate->set_var('Base_renta', 0);
	       	$MiTemplate->set_var('Valor_renta', 0);       	
	       	}
	     	       
       $MiTemplate->parse("PBLModulos3", "Impuestos_renta", true);
       }
       
       if($contar == 1)
       $contar++;
       
       $MiTemplate->set_var('Contar_renta', $contar);
	}
	else{
		$MiTemplate->set_var('Contar_renta', 0);
	}

		/* Consulta de Ica */   
	if($clie_tipo_contribuyente != 'RS' && $calidad_tributaria_easy == 'REGIMEN_COMUN' && $clie_reteica == 1)
	{	  
      $consulta_ICA = "SELECT  ( SUM(osde_precio * osde_cantidad)) AS valor, reteica  
					  FROM  precios P INNER JOIN  os_detalle D ON   P.id_producto = D.id_producto 
					  WHERE id_os= $id_os AND id_local = $idlocal GROUP BY  reteica ";
      $resultado = tep_db_query($consulta_ICA);      
      
      $arreglo3 = array();      
      
      while($result2 = tep_db_fetch_array($resultado))
      {      
      
      $base_ica = (($result2['valor'] * 100)) / (100+$result2['reteica']);
      $arreg3['Descripcion_ica'] = $result2['reteica'];
	  $arreg3['Base_ica'] = round($base_ica, 0);
      $arreg3['Valor_ica'] = $result2['valor']- round($base_ica, 0);      
      
      $arreglo3[] = $arreg3; 
      }     
      
      $contar_ica = 0;
       $MiTemplate->set_block("main", "Impuestos_ica", "PBLModulos4");
       foreach($arreglo3 as $lista => $valor_ica)
       {
	       	if($valor_ica['Descripcion_ica'] != '')
	       	{
	       	$MiTemplate->set_var('Descripcion_ica', formato_precio($valor_ica['Descripcion_ica']));
	       	$MiTemplate->set_var('Base_ica', formato_precio($valor_ica['Base_ica']));
	       	$MiTemplate->set_var('Valor_ica', formato_precio($valor_ica['Valor_ica']));
	       	$contar_ica++;
	       	}
	       	
	       	else{
	       	$MiTemplate->set_var('Descripcion_ica', 0);
	       	$MiTemplate->set_var('Base_ica', 0);
	       	$MiTemplate->set_var('Valor_ica', 0);       	
	       	}
	       	       
       $MiTemplate->parse("PBLModulos4", "Impuestos_ica", true);
       }
       
       if($contar_ica == 1)
       $contar_ica++;
       
       $MiTemplate->set_var('Contar_ica', $contar_ica);
	}
	else{
		$MiTemplate->set_var('Contar_ica', 0);
	} 
       
/* para los include de las glosas*/
$arr_includes = Array();
array_push($arr_includes, 'main');

$queryGLo="select distinct glosa from os_detalle join tipo_subtipo on osde_tipoprod = prod_tipo and osde_subtipoprod = prod_subtipo where id_os = ".($id_os+0)." and glosa is not null";
$arreglosa = tep_db_query($queryGLo);
while( $glo = tep_db_fetch_array( $arreglosa ) ) {
	$MiTemplate->set_file($glo["glosa"],"glosas/".$glo["glosa"]);
	array_push($arr_includes, $glo["glosa"]);
}           

array_push($arr_includes, 'footer');

$MiTemplate->parse("OUT_M", $arr_includes, true);
$MiTemplate->p("OUT_M");

include "../../includes/application_bottom.php";
?>