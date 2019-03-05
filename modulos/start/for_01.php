<?
$SIN_PER = 1;
include "../../includes/aplication_top.php";

include_idioma_mod( $ses_idioma, "olvido" );

function form_clave() {
    $MiTemplate = new Template();
    // asignamos degug maximo
    $MiTemplate->debug = 0;
    // root directory de los templates
    $MiTemplate->set_root(DIRTEMPLATES);
    // variables perdidas
    $MiTemplate->set_unknowns("keep");

    $MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO);
    $MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);
    $MiTemplate->set_var("TEXT_OLVIDO_1",TEXT_OLVIDO_1);
    $MiTemplate->set_var("TEXT_SUB_LOGIN",TEXT_SUB_LOGIN);
    $MiTemplate->set_var("TEXT_USER",TEXT_USER);

    // Agregamos el header
    $MiTemplate->set_file("header","header_sc.html");

    // Agregamos el main
    $MiTemplate->set_file("main","start/for_01.html");

    // Agregamos el footer
    $MiTemplate->set_file("footer","footer.html");

    $MiTemplate->parse("OUT", array("header","main","footer"), true);
    $MiTemplate->p("OUT");

}

function env_clave( $usuario ) {
    $query_1 = "select usr_id, usr_nombres, usr_apellidos, usr_email from usuarios where usr_login = '$usuario' and usr_estado <> 2";
    $res_1 = tep_db_fetch_array( tep_db_query($query_1) );
    $newpass = random_password('8');

    $query_2 = "update usuarios set usr_clave = md5('$newpass') where usr_id = " . $res_1['usr_id'];
    tep_db_query($query_2);

    $mail_para = $res_1['usr_email'];
    $mail_tit = TEXT_MAIL_TIT;
    $mail_from = MAIL_FROM;
    $mail_msg = sprintf( TEXT_MAIL_MEN, ucwords( $res_1['usr_nombres'] . " " . $res_1['usr_apellidos'] ), $newpass );

    if( KID_ENVIO_MAIL )
        mail( $mail_para , $mail_tit, $mail_msg, 'From: ' . $mail_from);
    else
        writelog( "mail( $mail_para , $mail_tit, $mail_msg, 'From: ' . $mail_from)" );

    header('Location: index.php?error=4' );
    tep_exit();            
}

if( $action == 'login' )
    env_clave( $usuario );
else
    form_clave();


include "../../includes/application_bottom.php";

?>