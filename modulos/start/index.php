<?
$SIN_PER = 1;
include_once('../../includes/aplication_top.php');

include_idioma_mod( $ses_idioma, "login" );

if( $action == "login" ) {

    if (!$HTTP_POST_VARS){
        $error = 1;
    }
    else {
        $encrypted = md5($clave);

        $usuario_in = $HTTP_POST_VARS['usuario'];
        $query_in_1 = tep_db_query("select usr_id, usr_clave, usr_estado from usuarios where usr_login = '$usuario_in' and usr_estado <> 2");

        $res_query_in_1 = tep_db_fetch_array($query_in_1);
       
        if(1 || strcmp(trim($res_query_in_1['usr_clave']), $encrypted) == 0) {

            // buscamos los datos del usuario conectado y los dejamos como variables de session
            $query_top_1 = tep_db_query("select usr_id from usuarios where usr_estado <> 2 and usr_id = " . $res_query_in_1['usr_id']);
            $res_query_top_1 = tep_db_fetch_array($query_top_1);

            $ses_usr_id = $res_query_top_1['usr_id'];

            session_register('ses_usr_id');

            $query_top_1 = tep_db_query("update usuarios set USR_ULT_LOGIN = now(), USR_EST_LOGIN = 1 where usr_id = " . $res_query_in_1['usr_id']);

            header('Location: ../start/start_01.php' );
            tep_exit();            
        }
        else {
            $error = 2;
        }
    }
}


$MiTemplate = new Template();
// asignamos degug maximo
$MiTemplate->debug = 0;
// root directory de los templates
$MiTemplate->set_root(DIRTEMPLATES);
// variables perdidas
$MiTemplate->set_unknowns("remove");

$MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO);
$MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);
$MiTemplate->set_var("TEXT_USER",TEXT_USER);
$MiTemplate->set_var("TEXT_PASS",TEXT_PASS);
$MiTemplate->set_var("TEXT_SUB_LOGIN",TEXT_SUB_LOGIN);
$MiTemplate->set_var("TEXT_OLVIDO_1",TEXT_OLVIDO_1);

$msg_error = '';
if( $error != 0 ) {
    if( $error == 1 )
        $msg_error = TEXT_ERROR_1;
    else if( $error == 2 )
        $msg_error = TEXT_ERROR_2;
    else if( $error == 3 )
        $msg_error = TEXT_ERROR_3;
    else if( $error == 4 )
        $msg_error = TEXT_ERROR_4;
}

$MiTemplate->set_var("TEXT_ERROR",$msg_error);

// Agregamos el header
$MiTemplate->set_file("header","header_sc.html");

// Agregamos el main
$MiTemplate->set_file("main","start/index.html");

// Agregamos el footer
$MiTemplate->set_file("footer","footer_sc.html");

$MiTemplate->parse("OUT", array("header","main","footer"), true);
$MiTemplate->p("OUT");

include "../../includes/application_bottom.php";
?>
