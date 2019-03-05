<?
$pag_ini = '../adm_usr/adm_usr_01.php';
include "../../includes/aplication_top.php";

include_idioma_mod( $ses_idioma, "adm_usr" );


/**********************************************************************************************/

function form_eliminar( $usr_id, $perfil ) {

    $MiTemplate = new Template();
    // asignamos degug maximo
    $MiTemplate->debug = 0;
    // root directory de los templates
    $MiTemplate->set_root(DIRTEMPLATES);
    // variables perdidas
    $MiTemplate->set_unknowns("remove");

    $MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
    $MiTemplate->set_var("PER_O",$perfil);
    $MiTemplate->set_var("USR_ID",$usr_id);
    $MiTemplate->set_var("TEXT_ELIMINAR_USR",TEXT_ELIMINAR_USR);
    $MiTemplate->set_var("TEXT_ELIMINAR_USR_1",TEXT_ELIMINAR_USR_1);
    $MiTemplate->set_var("TEXT_ELIMINAR_USR_2",TEXT_ELIMINAR_USR_2);
    $MiTemplate->set_var("TEXT_ELIMINAR_USR_3",TEXT_ELIMINAR_USR_3);

    // Agregamos el main
    $MiTemplate->set_file("main","adm_usr/adm_usr_08_1.html");

    $MiTemplate->parse("OUT_M", array("main"), true);
    $MiTemplate->p("OUT_M");

}

function delete_usuario( $usr_id, $per_o ) {
    $query_1 = "delete from perfilesxusuario where peus_usr_id = $usr_id and peus_per_id = $per_o";
    tep_db_query($query_1);
    
    $MiTemplate = new Template();
    // asignamos degug maximo
    $MiTemplate->debug = 0;
    // root directory de los templates
    $MiTemplate->set_root(DIRTEMPLATES);
    // variables perdidas
    $MiTemplate->set_unknowns("remove");

    $MiTemplate->set_var("ID_PADRE",$per_o);

    // Agregamos el main
    $MiTemplate->set_file("main","adm_usr/adm_usr_03_2.html");

    $MiTemplate->parse("OUT_M", array("main"), true);
    $MiTemplate->p("OUT_M");

}

/**********************************************************************************************/

if( $action == 'delusr' )
    delete_usuario( $usr_id, $per_o );
else
    form_eliminar( $usr_id, $per_o );

/**********************************************************************************************/

include "../../includes/application_bottom.php";

?>