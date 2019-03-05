<?
$pag_ini = '../nueva_cotizacion/nueva_cotizacion_00.php';
include "../../includes/aplication_top.php";
include "activewidgets.php";

// include_idioma_mod( $ses_idioma, "nueva_cotizacion_01" );

// *************************************************************************************


/*$MiTemplate = new Template();
// asignamos degug maximo
$MiTemplate->debug = 0;
// root directory de los templates
$MiTemplate->set_root(DIRTEMPLATES);
// variables perdidas
$MiTemplate->set_unknowns("remove");*/


// Agregamos el main
$MiTemplate->set_file("main","nueva_cotizacion/os_detalle.htm");

/* para los productos*/
$instn="No";
$insts="Sí";
$precioVacio=" - ";
$especificacion="<br> <u>Especificaciones</u>:&nbsp;";
    $MiTemplate->set_block("main", "OSDETALLE", "BLO_detalle");
    $query_OD="select OD.osde_especificacion, OD.cod_sap,OD.cod_barra,OD.osde_tipoprod,OD.osde_descripcion,OD.osde_cantidad,OD.id_tipodespacho,OD.osde_instalacion,OD.osde_precio,TD.nombre as tipo_nombre ,OD.osde_descuento ,ROUND((osde_precio-osde_descuento)*osde_cantidad) as Total ,if('0'=OD.osde_instalacion, '$instn', '$insts') 'osde_instalacion' ,if(OD.osde_precio is null, '$precioVacio', '') 'precioVacio'   ,if(OD.osde_especificacion is not null, '$especificacion', '') 'especificacion' from os_detalle OD inner join tipos_despacho TD on (TD.id_tipodespacho=OD.id_tipodespacho) where id_os=".($id_os+0)." ";
    query_to_set_var( $query_OD, $MiTemplate, 1, 'BLO_detalle', 'OSDETALLE' ); 

?>