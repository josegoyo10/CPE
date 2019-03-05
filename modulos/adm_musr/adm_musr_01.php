<?
$pag_ini = '../adm_musr/adm_musr_01.php';
include "../../includes/aplication_top.php";

include_idioma_mod( $ses_idioma, "adm_musr" );

$nombre_pagina = NOMBRE_PAGINA;

/**********************************************************************************************/

function listado_usuarios( $patron ) {
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

    $MiTemplate->set_var("TEXT_1",TEXT_1);
    $MiTemplate->set_var("TEXT_SUB_USUARIOS",TEXT_SUB_USUARIOS);
    $MiTemplate->set_var("TEXT_BUSCAR_CAMPO_1",TEXT_BUSCAR_CAMPO_1);
    $MiTemplate->set_var("BOTON_BUSCAR_USUARIO",BOTON_BUSCAR_USUARIO);
    $MiTemplate->set_var("TEXT_CAMPO_1",TEXT_CAMPO_1);
    $MiTemplate->set_var("TEXT_CAMPO_4",TEXT_CAMPO_4);
    $MiTemplate->set_var("TEXT_CAMPO_10",TEXT_CAMPO_10);
    $MiTemplate->set_var("patron",$patron);
    
    // Agregamos el header
    $MiTemplate->set_file("header","header_cc.html");

    // Agregamos la barra de herramientas
    //$barra_her = kid_href( 'adm_musr_01.php', '', BOTON_LISTAR_IMG, TEXT_TAG_LISTAR, '' );
    if( se_puede( 'i', PERMISOS_MOD ) )
        $barra_her .= " " . kid_href( 'adm_musr_01.php', 'action=ins', BOTON_AGREGAR_IMG, TEXT_TAG_AGREGAR, '' );
    $MiTemplate->set_var("BARRA_HERRAMIENTAS",$barra_her);
    $MiTemplate->set_file("barra_her","barra.html");
    $MiTemplate->parse("OUT_BARRA","barra_her");

    // Agregamos el main
    $MiTemplate->set_file("main","adm_musr/listado.html");

    // Recuperamos los modulos de la base
    if( $patron )
        $add_que = " and ( usr_login like '%$patron%' or usr_apellidos like '%$patron%' or usr_nombres like '%$patron%' )";
    $query = "select usr_id, usr_login, CONCAT( usr_nombres, ' ', usr_apellidos ) as nom_com from usuarios where usr_estado <> 2 $add_que order by usr_login";
    $rq = tep_db_query($query);

    $MiTemplate->set_block("main", "Usuarios", "PBLUsuarios");
    if( tep_db_num_rows($rq) > 0 ) {
        $res = mysql_fetch_assoc( $rq );
        $arr_k = array_keys ($res);

        $rq = tep_db_query($query);
        $primero = 1;
        while( $res = tep_db_fetch_array( $rq ) ) {
            for( $i = 0; $i < sizeof( $arr_k ); $i++ ) {
                $MiTemplate->set_var($arr_k[$i],tohtml( $res[$arr_k[$i]] ));
            }

            if( se_puede( 'u', PERMISOS_MOD ) )
                $msg_aux = kid_href( 'adm_musr_01.php', "action=upd&usr_id=".$res['usr_id'], BOTON_MODIFICAR_IMG, TEXT_TAG_MODIFICAR, '' ) . " ";
            if( se_puede( 'd', PERMISOS_MOD ) )
                $msg_aux .= kid_href( 'javascript:validar_eliminar( "'.CONFIRM_ELIMINAR_USR.'", "adm_musr_01.php?action=del&usr_id='.$res['usr_id'].'" ); //', '', BOTON_ELIMINAR_IMG, TEXT_TAG_ELIMINAR, '' );

            $MiTemplate->set_var('ACCIONES',$msg_aux);
            $MiTemplate->parse("PBLUsuarios", "Usuarios", true);
        }
    }
    else {
        $MiTemplate->set_var('ACCIONES','');
    }

    // Agregamos el footer
    $MiTemplate->set_file("footer","footer.html");

    $MiTemplate->pparse("OUT_H", array("header"), false);
    include "../../menu/menu.php";
    $MiTemplate->parse("OUT_M", array("main","footer"), true);
    $MiTemplate->p("OUT_M");

}

function delete_usuario( $usr_id ) {
    $query_1 = "delete from usuarios where usr_id = $usr_id";
    tep_db_query($query_1);
    header( "Location: adm_musr_01.php" );
    tep_exit();
}

function form_usuarios( $usr_id, $error ) {

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

    $MiTemplate->set_var("TEXT_CAMPO_CLAVES_DISTINTAS",TEXT_CAMPO_CLAVES_DISTINTAS);
    $MiTemplate->set_var("TEXT",TEXT_2);
    $MiTemplate->set_var("TEXT_SUB",TEXT_SUB_AGREGAR);
    $MiTemplate->set_var("BOTON",BOTON_USUARIO_AGREGAR);
    $MiTemplate->set_var("TEXT_CAMPO_1",TEXT_CAMPO_1);
    $MiTemplate->set_var("TEXT_CAMPO_2",TEXT_CAMPO_2);
    $MiTemplate->set_var("TEXT_CAMPO_3",TEXT_CAMPO_3);
    $MiTemplate->set_var("TEXT_CAMPO_4",TEXT_CAMPO_4);
    $MiTemplate->set_var("TEXT_CAMPO_5",TEXT_CAMPO_5);
    $MiTemplate->set_var("TEXT_CAMPO_6",TEXT_CAMPO_6);
    $MiTemplate->set_var("TEXT_CAMPO_7",TEXT_CAMPO_7);
    $MiTemplate->set_var("TEXT_CAMPO_8",TEXT_CAMPO_8);
    $MiTemplate->set_var("TEXT_CAMPO_9",TEXT_CAMPO_9);
    if( $error == 1 )
        $MiTemplate->set_var("TEXT_ERROR",TEXT_CAMPO_EXISTE);

    // Agregamos el header
    $MiTemplate->set_file("header","header_cc.html");

    // Agregamos la barra de herramientas
    $barra_her = kid_href( 'adm_musr_01.php', '', BOTON_VOLVER_IMG, BOTON_VOLVER, '' );
    if( se_puede( 'i', PERMISOS_MOD ) )
        $barra_her .= " " . kid_href( 'adm_musr_01.php', 'action=ins', BOTON_AGREGAR_IMG, TEXT_TAG_AGREGAR, '' );
    $MiTemplate->set_var("BARRA_HERRAMIENTAS",$barra_her);
    $MiTemplate->set_file("barra_her","barra.html");
    $MiTemplate->parse("OUT_BARRA","barra_her");

    // Agregamos el main
    $MiTemplate->set_file("main","adm_musr/form.html");

    //Recuperamos los Locales
    $MiTemplate->set_block("main", "Locales", "BLO_Loc");
    $queryL = "select l.id_local, l.nom_local from locales l"; 
    query_to_set_var( $queryL, $MiTemplate, 1, 'BLO_Loc', 'Locales' );   

    //Recuperamos los origenes
    $MiTemplate->set_block("main", "Origen", "BLO_Ori");
    $queryO = "select o.id_origen, o.nom_origen from OrigenUsr o order by nom_origen"; 
    query_to_set_var( $queryO, $MiTemplate, 1, 'BLO_Ori', 'Origen' );  
    // Agregamos el footer
    $MiTemplate->set_file("footer","footer.html");

    $MiTemplate->pparse("OUT_H", array("header"), false);
    include "../../menu/menu.php";
    $MiTemplate->parse("OUT_M", array("main","footer"), true);
    $MiTemplate->p("OUT_M");

}

function form_mod_usuarios( $usr_id, $error ) {
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

    $MiTemplate->set_var("TEXT",TEXT_3);
    $MiTemplate->set_var("TEXT_SUB",TEXT_SUB_MODIFICAR);
    $MiTemplate->set_var("BOTON",BOTON_USUARIO_MODIFICAR);
    $MiTemplate->set_var("TEXT_CAMPO_1",TEXT_CAMPO_1);
    $MiTemplate->set_var("TEXT_CAMPO_2",TEXT_CAMPO_2);
    $MiTemplate->set_var("TEXT_CAMPO_3",TEXT_CAMPO_3);
    $MiTemplate->set_var("TEXT_CAMPO_4",TEXT_CAMPO_4);
    $MiTemplate->set_var("TEXT_CAMPO_5",TEXT_CAMPO_5);
    $MiTemplate->set_var("TEXT_CAMPO_6",TEXT_CAMPO_6);
    $MiTemplate->set_var("TEXT_CAMPO_7",TEXT_CAMPO_7);
    $MiTemplate->set_var("TEXT_CAMPO_8",TEXT_CAMPO_8);
    $MiTemplate->set_var("TEXT_CAMPO_9",TEXT_CAMPO_9);
    $MiTemplate->set_var("TEXT_ASOCIAR_CAMPO_1",TEXT_ASOCIAR_CAMPO_1);
    $MiTemplate->set_var("TEXT_ASOCIAR_CAMPO_2",TEXT_ASOCIAR_CAMPO_2);
    $MiTemplate->set_var("TEXT_ASOCIAR_CAMPO_3",TEXT_ASOCIAR_CAMPO_3);
    $MiTemplate->set_var("TEXT_ASOCIAR_CAMPO_4",TEXT_ASOCIAR_CAMPO_4);
    $MiTemplate->set_var("TEXT_CAMPO_CLAVES_DISTINTAS",TEXT_CAMPO_CLAVES_DISTINTAS);
    $MiTemplate->set_var("usr_id",$usr_id);
    if( $error == 1 )
        $MiTemplate->set_var("TEXT_ERROR",TEXT_CAMPO_EXISTE);

    // Agregamos el header
    $MiTemplate->set_file("header","header_cc.html");

    // Agregamos la barra de herramientas
    $barra_her = kid_href( 'adm_musr_01.php', '', BOTON_VOLVER_IMG, BOTON_VOLVER, '' );
    if( se_puede( 'i', PERMISOS_MOD ) )
        $barra_her .= " " . kid_href( 'adm_musr_01.php', 'action=ins', BOTON_AGREGAR_IMG, TEXT_TAG_AGREGAR, '' );
    $MiTemplate->set_var("BARRA_HERRAMIENTAS",$barra_her);
    $MiTemplate->set_file("barra_her","barra.html");
    $MiTemplate->parse("OUT_BARRA","barra_her");

    // Recuperamos los datos del usuario
    $query = "select usr_id, usr_nombres, usr_apellidos, usr_email, usr_login, usr_clave, usr_estado ,usr_origen from usuarios where usr_id = $usr_id";
    $rq = tep_db_query($query);
    $res = mysql_fetch_assoc( $rq );
    $arr_k = array_keys ($res);

    $rq = tep_db_query($query);
    while( $res = tep_db_fetch_array( $rq ) ) {
        for( $i = 0; $i < sizeof( $arr_k ); $i++ ) {
            $MiTemplate->set_var($arr_k[$i],tohtml( $res[$arr_k[$i]] ));
        }
        if( $res['usr_estado'] == 0 ) {
            $MiTemplate->set_var('chec1','');
            $MiTemplate->set_var('chec2','checked');
        }
        else {
            $MiTemplate->set_var('chec1','checked');
            $MiTemplate->set_var('chec2','');
        }
		$origen=$res['usr_origen'];
    }

    // Obtenemos los datos de los modulos para las excepciones
    $query_1 = "select pemo_mod_id, pemo_insert, pemo_update, pemo_delete, pemo_select, mod_nombre from permisosxmodulo, modulos where pemo_tipo = 2 and pemo_mod_id = mod_id and pemo_per_id = $usr_id order by mod_orden";
    $rq_1 = tep_db_query($query_1);
    $i = 0;
    $not_in = "";
    $tag = "";
    while( $res_1 = tep_db_fetch_array( $rq_1 ) ) {
        $arr_pemo[$i][0] = $res_1['pemo_mod_id'];
        $arr_pemo[$i][1] = $res_1['mod_nombre'];
        $arr_pemo[$i][2] = $res_1['pemo_insert'];
        $arr_pemo[$i][3] = $res_1['pemo_delete'];
        $arr_pemo[$i][4] = $res_1['pemo_update'];
        $arr_pemo[$i][5] = $res_1['pemo_select'];
        $not_in .= $tag . $res_1['pemo_mod_id'];
        if( $tag == "" )
            $tag = ", ";
        $i++;
    }
    $not_in .= $tag . "-1";
    $query_1 = "select mod_id, mod_nombre from modulos where mod_id not in ( $not_in ) order by mod_orden";
    $rq_1 = tep_db_query($query_1);
    $i = 0;
    while( $res_1 = tep_db_fetch_array( $rq_1 ) ) {
        $arr_pemo_new[$i][0] = $res_1['mod_id'];
        $arr_pemo_new[$i][1] = $res_1['mod_nombre'];
        $i++;
    }

    // Agregamos el main
    $MiTemplate->set_file("main","adm_musr/form_mod.html");

    $MiTemplate->set_var("TEXT_SUB2",TEXT_ADMIN_TITULO_ASOCIADOS);
    $MiTemplate->set_var("TEXT_SUB3",TEXT_ADMIN_TITULO_NOASOCIADOS);
    $MiTemplate->set_var("BOTON_ASOCIAR_ADD",BOTON_ASOCIAR_ADD);
    $MiTemplate->set_var("BOTON_ASOCIAR_MOD",BOTON_ASOCIAR_MOD);
    $MiTemplate->set_var("BOTON_ELIMINAR",BOTON_ELIMINAR);

    $MiTemplate->set_block("main", "Modulos_a", "PBLModulos_a");
    if( sizeof( $arr_pemo ) > 0 ) {
        for( $i = 0; $i < sizeof( $arr_pemo ); $i++ ) {
            $MiTemplate->set_var('pemo_mod_id',$arr_pemo[$i][0]);
            $MiTemplate->set_var('mod_nombre',$arr_pemo[$i][1]);
            if( $arr_pemo[$i][2] == 1 ) $MiTemplate->set_var('ch1','checked'); else $MiTemplate->set_var('ch1','');
            if( $arr_pemo[$i][3] == 1 ) $MiTemplate->set_var('ch2','checked'); else $MiTemplate->set_var('ch2','');
            if( $arr_pemo[$i][4] == 1 ) $MiTemplate->set_var('ch3','checked'); else $MiTemplate->set_var('ch3','');
            $MiTemplate->parse("PBLModulos_a", "Modulos_a", true);
        }
    }

    $MiTemplate->set_block("main", "Modulos_b", "PBLModulos_b");
    if( sizeof( $arr_pemo_new ) > 0 ) {
        for( $i = 0; $i < sizeof( $arr_pemo_new ); $i++ ) {
            $MiTemplate->set_var('pemo_mod_id',$arr_pemo_new[$i][0]);
            $MiTemplate->set_var('mod_nombre',$arr_pemo_new[$i][1]);
            $MiTemplate->parse("PBLModulos_b", "Modulos_b", true);
        }
    }

    //Recuperamos los Locales
    $MiTemplate->set_block("main", "Locales", "BLO_Loc");
    $queryL = "select l.id_local, l.nom_local, if (u.id_local, 'selected', '') 'selected' from locales l left join local_usr u on l.id_local = u.id_local and u.USR_ID = " . ($usr_id + 0) ; 
    query_to_set_var( $queryL, $MiTemplate, 1, 'BLO_Loc', 'Locales' );   

    $MiTemplate->set_block("main", "Origen", "BLO_Ori");
	if ($origen) {
            $queryO = "select o.id_origen, o.nom_origen, if(o.id_origen = $origen+0, 'selected', '') as selected from OrigenUsr o order by nom_origen";
    }else{
           $queryO = "select o.id_origen, o.nom_origen from OrigenUsr o order by nom_origen";
	}
    query_to_set_var( $queryO, $MiTemplate, 1, 'BLO_Ori', 'Origen' );   

    // Agregamos el footer
    $MiTemplate->set_file("footer","footer.html");

    $MiTemplate->pparse("OUT_H", array("header"), false);
    include "../../menu/menu.php";
    $MiTemplate->parse("OUT_M", array("main","footer"), true);
    $MiTemplate->p("OUT_M");

}

function insert_usuario() {
    global $ses_usr_id, $c_1, $c_2, $c_3, $c_4, $c_5, $c_7, $local,$origen;
    $contador = 0;
    $query_1 = "select count(*) as cont from usuarios where usr_login = '$c_4' and usr_estado <> 2";
    $rq_1 = tep_db_query($query_1);
    $res_1 = tep_db_fetch_array( $rq_1 );
    $contador = $res_1['cont'];

    if( $contador == 0 ) {
        $usr_nombre = get_nombre_usr( $ses_usr_id );
        $query_1 = "insert into usuarios( usr_nombres, usr_apellidos, usr_email, usr_login, usr_clave, usr_estado, usr_tipo, usr_usr_crea, usr_fec_crea,usr_origen ) values ( '$c_1', '$c_2', '$c_3', '$c_4', md5('$c_5'), $c_7, 1, '$usr_nombre', now(),$origen )";

        $db_1 = tep_db_query($query_1);
        
		//Inserto el local del usuario
		if ($local)
	        $query_1 = "insert into local_usr( USR_ID, id_local) values ( ".tep_db_insert_id( '' ).", " . ($local+0) . ")";
		else
	        $query_1 = "insert into local_usr( USR_ID, id_local) values ( ".tep_db_insert_id( '' ).", null)";

        $db_1 = tep_db_query($query_1);
        
        header( "Location: adm_musr_01.php" );
        tep_exit();
    }
    else {
        header( "Location: adm_musr_01.php?action=ins&error=1" );
        tep_exit();
    }
}

function update_usuario( $usr_id ) {
    global $ses_usr_id, $c_1, $c_2, $c_3, $c_4, $c_5, $c_7, $local,$origen;

    $usr_nombre = get_nombre_usr( $ses_usr_id );
    if( $c_5 != "" )
        $update_clave = " usr_clave = md5('$c_5'), ";
    $query_1 = "update usuarios set usr_nombres = '$c_1', usr_apellidos = '$c_2', usr_email = '$c_3', usr_login = '$c_4', $update_clave usr_estado = $c_7, usr_usr_mod = '$usr_nombre', usr_fec_mod = now(),usr_origen=$origen where usr_id = $usr_id";
    $db_1 = tep_db_query($query_1);
	//Actualizo el local del usuario
	if ($local)
		$query_1 = "update local_usr set id_local = " . ($local+0) . " where USR_ID = " . ($usr_id+0);
	else
                $query_1 = "delete from local_usr where USR_ID = " . ($usr_id+0);
	$db_1 = tep_db_query($query_1);

	if (!mysql_affected_rows()) {
		//El usuario no existe para el local por tanto lo creamos
		if ($local)
			$query_1 = "insert into local_usr (id_local, USR_ID) VALUES (" . ($local+0) . ", " . ($usr_id+0) . ")" ;
		else
			$query_1 = "insert into local_usr (id_local, USR_ID) VALUES (null, " . ($usr_id+0) . ")" ;
		$db_1 = tep_db_query($query_1);
	}

	header( "Location: adm_musr_01.php" );
    tep_exit();

}

function asocia_modulo( $usr_id, $insert_in, $delete_in, $update_in, $select_in, $pemo_mod_id ) {
    global $ses_usr_id;

    $usr_nombre = get_nombre_usr( $ses_usr_id );

    if( $insert_in == '' )
        $insert_in = 0;
    if( $delete_in == '' )
        $delete_in = 0;
    if( $update_in == '' )
        $update_in = 0;
    if( $select_in == '' )
        $select_in = 0;

    $query_1 = "insert into permisosxmodulo( pemo_per_id, pemo_mod_id, pemo_tipo, pemo_insert, pemo_delete, pemo_update, pemo_select, pemo_usr_crea, pemo_fec_crea ) values ( $usr_id, $pemo_mod_id, 2, $insert_in, $delete_in, $update_in, $select_in, '$usr_nombre', now() )";
    tep_db_query($query_1);

    header( "Location: adm_musr_01.php?action=upd&usr_id=$usr_id" );
    tep_exit();
}

function mod_asocia_modulo( $usr_id, $insert_in, $delete_in, $update_in, $select_in, $pemo_mod_id ) {
    global $ses_usr_id;

    $usr_nombre = get_nombre_usr( $ses_usr_id );

    if( $insert_in == '' )
        $insert_in = 0;
    if( $delete_in == '' )
        $delete_in = 0;
    if( $update_in == '' )
        $update_in = 0;
    if( $select_in == '' )
        $select_in = 0;

    $usr_nombre = get_nombre_usr( $ses_usr_id );

    $query_1 = "update permisosxmodulo set pemo_insert = $insert_in, pemo_update = $update_in, pemo_delete = $delete_in, pemo_select = $select_in, pemo_usr_mod = '$usr_nombre', pemo_fec_mod = now() where pemo_per_id = $usr_id and pemo_mod_id = $pemo_mod_id and pemo_tipo = 2";
    tep_db_query($query_1);

    header( "Location: adm_musr_01.php?action=upd&usr_id=$usr_id" );
    tep_exit();

}

function del_asocia_modulo( $usr_id, $pemo_mod_id ) {
    $query_1 = "delete from permisosxmodulo where pemo_tipo = 2 and pemo_per_id = $usr_id and pemo_mod_id = $pemo_mod_id";
    tep_db_query($query_1);

    header( "Location: adm_musr_01.php?action=upd&usr_id=$usr_id" );
    tep_exit();

}

/**********************************************************************************************/

if( $action == 'del' )
    delete_usuario( $usr_id );
else if( $action == 'ins' )
    form_usuarios( '', $error );
else if( $action == 'upd' )
    form_mod_usuarios( $usr_id, $error );
else if( $action == 'ins1' )
    insert_usuario();
else if( $action == 'upd1' )
    update_usuario( $usr_id );
else if( $action == 'addmod' )
    asocia_modulo( $usr_id, $insert_in, $delete_in, $update_in, $select_in, $pemo_mod_id );
else if( $action == 'updmod' )
    mod_asocia_modulo( $usr_id, $insert_in, $delete_in, $update_in, $select_in, $pemo_mod_id );
else if( $action == 'delmod' )
    del_asocia_modulo( $usr_id, $pemo_mod_id );
else
    listado_usuarios( $patron );

/**********************************************************************************************/

include "../../includes/application_bottom.php";

?>
