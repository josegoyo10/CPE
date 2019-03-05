<?php
$pag_ini = '../nueva_cotizacion/adminPs_Pe.php';
include ("../../includes/aplication_top.php");
include_idioma_mod( $ses_idioma, "nueva_cotizacion_00" );

//***Insertar nueva fecha de entrega en la Tabla fecha_entrega ***

if($accion == 'Insertar' && $codSap != '' && $codProv != '')
{
$consulta = "INSERT INTO ventaxvolumenps(cod_sap, cod_proveedor, numero_dias, nombre_producto, nombre_proveedor, cantidadventaXvolumen)
VALUES ($codSap, $codProv, $numero_dias, '$descProd', '$nomProv', $valorMax);";
$resultado = tep_db_query($consulta);
}


if($accion == 'Modificar' && $codSap != '' && $codProv != '')
{
$consulta = "UPDATE ventaxvolumenps SET numero_dias = $numero_dias, cantidadventaXvolumen = $valorMax  WHERE cod_sap = $codSap AND cod_proveedor = $codProv ";
$resultado = tep_db_query($consulta);
}


if($accion == 'Eliminar' && $codSap != '' && $codProv != '')
{
$consulta = "DELETE FROM ventaxvolumenps WHERE cod_sap = $codSap AND cod_proveedor = $codProv ";
$resultado = tep_db_query($consulta);
}


if($resultado){
   echo  '<script language="JavaScript">
            alert ("Se realizarón los cambios correctamente.");
          </script>';
}


$MiTemplate = new Template();
// asignamos debug maximo
$MiTemplate->debug = 0;
// root directory de los templates
$MiTemplate->set_root(DIRTEMPLATES);
// variables perdidas
$MiTemplate->set_unknowns("remove");


$MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
$MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);
$MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));

$MiTemplate->set_file("header","header_ident.html");

// Agregamos el header
$MiTemplate->set_file("header","header_ident.html");

// Agregamos el main
$MiTemplate->set_file("main","adminPs_Pe.htm");

// Agregamos el footer
include "../../includes/footer_cproy.php";

$MiTemplate->pparse("OUT_H", array("header"), true);
include "../../menu/menu.php";
$MiTemplate->parse("OUT_M", array("main","footer"), true);
$MiTemplate->p("OUT_M");
include "../../includes/application_bottom.php";


?>

