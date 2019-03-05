<?
$pag_ini = '../track/track_01.php';
include "../../includes/aplication_top.php";

include_idioma_mod( $ses_idioma, "track" );

/**********************************************************************************************/

function tracking_to_csv() {

    $fname = 'tracking.xls';

    $vcstr = '';

    $query_1 = "select * from tracking limit 1";
    $rq_1 = tep_db_query($query_1);
    $res_1 = mysql_fetch_assoc( $rq_1 );
    $arr_k = array_keys ($res_1);
    for( $i = 0; $i < sizeof( $arr_k ); $i++ ) {
        $vcstr .= $arr_k[$i] . "\t";
    }
    $vcstr .= "<br>\n";
    $query_1 = "select * from tracking";
    $rq_1 = tep_db_query($query_1);
    while( $res_1 = tep_db_fetch_array( $rq_1 ) ) {
        for( $i = 0; $i < sizeof( $arr_k ); $i++ ) {
            $vcstr .= $res_1[$i] . "\t";;
        }
        $vcstr .= "\n";
    }


    header("Content-Type: text/x-xls");
    header("Content-Length: ".strlen($vcstr));
    header("Content-Disposition: attachment; filename=".$fname);
    header("Content-Transfer-Encoding: 7bit");
    header("Content-Description: xls-export");

    echo $vcstr;

    //header( "Location: track_01.php" );
    tep_exit();
}

function delete_tracking() {
    $query_1 = "delete from tracking";
    tep_db_query($query_1);
    header( "Location: track_01.php" );
    tep_exit();
}

function tracking() {
    global $ses_usr_id;

    $MiTemplate = new Template();
    // asignamos degug maximo
    $MiTemplate->debug = 0;
    // root directory de los templates
    $MiTemplate->set_root(DIRTEMPLATES);
    // variables perdidas
    $MiTemplate->set_unknowns("remove");

    $MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
    $MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);
    $MiTemplate->set_var("SUBTITULO1",TEXT_CONSULTA_1_TITLE);
    $MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));

    $MiTemplate->set_var("TEXT_CAMPO_1",TEXT_CAMPO_1);
    $MiTemplate->set_var("TEXT_CAMPO_2",TEXT_CAMPO_2);
    $MiTemplate->set_var("TEXT_CAMPO_3",TEXT_CAMPO_3);
    $MiTemplate->set_var("TEXT_CAMPO_4",TEXT_CAMPO_4);
    $MiTemplate->set_var("TEXT_CAMPO_5",TEXT_CAMPO_5);
    $MiTemplate->set_var("TEXT_CAMPO_6",TEXT_CAMPO_6);

    // Agregamos la barra de herramientas
    $barra_her = kid_href( 'track_01.php', 'action=con1', TEXT_CONSULTA_1, TEXT_CONSULTA_1_ALT, '' );
    if( se_puede( 'd', PERMISOS_MOD ) )
        $barra_her .= ' | ' . kid_href( 'javascript:validar_eliminar( "'.CONFIRM_ELIMINAR_TRA.'", "track_01.php?action=con4" ); //', '', TEXT_CONSULTA_4, TEXT_CONSULTA_4_ALT, '' );
    $barra_her .= ' | ' . kid_href( 'track_01.php', 'action=con2', TEXT_CONSULTA_2, TEXT_CONSULTA_2_ALT, '' );
    $barra_her .= ' | ' . kid_href( 'track_01.php', 'action=con3', TEXT_CONSULTA_3, TEXT_CONSULTA_3_ALT, '' );
    $barra_her .= ' | ' . kid_href( 'track_01.php', 'action=con9', TEXT_CONSULTA_9, TEXT_CONSULTA_9_ALT, '' );
    $barra_her .= ' | ' . kid_href( 'track_01.php', 'action=con7', TEXT_CONSULTA_7, TEXT_CONSULTA_7_ALT, '' );
    $MiTemplate->set_var("BARRA_HERRAMIENTAS",$barra_her);
    $MiTemplate->set_file("barra_her","barra.html");
    $MiTemplate->parse("OUT_BARRA","barra_her");

    // Agregamos el header
    $MiTemplate->set_file("header","header_cc.html");

    // Agregamos el main
    $MiTemplate->set_file("main","track/track_02.html");

    $query = "select * from tracking order by tra_tracktime desc limit 50";
    $MiTemplate->set_block("main", "Track1", "PBLTrack1");
    query_to_set_var( $query, &$MiTemplate, 1, 'PBLTrack1', 'Track1' );

    // Agregamos el footer
    $MiTemplate->set_file("footer","footer.html");

    $MiTemplate->pparse("OUT_H", array("header"), false);
    include "../../menu/menu.php";
    $MiTemplate->parse("OUT_M", array("main","footer"), true);
    $MiTemplate->p("OUT_M");

}

function usuarios() {
    global $ses_usr_id;

    $MiTemplate = new Template();
    // asignamos degug maximo
    $MiTemplate->debug = 0;
    // root directory de los templates
    $MiTemplate->set_root(DIRTEMPLATES);
    // variables perdidas
    $MiTemplate->set_unknowns("remove");

    $MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
    $MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);
    $MiTemplate->set_var("SUBTITULO1",TEXT_CONSULTA_3_TITLE);
    $MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));

    $MiTemplate->set_var("TEXT_CAMPO_1",TEXT_CAMPO_1);
    $MiTemplate->set_var("TEXT_CAMPO_2",TEXT_CAMPO_2);
    $MiTemplate->set_var("TEXT_CAMPO_3",TEXT_CAMPO_3);
    $MiTemplate->set_var("TEXT_CAMPO_4",TEXT_CAMPO_4);
    $MiTemplate->set_var("TEXT_CAMPO_5",TEXT_CAMPO_5);
    $MiTemplate->set_var("TEXT_CAMPO_6",TEXT_CAMPO_6);

    // Agregamos la barra de herramientas
    $barra_her = kid_href( 'track_01.php', 'action=con1', TEXT_CONSULTA_1, TEXT_CONSULTA_1_ALT, '' );
    if( se_puede( 'd', PERMISOS_MOD ) )
        $barra_her .= ' | ' . kid_href( 'javascript:validar_eliminar( "'.CONFIRM_ELIMINAR_TRA.'", "track_01.php?action=con4" ); //', '', TEXT_CONSULTA_4, TEXT_CONSULTA_4_ALT, '' );
    $barra_her .= ' | ' . kid_href( 'track_01.php', 'action=con2', TEXT_CONSULTA_2, TEXT_CONSULTA_2_ALT, '' );
    $barra_her .= ' | ' . kid_href( 'track_01.php', 'action=con3', TEXT_CONSULTA_3, TEXT_CONSULTA_3_ALT, '' );
    $barra_her .= ' | ' . kid_href( 'track_01.php', 'action=con9', TEXT_CONSULTA_9, TEXT_CONSULTA_9_ALT, '' );
    $barra_her .= ' | ' . kid_href( 'track_01.php', 'action=con7', TEXT_CONSULTA_7, TEXT_CONSULTA_7_ALT, '' );
    $MiTemplate->set_var("BARRA_HERRAMIENTAS",$barra_her);
    $MiTemplate->set_file("barra_her","barra.html");
    $MiTemplate->parse("OUT_BARRA","barra_her");

    // Agregamos el header
    $MiTemplate->set_file("header","header_cc.html");

    // Agregamos el main
    $MiTemplate->set_file("main","track/track_01.html");

    $query = "SELECT usr_id, max(tra_tracktime) as conexion, usr_login, usr_nombres, usr_apellidos, count(*) as cantidad FROM tracking, usuarios where tra_usr_id = usr_id group by tra_usr_id order by usr_login asc";
    $MiTemplate->set_block("main", "Track1", "PBLTrack1");
    query_to_set_var( $query, &$MiTemplate, 1, 'PBLTrack1', 'Track1' );

    // Agregamos el footer
    $MiTemplate->set_file("footer","footer.html");

    $MiTemplate->pparse("OUT_H", array("header"), false);
    include "../../menu/menu.php";
    $MiTemplate->parse("OUT_M", array("main","footer"), true);
    $MiTemplate->p("OUT_M");


}

function usuarios_dia() {
    global $ses_usr_id;

    $MiTemplate = new Template();
    // asignamos degug maximo
    $MiTemplate->debug = 0;
    // root directory de los templates
    $MiTemplate->set_root(DIRTEMPLATES);
    // variables perdidas
    $MiTemplate->set_unknowns("remove");

    $MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
    $MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);
    $MiTemplate->set_var("SUBTITULO1",TEXT_CONSULTA_3_TITLE);
    $MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));

    $MiTemplate->set_var("TEXT_CAMPO_1",TEXT_CAMPO_1);
    $MiTemplate->set_var("TEXT_CAMPO_2",TEXT_CAMPO_2);
    $MiTemplate->set_var("TEXT_CAMPO_3",TEXT_CAMPO_3);
    $MiTemplate->set_var("TEXT_CAMPO_4",TEXT_CAMPO_4);
    $MiTemplate->set_var("TEXT_CAMPO_5",TEXT_CAMPO_5);
    $MiTemplate->set_var("TEXT_CAMPO_6",TEXT_CAMPO_6);

    // Agregamos la barra de herramientas
    $barra_her = kid_href( 'track_01.php', 'action=con1', TEXT_CONSULTA_1, TEXT_CONSULTA_1_ALT, '' );
    if( se_puede( 'd', PERMISOS_MOD ) )
        $barra_her .= ' | ' . kid_href( 'javascript:validar_eliminar( "'.CONFIRM_ELIMINAR_TRA.'", "track_01.php?action=con4" ); //', '', TEXT_CONSULTA_4, TEXT_CONSULTA_4_ALT, '' );
    $barra_her .= ' | ' . kid_href( 'track_01.php', 'action=con2', TEXT_CONSULTA_2, TEXT_CONSULTA_2_ALT, '' );
    $barra_her .= ' | ' . kid_href( 'track_01.php', 'action=con3', TEXT_CONSULTA_3, TEXT_CONSULTA_3_ALT, '' );
    $barra_her .= ' | ' . kid_href( 'track_01.php', 'action=con9', TEXT_CONSULTA_9, TEXT_CONSULTA_9_ALT, '' );
    $barra_her .= ' | ' . kid_href( 'track_01.php', 'action=con7', TEXT_CONSULTA_7, TEXT_CONSULTA_7_ALT, '' );
    $MiTemplate->set_var("BARRA_HERRAMIENTAS",$barra_her);
    $MiTemplate->set_file("barra_her","barra.html");
    $MiTemplate->parse("OUT_BARRA","barra_her");

    // Agregamos el header
    $MiTemplate->set_file("header","header_cc.html");

    // Agregamos el main
    $MiTemplate->set_file("main","track/track_03.html");

    $query = "SELECT usr_id, max(tra_tracktime) as conexion, usr_login, usr_nombres, usr_apellidos, usr_pais, count(*) as cantidad, DATE_FORMAT( tra_tracktime, '%d') as dia FROM `tracking`, usuarios where tra_usr_id = usr_id group by dia, tra_usr_id order by dia, tra_tracktime";    
    $MiTemplate->set_block("main", "Track1", "PBLTrack1");
    query_to_set_var( $query, &$MiTemplate, 1, 'PBLTrack1', 'Track1' );

    // Agregamos el footer
    $MiTemplate->set_file("footer","footer.html");

    $MiTemplate->pparse("OUT_H", array("header"), false);
    include "../../menu/menu.php";
    $MiTemplate->parse("OUT_M", array("main","footer"), true);
    $MiTemplate->p("OUT_M");


    global $mode, $order;

}

function form_log() {
    global $ses_usr_id;

    $MiTemplate = new Template();
    // asignamos degug maximo
    $MiTemplate->debug = 0;
    // root directory de los templates
    $MiTemplate->set_root(DIRTEMPLATES);
    // variables perdidas
    $MiTemplate->set_unknowns("remove");

    $MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
    $MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);
    $MiTemplate->set_var("SUBTITULO1",TEXT_CONSULTA_7_TITLE);
    $MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));

    $MiTemplate->set_var("TEXT_CAMPO_1",TEXT_CAMPO_1);
    $MiTemplate->set_var("TEXT_CAMPO_2",TEXT_CAMPO_2);
    $MiTemplate->set_var("TEXT_CAMPO_3",TEXT_CAMPO_3);
    $MiTemplate->set_var("TEXT_CAMPO_4",TEXT_CAMPO_4);
    $MiTemplate->set_var("TEXT_CAMPO_5",TEXT_CAMPO_5);
    $MiTemplate->set_var("TEXT_CAMPO_6",TEXT_CAMPO_6);

    // Agregamos la barra de herramientas
    $barra_her = kid_href( 'track_01.php', 'action=con1', TEXT_CONSULTA_1, TEXT_CONSULTA_1_ALT, '' );
    if( se_puede( 'd', PERMISOS_MOD ) )
        $barra_her .= ' | ' . kid_href( 'javascript:validar_eliminar( "'.CONFIRM_ELIMINAR_TRA.'", "track_01.php?action=con4" ); //', '', TEXT_CONSULTA_4, TEXT_CONSULTA_4_ALT, '' );
    $barra_her .= ' | ' . kid_href( 'track_01.php', 'action=con2', TEXT_CONSULTA_2, TEXT_CONSULTA_2_ALT, '' );
    $barra_her .= ' | ' . kid_href( 'track_01.php', 'action=con3', TEXT_CONSULTA_3, TEXT_CONSULTA_3_ALT, '' );
    $barra_her .= ' | ' . kid_href( 'track_01.php', 'action=con9', TEXT_CONSULTA_9, TEXT_CONSULTA_9_ALT, '' );
    $barra_her .= ' | ' . kid_href( 'track_01.php', 'action=con7', TEXT_CONSULTA_7, TEXT_CONSULTA_7_ALT, '' );
    $MiTemplate->set_var("BARRA_HERRAMIENTAS",$barra_her);
    $MiTemplate->set_file("barra_her","barra.html");
    $MiTemplate->parse("OUT_BARRA","barra_her");

    // Agregamos el header
    $MiTemplate->set_file("header","header_cc.html");

    // Agregamos el main
    $MiTemplate->set_file("main","track/track_04.html");

    $query = "SELECT usr_id, max(tra_tracktime) as conexion, usr_login, usr_nombres, usr_apellidos, count(*) as cantidad FROM tracking, usuarios where tra_usr_id = usr_id group by tra_usr_id order by usr_login asc";
    $MiTemplate->set_block("main", "Track1", "PBLTrack1");
    query_to_set_var( $query, &$MiTemplate, 1, 'PBLTrack1', 'Track1' );

    // Agregamos el footer
    $MiTemplate->set_file("footer","footer.html");

    $MiTemplate->pparse("OUT_H", array("header"), false);
    include "../../menu/menu.php";
    $MiTemplate->parse("OUT_M", array("main","footer"), true);
    $MiTemplate->p("OUT_M");

}

function archivo_log( $c_1 ) {
    if( $c_1 != '' ) {
        $filename = DIR_LOG.$c_1;
        if( $fd = fopen ($filename, "r") ) {
            $contents = fread ($fd, filesize ($filename));
            echo "<pre>$contents</pre>";
            fclose ($fd);
        }
    }
}

/**********************************************************************************************/

if( $action == 'con3' )
    usuarios();
else if( $action == 'con1' )
    tracking();
else if( $action == 'con4' )
    delete_tracking();
else if( $action == 'con2' )
    tracking_to_csv();
else if( $action == 'con9' )
    usuarios_dia();
else if( $action == 'con7' )
    form_log();
else if( $action == 'con7_1' )
    archivo_log( $c_1 );
else
    usuarios();

/**********************************************************************************************/

include "../../includes/application_bottom.php";


?>