<?
//$pag_ini = '../nueva_cotizacion/nueva_cotizacion_00.php';
$SIN_PER = 1;
include "../../includes/aplication_top.php";
include "activewidgets.php";

// include_idioma_mod( $ses_idioma, "nueva_cotizacion_01" );

// *************************************************************************************

/*include_idioma_mod( $ses_idioma, "start" );*/

/* acciones*/
$querysel="select CL.clie_tipo,CL.clie_razonsocial,S.os_fechaestimacion,CL.clie_rut,S.os_descripcion,S.os_comentarios ,S.id_os ,S.clie_rut ,CL.clie_nombre,CL.clie_paterno,CL.clie_materno,CL.clie_telefonocasa,L.nom_local,S.os_fechacotizacion ,E.esta_nombre,P.proy_nombre,D.dire_direccion,D.dire_observacion,D.dire_telefono,CO.comu_nombre,cod_local_pos from os S inner join clientes CL on (CL.clie_rut=S.clie_rut) Inner join locales L on (L.id_local=S.id_local) inner join estados E on (S.id_estado=E.id_estado) inner join proyectos P on (P.id_proyecto=S.id_proyecto) inner join direcciones D on (D.id_direccion=S.id_direccion) inner join comuna CO on (D.id_comuna=CO.id_comuna) where id_os=".($id_os+0)." ";
$osSel = tep_db_query($querysel);
$osSel = tep_db_fetch_array( $osSel );

/* si viene SIN direccion de despacho , como en sumario*/
    $querydir_pri="select OS.id_direccion,OS.clie_rut,D.id_direccion,D.dire_nombre as dire_nombre_pri ,D.dire_direccion as dire_direccion_pri,D.id_direccion ,D.dire_defecto ,C.comu_nombre as comu_nombre_pri ,D.dire_telefono as dire_telefono_pri ,D.dire_observacion as dire_observacion_pri  from os OS inner join direcciones D on (OS.clie_rut= D.clie_rut)inner join comuna C on (C.id_comuna= D.id_comuna ) where OS.id_os=".($id_os+0)." and D.dire_defecto='p' ";
    $direpri = tep_db_query($querydir_pri);
    $direpri = tep_db_fetch_array( $direpri );

$digito=dv($osSel['clie_rut']);

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
     $MiTemplate->set_var("digito",$digito);
     $MiTemplate->set_var("os_fechaestimacion",fecha_db2php($osSel['os_fechaestimacion']));
     $MiTemplate->set_var("esta_nombre",$osSel['esta_nombre']);
     $MiTemplate->set_var("nom_local",$osSel['nom_local']);
     $MiTemplate->set_var("os_descripcion",$osSel['os_descripcion']);
     $MiTemplate->set_var("os_comentarios",$osSel['os_comentarios']);
     $MiTemplate->set_var("os_fechacotizacion",fecha_db2php($osSel['os_fechacotizacion']));
     $MiTemplate->set_var("proy_nombre",$osSel['proy_nombre']);
     $MiTemplate->set_var("clie_nombre",$osSel['clie_nombre']);
     $MiTemplate->set_var("clie_telefonocasa",$osSel['clie_telefonocasa']);
     $MiTemplate->set_var("clie_paterno",$osSel['clie_paterno']);
     $MiTemplate->set_var("clie_materno",$osSel['clie_materno']);
     $MiTemplate->set_var("clie_rut",$osSel['clie_rut']);
     $MiTemplate->set_var("dire_direccion_pri",$direpri['dire_direccion_pri']);
     $MiTemplate->set_var("dire_telefono",$osSel['dire_telefono']);
     $MiTemplate->set_var("comu_nombre_pri",$direpri['comu_nombre_pri']);

     $MiTemplate->set_var("dire_telefono_des",$osSel['dire_telefono']);
     $MiTemplate->set_var("dire_direccion_des",$osSel['dire_direccion']);
     $MiTemplate->set_var("comu_nombre_des",$osSel['comu_nombre']);
     $MiTemplate->set_var("dire_observacion_des",$osSel['dire_observacion']);



if($dire_des){
     $MiTemplate->set_var("dire_telefono_des",$diredes['dire_telefono_des']);
     $MiTemplate->set_var("dire_direccion_des",$diredes['dire_direccion_des']);
     $MiTemplate->set_var("comu_nombre_des",$diredes['comu_nombre_des']);
     $MiTemplate->set_var("dire_observacion_des",$diredes['dire_observacion_des']);
}

//Imprimimos el c�digo de barra de la cotizaci�n
	$cod_prefijo = 20;
	$mcode = sprintf("%02.2s%010.10s",$cod_prefijo, $id_os); //Ya no se utilizar� el c�digo pos del local
	$mcode .= dvEAN13($mcode); 
	$MiTemplate->set_var("cod_barra_os",gencode_EAN13($mcode, 150, 60));

// Agregamos el header
/*$MiTemplate->set_file("header","header_ident.html");*/

// Agregamos el main
$MiTemplate->set_file("main","nueva_cotizacion/print1.htm");

/* para el nombre del que atendio*/
$qnombre="select U.usr_nombres from usuarios U inner join os OS on (OS.usr_id=U.usr_id) where id_os=".($id_os+0)."";
$osAtendido = tep_db_query($qnombre);
$osAtendido = tep_db_fetch_array( $osAtendido );
$MiTemplate->set_var('atendido', $osAtendido['usr_nombres']);

/* para los productos*/
$MiTemplate->set_file("productos","nueva_cotizacion/os_detalle.htm");
/* para los productos*/
$instn="No";
$insts="S�";
$precioVacio=" - ";
$totalVacio=" 0 ";
$especificacion="<br> <u>Especificaciones</u>:&nbsp;";
     $MiTemplate->set_block("main", "Precio_Total", "PBLModulos");
/* precios y total parciado*/
    $query_OD="select OD.osde_especificacion, (OD.cod_sap+0)  as cod_sap,OD.cod_barra,OD.osde_tipoprod,OD.osde_descripcion,OD.osde_cantidad,OD.id_tipodespacho,OD.osde_instalacion,OD.osde_precio,TD.nombre as tipo_nombre ,OD.osde_descuento,
    if(osde_descuento is not null, osde_descuento, 0) 'osde_descuento',
    if(osde_descuento<>0,ROUND((osde_precio-osde_descuento)*osde_cantidad),ROUND(osde_precio*osde_cantidad))'Total',
    if(((osde_precio-osde_descuento)*osde_cantidad) is Null,'$totalVacio','')'totalVacio' ,
    if(OD.osde_tipoprod='SV', '-', TD.nombre) 'tipo_nombre',  
    if(OD.osde_tipoprod='SV', ' - ',  if (OD.osde_instalacion, 'SI', 'NO')) 'osde_instalacion',
    if(OD.osde_precio is null, '$precioVacio', '') 'precioVacio'   ,if(OD.osde_especificacion is not null, '$especificacion', '') 'especificacion' from os_detalle OD inner join tipos_despacho TD on (TD.id_tipodespacho=OD.id_tipodespacho) where id_os=".($id_os+0)." order by OD.id_os_detalle desc ";
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
                $MiTemplate->set_var('cod_barra', $res['cod_barra']);
                $MiTemplate->set_var('cod_sap', $res['cod_sap']);
                $MiTemplate->set_var('osde_tipoprod', $res['osde_tipoprod']);
                $MiTemplate->set_var('osde_descripcion', $res['osde_descripcion']);
                $MiTemplate->set_var('especificacion', $res['especificacion']);
                $MiTemplate->set_var('osde_especificacion', $res['osde_especificacion']);
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