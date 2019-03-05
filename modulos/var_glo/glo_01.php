<?
$pag_ini = '../var_glo/glo_01.php';
include "../../includes/aplication_top.php";

include_idioma_mod( $ses_idioma, "glo" );

$nombre_pagina = NOMBRE_PAGINA;

/**********************************************************************************************/

function listado_grupos() {
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
    $MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));

    $MiTemplate->set_var("TEXT_TITLE_1",TEXT_TITLE_1);
    $MiTemplate->set_var("TEXT_TITLE_2",TEXT_TITLE_2);

    // Agregamos el header
    $MiTemplate->set_file("header","header_cc.html");

    // Agregamos la barra de herramientas
    if( se_puede( 'i', PERMISOS_MOD ) )
        $barra_her = kid_href( 'glo_01.php', "action=ins", BOTON_AGREGAR_IMG, MENU_ADD_GRUPO_ALT, '' );
    $MiTemplate->set_var("BARRA_HERRAMIENTAS",$barra_her);
    $MiTemplate->set_file("barra_her","barra.html");
    $MiTemplate->parse("OUT_BARRA","barra_her");

    // Agregamos el main
    $MiTemplate->set_file("main","var_glo/list_grupos.html");

    $MiTemplate->set_block("main", "Grupos", "PBLGrupos");
    $query = "select glo_id, glo_titulo, glo_descripcion from glo_grupos order by glo_titulo";
    $rq = tep_db_query($query);
    $res = mysql_fetch_assoc( $rq );
    $arr_k = array_keys ($res);

    $rq = tep_db_query($query);
    while( $res = tep_db_fetch_array( $rq ) ) {
        for( $i = 0; $i < sizeof( $arr_k ); $i++ ) {
            $MiTemplate->set_var($arr_k[$i],tohtml( $res[$arr_k[$i]] ));
        }
        $msg_aux = kid_href( 'glo_01.php', 'action=lisvar&glo_id='.$res['glo_id'], BOTON_LISTAR_CHI_IMG, TEXT_VER_VARIABLES_GRUPO, '' );
        if( se_puede( 'u', PERMISOS_MOD ) )
            $msg_aux .= ' ' . kid_href( 'glo_01.php', 'action=updgru&glo_id='.$res['glo_id'], BOTON_MODIFICAR_IMG, TEXT_MODIFICAR_GRUPO_ALT, '' );
        if( se_puede( 'd', PERMISOS_MOD ) )
            $msg_aux .= ' ' . kid_href( 'javascript:validar_eliminar( "'.CONFIRM_ELIMINAR_GRUPO.'", "glo_01.php?action=delgru&glo_id='.$res['glo_id'].'" ); //', '', BOTON_ELIMINAR_IMG, TEXT_ELIMINAR_GRUPO_ALT, '' );

        $MiTemplate->set_var('ACCIONES',$msg_aux);
        $MiTemplate->parse("PBLGrupos", "Grupos", true);
    }

    // Agregamos el footer
    $MiTemplate->set_file("footer","footer.html");

    $MiTemplate->pparse("OUT_H", array("header"), false);
    include "../../menu/menu.php";
    $MiTemplate->parse("OUT_M", array("main","footer"), true);
    $MiTemplate->p("OUT_M");

}

function form_grupo( $action, $glo_id ) {
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
    $MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));
    $MiTemplate->set_var("CAMPO_OBLIGATORIO",MOD_CAMPO_OBLIGATORIO);

    $MiTemplate->set_var("TEXT_CAMPO_1",TEXT_CAMPO_1);
    $MiTemplate->set_var("TEXT_CAMPO_2",TEXT_CAMPO_2);

    // Recuperamos los datos del grupo
    if( $action == 'updgru' ) {
        $query = "select glo_titulo, glo_descripcion from glo_grupos where glo_id = $glo_id";
        query_to_set_var( $query, &$MiTemplate, 0, '', '' );
        $MiTemplate->set_var("TEXT_TITULO_SUB",TEXT_TITULO_MOD_GRUPO);
        $MiTemplate->set_var("BOTON",BOTON_MODIFICAR);
        $MiTemplate->set_var("GLO_ID",$glo_id);
        $MiTemplate->set_var("ACTION",'updgru1');
    }
    else {
        $MiTemplate->set_var("TEXT_TITULO_SUB",TEXT_TITULO_ADD_GRUPO);
        $MiTemplate->set_var("BOTON",BOTON_AGREGAR);
        $MiTemplate->set_var("ACTION",'insgru');
    }

    // Agregamos el header
    $MiTemplate->set_file("header","header_cc.html");

    // Agregamos la barra de herramientas
    $barra_her = kid_href( 'glo_01.php', "", BOTON_VOLVER_IMG, BOTON_VOLVER, '' );
    if( se_puede( 'i', PERMISOS_MOD ) )
        $barra_her .= ' ' . kid_href( 'glo_01.php', "action=ins", BOTON_AGREGAR_IMG, MENU_ADD_GRUPO_ALT, '' );
    $MiTemplate->set_var("BARRA_HERRAMIENTAS",$barra_her);
    $MiTemplate->set_file("barra_her","barra.html");
    $MiTemplate->parse("OUT_BARRA","barra_her");

    // Agregamos el main
    $MiTemplate->set_file("main","var_glo/form_grupo.html");

    // Agregamos el footer
    $MiTemplate->set_file("footer","footer.html");

    $MiTemplate->pparse("OUT_H", array("header"), false);
    include "../../menu/menu.php";
    $MiTemplate->parse("OUT_M", array("main","footer"), true);
    $MiTemplate->p("OUT_M");

}

function insert_grupo( $c_1,$c_2 ) {
    global $ses_usr_id;

    $usr_nombre = get_nombre_usr( $ses_usr_id );

    $query = "insert into glo_grupos( glo_titulo, glo_descripcion, glo_orden, glo_usr_crea, glo_fec_crea ) values( '$c_1', '$c_2', 0, '$usr_nombre', now() )";
    tep_db_query($query);

    header( "Location: glo_01.php" );
    tep_exit();
}

function delete_grupo( $glo_id ) {
    $query = "delete from glo_grupos where glo_id = $glo_id";
    tep_db_query($query);

    header( "Location: glo_01.php" );
    tep_exit();
}

function update_grupo( $glo_id,$c_1,$c_2 ) {
    global $ses_usr_id;

    $usr_nombre = get_nombre_usr( $ses_usr_id );

    $query = "update glo_grupos set glo_titulo = '$c_1', glo_descripcion = '$c_2', glo_usr_mod = '$usr_nombre', glo_fec_mod = now() where glo_id = $glo_id";
    tep_db_query($query);

    header( "Location: glo_01.php" );
    tep_exit();

}

function listado_variables( $glo_id ) {
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
    $MiTemplate->set_var("SUBTITULO1",TEXT_VER_VARIABLES_GRUPO);
    $MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));

    $MiTemplate->set_var("TEXT_CAMPO_VAR_1",TEXT_CAMPO_VAR_1);
    $MiTemplate->set_var("TEXT_CAMPO_VAR_2",TEXT_CAMPO_VAR_2);
    $MiTemplate->set_var("TEXT_CAMPO_VAR_3",TEXT_CAMPO_VAR_3);
    $MiTemplate->set_var("TEXT_CAMPO_VAR_4",TEXT_CAMPO_VAR_4);

    // Agregamos el header
    $MiTemplate->set_file("header","header_cc.html");

    // Agregamos la barra de herramientas
    $barra_her = kid_href( 'glo_01.php', "", BOTON_VOLVER_IMG, BOTON_VOLVER, '' );
    if( se_puede( 'i', PERMISOS_MOD ) )
        $barra_her .= ' ' . kid_href( 'glo_01.php', "action=insvar&glo_id=$glo_id", BOTON_AGREGAR_IMG, MENU_ADD_VARIABLE_ALT, '' );
    $MiTemplate->set_var("BARRA_HERRAMIENTAS",$barra_her);
    $MiTemplate->set_file("barra_her","barra.html");
    $MiTemplate->parse("OUT_BARRA","barra_her");

    // Agregamos el main
    $MiTemplate->set_file("main","var_glo/list_variables.html");

    // agregamos el bloque para las variables al main
    $MiTemplate->set_block("main", "Variables", "PBLVariables");
    $query = "select var_id, var_titulo, var_llave, var_valor, var_descripcion from glo_variables where var_glo_id = $glo_id order by var_titulo";
    $rq = tep_db_query($query);
    if( tep_db_num_rows($rq) > 0 ) {
        $res = mysql_fetch_assoc( $rq );
        $arr_k = array_keys ($res);

        $rq = tep_db_query($query);
        while( $res = tep_db_fetch_array( $rq ) ) {
            for( $i = 0; $i < sizeof( $arr_k ); $i++ ) {
                $MiTemplate->set_var($arr_k[$i],tohtml( $res[$arr_k[$i]] ));
            }
            $msg_aux = '';
            if( se_puede( 'u', PERMISOS_MOD ) )
                $msg_aux .= ' ' . kid_href( 'glo_01.php', "action=updvar&glo_id=$glo_id&var_id=".$res['var_id'], BOTON_MODIFICAR_IMG, TEXT_MODIFICAR_VARIABLE_ALT, '' );
            if( se_puede( 'd', PERMISOS_MOD ) )
                $msg_aux .= ' ' . kid_href( 'javascript:validar_eliminar( "'.CONFIRM_ELIMINAR_VARIABLE.'", "glo_01.php?action=delvar&glo_id='.$glo_id.'&var_id='.$res['var_id'].'" ); //', '', BOTON_ELIMINAR_IMG, TEXT_ELIMINAR_VARIABLE_ALT, '' );


            $MiTemplate->set_var('ACCIONES',$msg_aux);
            $MiTemplate->parse("PBLVariables", "Variables", true);
        }
    }

    // Agregamos el footer
    $MiTemplate->set_file("footer","footer.html");

    $MiTemplate->pparse("OUT_H", array("header"), false);
    include "../../menu/menu.php";
    $MiTemplate->parse("OUT_M", array("main","footer"), true);
    $MiTemplate->p("OUT_M");

}

function form_variable( $action, $glo_id, $var_id ) {
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
    $MiTemplate->set_var("SUBTITULO1",TEXT_2);
    $MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));
    $MiTemplate->set_var("CAMPO_OBLIGATORIO",MOD_CAMPO_OBLIGATORIO);

    $MiTemplate->set_var("TEXT_CAMPO_VAR_1",TEXT_CAMPO_VAR_1);
    $MiTemplate->set_var("TEXT_CAMPO_VAR_2",TEXT_CAMPO_VAR_2);
    $MiTemplate->set_var("TEXT_CAMPO_VAR_3",TEXT_CAMPO_VAR_3);
    $MiTemplate->set_var("TEXT_CAMPO_VAR_4",TEXT_CAMPO_VAR_4);

    $MiTemplate->set_var("TEXT_CAMPO_1",TEXT_CAMPO_1);
    $MiTemplate->set_var("TEXT_CAMPO_2",TEXT_CAMPO_2);

    // Recuperamos los datos del grupo
    if( $action == 'updvar' ) {
        $query = "select var_titulo, var_descripcion, var_llave, var_valor from glo_variables where var_id = $var_id";
        query_to_set_var( $query, &$MiTemplate, 0, '', '' );
        $MiTemplate->set_var("TEXT_TITULO_SUB",TEXT_TITULO_MOD_VARIABLE);
        $MiTemplate->set_var("BOTON",BOTON_MODIFICAR);
        $MiTemplate->set_var("GLO_ID",$glo_id);
        $MiTemplate->set_var("VAR_ID",$var_id);
        $MiTemplate->set_var("ACTION",'updvar1');
    }
    else {
        $MiTemplate->set_var("TEXT_TITULO_SUB",TEXT_TITULO_ADD_VARIABLE);
        $MiTemplate->set_var("BOTON",BOTON_AGREGAR);
        $MiTemplate->set_var("GLO_ID",$glo_id);
        $MiTemplate->set_var("ACTION",'insvar1');
    }

    // Agregamos el header
    $MiTemplate->set_file("header","header_cc.html");

    // Agregamos la barra de herramientas
    $barra_her = kid_href( 'glo_01.php', "action=lisvar&glo_id=$glo_id", BOTON_VOLVER_IMG, BOTON_VOLVER, '' );
    if( se_puede( 'i', PERMISOS_MOD ) )
        $barra_her .= ' ' . kid_href( 'glo_01.php', "action=insvar&glo_id=$glo_id", BOTON_AGREGAR_IMG, MENU_ADD_VARIABLE_ALT, '' );
    $MiTemplate->set_var("BARRA_HERRAMIENTAS",$barra_her);
    $MiTemplate->set_file("barra_her","barra.html");
    $MiTemplate->parse("OUT_BARRA","barra_her");

    // Agregamos el main
    $MiTemplate->set_file("main","var_glo/form_variable.html");

    // Agregamos el footer
    $MiTemplate->set_file("footer","footer.html");

    $MiTemplate->pparse("OUT_H", array("header"), false);
    include "../../menu/menu.php";
    $MiTemplate->parse("OUT_M", array("main","footer"), true);
    $MiTemplate->p("OUT_M");

}

function insert_variable( $glo_id, $c_1, $c_2, $c_3, $c_4 ) {
    global $ses_usr_id;
    $max_orden = 0;
    $query = "insert into glo_variables( var_glo_id, var_titulo, var_descripcion, var_llave, var_valor, var_orden, var_usr_crea, var_fec_crea ) values( $glo_id, '$c_1', '$c_2', '$c_3', '$c_4', $max_orden, '$usr_nombre', now() )";
    tep_db_query($query);

    header( "Location: glo_01.php?action=lisvar&glo_id=$glo_id" );
    tep_exit();
}

function delete_variable( $glo_id, $var_id ) {
    $query = "delete from glo_variables where var_id = $var_id";
    tep_db_query($query);

    header( "Location: glo_01.php?action=lisvar&glo_id=$glo_id" );
    tep_exit();
}

function update_variable( $c_1, $c_2, $c_3, $c_4, $var_id, $glo_id ) {
    global $ses_usr_id;
    $usr_nombre = get_nombre_usr( $ses_usr_id );

    $query = "update glo_variables set var_titulo = '$c_1', var_descripcion = '$c_2', var_llave = '$c_3', var_valor = '$c_4', var_usr_mod = '$usr_nombre', var_fec_mod = now() where var_id = $var_id";
    tep_db_query($query);

    header( "Location: glo_01.php?action=lisvar&glo_id=$glo_id" );
    tep_exit();
}

/**********************************************************************************************/

if($action == 'ins')
    form_grupo( $action, $glo_id );
else if($action == 'insgru')
    insert_grupo( $c_1,$c_2 );
else if($action == 'delgru')
    delete_grupo( $glo_id );
else if( $action == 'updgru' )
    form_grupo( $action, $glo_id );
else if( $action == 'updgru1' )
    update_grupo( $glo_id,$c_1,$c_2 );
else if( $action == 'lisvar' )
    listado_variables( $glo_id );
else if( $action == 'insvar' )
    form_variable( $action, $glo_id, $var_id );
else if( $action == 'insvar1' )
    insert_variable( $glo_id, $c_1, $c_2, $c_3, $c_4 );
else if( $action == 'delvar' )
    delete_variable( $glo_id, $var_id );
else if( $action == 'updvar' )
    form_variable( $action, $glo_id, $var_id );
else if( $action == 'updvar1' )
    update_variable( $c_1, $c_2, $c_3, $c_4, $var_id, $glo_id );
else
    listado_grupos();

/**********************************************************************************************/

include "../../includes/application_bottom.php";

?>
