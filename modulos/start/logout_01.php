<?
$SIN_PER = 1;
include "../../includes/aplication_top.php";

include_idioma_mod( $ses_idioma, "logout" );

$nombre_pagina = NOMBRE_PAGINA;

if( $ses_usr_id != '' )
    $query_top_1 = tep_db_query("update usuarios set USR_EST_LOGIN = 0 where usr_id = " . $ses_usr_id);

session_destroy();

header('Location: ../../index.html' );
tep_exit();            

?>
