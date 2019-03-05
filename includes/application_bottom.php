<?
if( 0 ) {
if( $ses_usr_id ) {
    $query_1 = "select msg_pri_id from msg_pri where msg_pri_usr_to = $ses_usr_id and msg_pri_visto = 0";
    $db_1 = tep_db_query($query_1);
    if( tep_db_num_rows( $db_1 ) > 0 ) {
        // Mostramos los mensajes privados que han llegado
        $MiTemplate_1 = new Template();
        // asignamos degug maximo
        $MiTemplate_1->debug = 0;
        // root directory de los templates
        $MiTemplate_1->set_root(DIRTEMPLATES);
        // variables perdidas
        $MiTemplate_1->set_unknowns("remove");

        $MiTemplate_1->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
        $MiTemplate_1->set_var("TEXT_TITULO",TEXT_TITULO);
        $MiTemplate_1->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));

        $MiTemplate_1->set_var("TEXT_MSG_FROM",TEXT_MSG_FROM);
        $MiTemplate_1->set_var("TIT_BARRA_MSG_PRI",TIT_BARRA_MSG_PRI);

         // Agregamos el main
        $MiTemplate_1->set_file("maincajas","msg_pri/caja_mensajes.html");

        $query = "select msg_pri_tipo, SUBSTRING(msg_pri_text,1,80) as msg_pri_text, msg_pri_usr_from from msg_pri where msg_pri_usr_to = $ses_usr_id and msg_pri_visto = 0 order by msg_pri_fecha";
        $rq = tep_db_query($query);
        if( tep_db_num_rows($rq) > 0 ) {
            $res = mysql_fetch_assoc( $rq );
            $arr_k = array_keys ($res);

            $rq = tep_db_query($query);
            $MiTemplate_1->set_block("maincajas", "Mensajes", "PBLMensajes");
            while( $res = tep_db_fetch_array( $rq ) ) {
                for( $i = 0; $i < sizeof( $arr_k ); $i++ ) {
                    $MiTemplate_1->set_var($arr_k[$i],tohtml( $res[$arr_k[$i]] ));
                }
                if( $res['msg_pri_tipo'] == 0 )
                    $MiTemplate_1->set_var("IMAGEN_TIPO",'mail.gif');
                if( $res['msg_pri_visto'] == 0 )
                    $MiTemplate_1->set_var("IMAGEN_VISTO",'new_day.gif');
                else
                    $MiTemplate_1->set_var("IMAGEN_VISTO",'1x1trans.gif');

                $MiTemplate_1->parse("PBLMensajes", "Mensajes", true);
            }
        }

        $MiTemplate_1->parse("OUT_C", array("maincajas"), true);
        $MiTemplate_1->p("OUT_C");
    }
}

if( KEY_MULTI_LEN ) {
    $MiTemplate_1 = new Template();
    // asignamos degug maximo
    $MiTemplate_1->debug = 0;
    // root directory de los templates
    $MiTemplate_1->set_root(DIRTEMPLATES);
    // variables perdidas
    $MiTemplate_1->set_unknowns("keep");

     // Agregamos el main
    $MiTemplate_1->set_file("maincajas","start/caja_idiomas.html");

    $MiTemplate_1->set_block("maincajas", "Idiomas", "PBLIdiomas");
    $handle=opendir('../../includes/idiomas');
    while ($file = readdir($handle)) {
        if( strpos($file, ".") === false ) {
            $MiTemplate_1->set_var("IDIOMA",$file);
            $MiTemplate_1->parse("PBLIdiomas", "Idiomas", true);
        }
    }
    closedir($handle);

    $MiTemplate_1->parse("OUT_C", array("maincajas"), true);
    $MiTemplate_1->p("OUT_C");

}
}
?>
<?
include "../../includes/trackhack.php";
?>