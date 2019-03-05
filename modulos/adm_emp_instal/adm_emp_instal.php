<?php
$pag_ini = '../adm_emp_instal/adm_emp_instal.php';
include "../../includes/aplication_top.php";

include_idioma_mod( $ses_idioma, "adm_emp_instal" );

/**********************************************************************************************/


// ****************************************************************
// *
// * Despliega Listado de las Empresas Instaladoras
// *
// ****************************************************************

function DisplayListado() {
	global $ses_usr_id;
    global $req;

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

    $MiTemplate->set_var("BOTON_VER",BOTON_VER);
    $MiTemplate->set_var("TEXT_BOTON",TEXT_BOTON);
    $MiTemplate->set_var("TEXT_SELECT",TEXT_SELECT);
    
    // Agregamos el header
    $MiTemplate->set_file("header","header_ident.html");

    // Agregamos la barra de herramientas
    if( se_puede( 'i', PERMISOS_MOD ) )
        $barra_her = kid_href( 'adm_emp_instal.php', "req=addinst", BOTON_AGREGAR_IMG, MENU_ADD_INST, '' );
    $MiTemplate->set_var("BARRA_HERRAMIENTAS",$barra_her);
    $MiTemplate->set_file("barra_her","barra.html");
    $MiTemplate->parse("OUT_BARRA","barra_her");

	// Agregamos el main
    $MiTemplate->set_file("main","adm_emp_instal/listado.html");
    
	//Recuperamos los Instaladores
    $MiTemplate->set_block("main", "listado", "PBLlistado");
    
    $query=" SELECT id_empresa_instaladora,  descripcion,  nit,  direccion,  telefono 
    		 FROM empresa_instaladora ";
    
	//echo $query;
    if ( $rq = tep_db_query($query) ){
        while( $res = tep_db_fetch_array( $rq ) ) {
            $MiTemplate->set_var('nit',tohtml( $res['nit']));
            $MiTemplate->set_var('nombre',tohtml( $res['descripcion']));
            $MiTemplate->set_var('telefono',tohtml( $res['telefono']));            
			$MiTemplate->set_var('direccion',tohtml( $res['direccion']));            
            
			$CONFIRM_ELIMINAR_INST = '¿Esta seguro que desea eliminar la Empresa Instaladora ' . tohtml($res['descripcion']) . '?';
			$msg_aux = "";
			if( se_puede( 'u', PERMISOS_MOD ) )
				$msg_aux .= ' ' . kid_href( 'adm_emp_instal.php', 'req=updinst&inst_id='.$res['id_empresa_instaladora'], BOTON_MODIFICAR_IMG, TEXT_MODIFICAR_INST, '' );
			if( se_puede( 'd', PERMISOS_MOD ) )
				$msg_aux .= ' ' . kid_href( 'javascript:validar_eliminar( "'.$CONFIRM_ELIMINAR_INST.'", "adm_emp_instal.php?req=delinst&inst_id='.$res['id_empresa_instaladora'].'" ); //', '', BOTON_ELIMINAR_IMG, TEXT_ELIMINAR_INST, '' );

			$MiTemplate->set_var('ACCIONES',$msg_aux);
            $MiTemplate->parse("PBLlistado", "listado", true);   
        }           
    }
    
    // Agregamos el footer
	include "../../includes/footer_cproy.php";

    $MiTemplate->pparse("OUT_H", array("header"), false);
    include "../../menu/menu.php";
    $MiTemplate->parse("OUT_M", array("main","footer"), true);
    $MiTemplate->p("OUT_M");
}

// **************************************************************************
// *
// * Despliega Formulario de Ingreso, Modificación de Empresas Instaladoras
// *
// **************************************************************************

function DisplayFormInstal( $req, $inst_id ){
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

    // Agregamos el header
    $MiTemplate->set_file("header","header_cc.html");

    // Agregamos la barra de herramientas
    $barra_her = kid_href( 'adm_emp_instal.php', "", BOTON_VOLVER_IMG, BOTON_VOLVER, '' );
    $MiTemplate->set_var("BARRA_HERRAMIENTAS",$barra_her);
    $MiTemplate->set_file("barra_her","barra.html");
    $MiTemplate->parse("OUT_BARRA","barra_her");

    // Agregamos el main
    $MiTemplate->set_file("main","adm_emp_instal/form_emp_instal.html");

	if ($inst_id){
		//Buscamos los Datos de la Empresa Instaladora
		$query="SELECT id_empresa_instaladora,  descripcion,  nit,  direccion,  telefono  
				FROM empresa_instaladora i			
				where i.id_empresa_instaladora = " . $inst_id;
		//echo $query;
		$rq = tep_db_query($query); 
		if ( $res = tep_db_fetch_array( $rq ) ){
			$MiTemplate->set_var('descripcion',tohtml( $res['descripcion']));			
			$MiTemplate->set_var('telefono',tohtml( $res['telefono']));
			$MiTemplate->set_var('nit',tohtml( $res['nit']));			
			$MiTemplate->set_var('direccion',tohtml( $res['direccion']));			
		}		

		$MiTemplate->set_var("req",'modinst');
		$MiTemplate->set_var("inst_id",$inst_id);
		
		
	}
	else{
		$MiTemplate->set_var("req",'inginst');
	}

	// Agregamos el footer
	include "../../includes/footer_cproy.php";

    $MiTemplate->pparse("OUT_H", array("header"), false);
    include "../../menu/menu.php";
    $MiTemplate->parse("OUT_M", array("main","footer"), true);
    $MiTemplate->p("OUT_M");

}

// **************************************************************************
// *
// * Proceso de Ingreso de una Empresa Instaladora
// *
// **************************************************************************

function insert_instal() {
    global $ses_usr_id;
	global $descripcion, $telefono, $nit, $direccion;

    $error=0;
    $usr_nombre = get_nombre_usr( $ses_usr_id );

    $query_1 = "INSERT INTO empresa_instaladora (   descripcion,    telefono,    nit,   direccion  ) 
    									 values ( '$descripcion', '$telefono', '$nit', '$direccion' )";
    
	$db_1 = tep_db_query($query_1);
    if ( $last_insert = tep_db_insert_id($db_1) ){
		$error = 1; 
    }
    else{   
        $error = 2;
    }
    
    //echo $query_1, "<BR>";

    header( "Location: adm_emp_instal.php?req=updinst&inst_id=$last_insert");
    tep_exit();

}

// **************************************************************************
// *
// * Proceso de Modificación de una Empresa Instaladora
// *
// **************************************************************************

function Update_instal( $inst_id ){
    global $ses_usr_id;
	global $descripcion, $telefono, $nit, $direccion;

    $error=0;
    $usr_nombre = get_nombre_usr( $ses_usr_id );

    $query_1 = "UPDATE empresa_instaladora  SET 
				descripcion 			=	'$descripcion',				
				telefono 			    =	'$telefono',
				nit 				    =	'$nit',
				direccion 				=	'$direccion'  				
				WHERE id_empresa_instaladora = " . $inst_id;
    if ( $db_1 = tep_db_query($query_1))
		$error = 1; 
    else   
        $error = 2;        
        
    header( "Location: adm_emp_instal.php?req=updinst&inst_id=$inst_id");
    tep_exit();
}

// **************************************************************************
// *
// * Proceso de Eliminación de un Instalador
// *
// **************************************************************************

function Delete_instal( $inst_id ){
    global $ses_usr_id;

    $error=0;
    $query_1 = "DELETE from empresa_instaladora WHERE id_empresa_instaladora = ". $inst_id;
    if ( $db_1 = tep_db_query($query_1))
		$error = 1; 
    else   
        $error = 2;

    header( "Location: adm_emp_instal.php");
    tep_exit();
}

// **************************************************************************
// *
// * Proceso de Asosiación de Comunas
// *
// **************************************************************************

function Insert_asoc( $inst_id, $com_id ){

    $error=0;
    $query_1 = "INSERT INTO instalador_comuna (id_instalador, id_comuna) VALUES ( $inst_id, $com_id )";
    if ( $db_1 = tep_db_query($query_1))
		$error = 1; 
    else   
        $error = 2;

    header( "Location: adm_instal.php?req=updinst&inst_id=$inst_id");
    tep_exit();
}

// **************************************************************************
// *
// * Proceso de eliminación de Asosiación de Comunas
// *
// **************************************************************************

function Delete_asoc( $inst_id, $com_id ){
    $error=0;
    $query_1 = "DELETE FROM instalador_comuna WHERE id_instalador = $inst_id AND id_comuna = $com_id LIMIT 1 ";
    if ( $db_1 = tep_db_query($query_1))
		$error = 1; 
    else   
        $error = 2;

    header( "Location: adm_instal.php?req=updinst&inst_id=$inst_id");
    tep_exit();
}

/**********************************************************************************************/
switch($req){
	case "delasoc":
		Delete_asoc( $inst_id, $com_id );
		break;
	case "addcom":
		Insert_asoc( $inst_id, $com_id );
		break;
	case "delinst":
		Delete_instal( $inst_id );
		break;
	case "modinst":
		Update_instal( $inst_id );
		break;
	case "inginst":
		insert_instal();
		break;	
	case "updinst":
		DisplayFormInstal( $req, $inst_id );
		break;	
	case "addinst":
		DisplayFormInstal( $req, $inst_id );
		break;	
	default:
		DisplayListado();
		break;
}

/**********************************************************************************************/

include "../../includes/application_bottom.php";

?>