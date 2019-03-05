<?php
include_once("../../includes/funciones/database.php");
include_once("../../includes/funciones/general.php");
include_once('../../includes/template.php');
include_once('../../includes/db_config.php');

// nos conectamos a la base de datos
tep_db_connect() or die('Unable to connect to database server!');

// set the application parameters (can be modified through the administration tool)
$configuration_query = tep_db_query('select var_llave as cfgKey, var_valor as cfgValue from glo_variables');
while ($configuration = tep_db_fetch_array($configuration_query)) {
    define($configuration['cfgKey'], $configuration['cfgValue']);
}

// iniciamos las variables de session
session_start();

if( $ses_usr_id == '' && $SIN_PER != 1 ) {
    header( "Location: ../start/index.php" );
    tep_exit();
}

if( !session_is_registered('ses_ult_carga') ) {
    session_register('ses_ult_carga');
}
else {
    if( $action != 'logout' )
        desconectar_usuario( $ses_ult_carga );
}
$ses_ult_carga = time();

if( !session_is_registered('ses_idioma') ) {
    session_register('ses_idioma');
    $ses_idioma = "spanish";
}

if( $idioma != '' )
    $ses_idioma = $idioma;

include_once("../../includes/idiomas/".$ses_idioma."/general.php");

define(DIRTEMPLATES, "../templates");

define( 'COLOR_IFRAME_FONDO', '#EBEAEA' );
define( 'LARGO_IFRAME', 400 );
define( 'PREFIJO_CENTRO_PROYECTOS', 20);

define( 'PERMISO_REIMPRESION_PICKING', 82);
define( 'PERMISO_PAGO_MANUAL', 90);
define( 'PERMISO_CRUCE_COTIZACION', 99);
define( 'DECIMALES_PESO', 3);

define( 'VALIDA_SUB_ADMIN', 110);

include_once( "../../includes/funciones/fun_template.php" );

if( $SIN_PER == 0 ) {
   if( $mid == '' ) {
         $arr_aux = split( "/", getenv("PATH_INFO") );
       if( $pag_ini == '' )
            $pag_ini = $arr_aux[sizeof($arr_aux)-1];
        define( 'PERMISOS_MOD', permisos_modulo( '', $pag_ini ) );
   }
    else
         define( 'PERMISOS_MOD', permisos_modulo( $ses_mid ) );
    if( !se_puede( 's', PERMISOS_MOD ) ) {
       
         header('Location: ../start/sin_perm_01.php' );
        tep_exit();
   }
}
?>
