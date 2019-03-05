<?php
$pag_ini = '../adm_instal/adm_instal.php';
include "../../includes/aplication_top.php";

include_idioma_mod( $ses_idioma, "adm_instal" );

/**********************************************************************************************/


// ****************************************************************
// *
// * Despliega Listado de Instaladores
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
        $barra_her = kid_href( 'adm_instal.php', "req=addinst", BOTON_AGREGAR_IMG, MENU_ADD_INST, '' );
    $MiTemplate->set_var("BARRA_HERRAMIENTAS",$barra_her);
    $MiTemplate->set_file("barra_her","barra.html");
    $MiTemplate->parse("OUT_BARRA","barra_her");

	// Agregamos el main
    $MiTemplate->set_file("main","adm_instal/listado.html");
    
	//Recuperamos los Instaladores
    $MiTemplate->set_block("main", "listado", "PBLlistado");
    
    $query="SELECT I.id_instalador, I.inst_nombre, I.inst_paterno, I.inst_materno, I.inst_telefono, I.inst_rut, I.direccion, I.telefono2, I.email, E.descripcion
			FROM instaladores I
			INNER JOIN empresa_instaladora E ON I.id_empresa_instaladora = E.id_empresa_instaladora ";
    
	//echo $query;
    if ( $rq = tep_db_query($query) ){
        while( $res = tep_db_fetch_array( $rq ) ) {
            $MiTemplate->set_var('id_instalador',tohtml( $res['id_instalador']));
            $MiTemplate->set_var('inst_nombre',tohtml( $res['inst_nombre']));
            $MiTemplate->set_var('inst_paterno',tohtml( $res['inst_paterno']));
            $MiTemplate->set_var('inst_materno',tohtml( $res['inst_materno']));
            $MiTemplate->set_var('inst_telefono',tohtml( $res['inst_telefono']));
            $MiTemplate->set_var('inst_rut',tohtml( $res['inst_rut']));
			$MiTemplate->set_var('direccion',tohtml( $res['direccion']));
            $MiTemplate->set_var('telefono2',tohtml( $res['telefono2']));
            $MiTemplate->set_var('email',tohtml( $res['email']));
            $MiTemplate->set_var('nombre_empresa',tohtml( $res['descripcion']));
			$CONFIRM_ELIMINAR_INST = '¿Esta seguro que desea eliminar el Instalador '.tohtml($res['inst_nombre']).' '.tohtml( $res['inst_paterno']).'?';
			$msg_aux = "";
			if( se_puede( 'u', PERMISOS_MOD ) )
				$msg_aux .= ' ' . kid_href( 'adm_instal.php', 'req=updinst&inst_id='.$res['id_instalador'], BOTON_MODIFICAR_IMG, TEXT_MODIFICAR_INST, '' );
			if( se_puede( 'd', PERMISOS_MOD ) )
				$msg_aux .= ' ' . kid_href( 'javascript:validar_eliminar( "'.$CONFIRM_ELIMINAR_INST.'", "adm_instal.php?req=delinst&inst_id='.$res['id_instalador'].'" ); //', '', BOTON_ELIMINAR_IMG, TEXT_ELIMINAR_INST, '' );

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
// * Despliega Formulario de Ingreso, Modificación y Asosiación de Comunas 
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
    $barra_her = kid_href( 'adm_instal.php', "", BOTON_VOLVER_IMG, BOTON_VOLVER, '' );
    $MiTemplate->set_var("BARRA_HERRAMIENTAS",$barra_her);
    $MiTemplate->set_file("barra_her","barra.html");
    $MiTemplate->parse("OUT_BARRA","barra_her");

    // Agregamos el main
    $MiTemplate->set_file("main","adm_instal/form_instal.html");

	if ($inst_id){
		//Buscamos los Datos del Instalador
		$query="SELECT inst_nombre, 
				inst_paterno, 
				inst_materno, 
				inst_telefono, 
				inst_rut, 
				direccion, 
				telefono2, 
				email
				FROM instaladores i
				where i.id_instalador = ".$inst_id;
		//echo $query;
		$rq = tep_db_query($query); 
		if ( $res = tep_db_fetch_array( $rq ) ){
			$MiTemplate->set_var('inst_nombre',tohtml( $res['inst_nombre']));
			$MiTemplate->set_var('inst_paterno',tohtml( $res['inst_paterno']));
			$MiTemplate->set_var('inst_materno',tohtml( $res['inst_materno']));
			$MiTemplate->set_var('inst_fono1',tohtml( $res['inst_telefono']));
			$MiTemplate->set_var('inst_rut',tohtml( $res['inst_rut']));
			//$MiTemplate->set_var('inst_dv','-'.dv(tohtml( $res['inst_rut'])));
			$MiTemplate->set_var('inst_dire',tohtml( $res['direccion']));
			$MiTemplate->set_var('inst_fono2',tohtml( $res['telefono2']));
			$MiTemplate->set_var('inst_mail',tohtml( $res['email']));
			$inst_id_comuna = $res['id_comuna'];
		}		

		$MiTemplate->set_var("req",'modinst');
		$MiTemplate->set_var("inst_id",$inst_id);

		// Agregamos asociar
		
		
		//$MiTemplate->set_file("f_asociar","adm_instal/form_asoc.html");

		//Recuperamos los Comunas
		
		$MiTemplate->set_block("main", "Inst_Empresas", "BLO_Inst_Empresas");
		$queryL="SELECT E.id_empresa_instaladora, E.descripcion,  case WHEN I.id_instalador is null then '' else 'selected' end selected 
				FROM empresa_instaladora  E
				LEFT JOIN  instaladores I ON  I.id_empresa_instaladora = E.id_empresa_instaladora  AND I.id_instalador = $inst_id
 				order by descripcion ";
		query_to_set_var( $queryL, $MiTemplate, 1, 'BLO_Inst_Empresas', 'Inst_Empresas' );   
	
		/*	
		//Recuperamos El listado de Asosiaciones
		$MiTemplate->set_block("f_asociar", "listado", "BLO_listado");
		$query="SELECT c.id_comuna, c.comu_nombre as desc_comuna
				FROM  comuna c
				JOIN instalador_comuna ic ON ic.id_comuna = c.id_comuna AND ic.id_instalador = ".$inst_id;
		if ( $rq = tep_db_query($query) ){
			while( $res = tep_db_fetch_array( $rq ) ) {
				$MiTemplate->set_var('desc_comuna',tohtml( $res['desc_comuna']));
				$CONFIRM_ELIMINAR_ASOC = '¿Esta seguro que desea eliminar la Coumuna '.tohtml( $res['desc_comuna']).'?';
				$msg_aux = "";
				if( se_puede( 'd', PERMISOS_MOD ) )
					$msg_aux .= ' ' . kid_href( 'javascript:validar_eliminar( "'.$CONFIRM_ELIMINAR_ASOC.'", "adm_instal.php?req=delasoc&inst_id='.$inst_id.'&com_id='.$res['id_comuna'].'" ); //', '', BOTON_ELIMINAR_IMG, TEXT_ELIMINAR_ASOC, '' );

				$MiTemplate->set_var('ACCION',$msg_aux);
				$MiTemplate->parse("BLO_listado", "listado", true);   
			}           
		}

		$MiTemplate->parse("OUT_ASOC", array("f_asociar"), true);
		*/
		
	}
	else{
		$MiTemplate->set_var("req",'inginst');
	}

	//Recuperamos los Comunas donde vive el Instalador
	$MiTemplate->set_block("main", "Inst_Empresas", "BLO_Inst_Empresas");
	$queryL="SELECT * FROM empresa_instaladora  order by descripcion";
	query_to_set_var( $queryL, $MiTemplate, 1, 'BLO_Inst_Empresas', 'Inst_Empresas' );   
	

	// Agregamos el footer
	include "../../includes/footer_cproy.php";

    $MiTemplate->pparse("OUT_H", array("header"), false);
    include "../../menu/menu.php";
    $MiTemplate->parse("OUT_M", array("main","footer"), true);
    $MiTemplate->p("OUT_M");

}

// **************************************************************************
// *
// * Proceso de Ingreso de un Instalador
// *
// **************************************************************************

function insert_instal() {
    global $ses_usr_id;
	global $inst_nombre, $inst_paterno, $inst_materno, $inst_fono1, $inst_rut, $inst_dire, $inst_fono2, $inst_mail, $inst_empresa;

    $error=0;
    $usr_nombre = get_nombre_usr( $ses_usr_id );

    $query_1 = "INSERT INTO instaladores ( inst_nombre, inst_paterno, inst_materno, inst_telefono, inst_rut, direccion, telefono2, email, id_empresa_instaladora ) 
    values ( '$inst_nombre', '$inst_paterno', '$inst_materno', '$inst_fono1', '$inst_rut', '$inst_dire', '$inst_fono2', '$inst_mail', $inst_empresa )";
    
	$db_1 = tep_db_query($query_1);
    if ( $last_insert = tep_db_insert_id($db_1) ){
		$error = 1; 
    }
    else{   
        $error = 2;
    }

    header( "Location: adm_instal.php?req=updinst&inst_id=$last_insert");
    tep_exit();

}

// **************************************************************************
// *
// * Proceso de Modificación de un Instalador
// *
// **************************************************************************

function Update_instal( $inst_id ){
    global $ses_usr_id;
	global $inst_nombre, $inst_paterno, $inst_materno, $inst_fono1, $inst_rut, $inst_dire, $inst_fono2, $inst_mail, $inst_empresa;

    $error=0;
    $usr_nombre = get_nombre_usr( $ses_usr_id );

    $query_1 = "UPDATE instaladores SET 
				inst_nombre 			=	'$inst_nombre',
				inst_paterno 			=	'$inst_paterno',
				inst_materno 			=	'$inst_materno',
				inst_telefono 			=	'$inst_fono1',
				inst_rut 				=	'$inst_rut',
				direccion 				=	'$inst_dire',
				telefono2 				=	'$inst_fono2',
				email 					=	'$inst_mail',
				id_empresa_instaladora	=	$inst_empresa 	
				WHERE id_instalador = ".$inst_id;
    if ( $db_1 = tep_db_query($query_1))
		$error = 1; 
    else   
        $error = 2;
  
    header( "Location: adm_instal.php?req=updinst&inst_id=$inst_id");
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
    $query_1 = "DELETE from instaladores WHERE id_instalador = ".$inst_id;
    if ( $db_1 = tep_db_query($query_1))
		$error = 1; 
    else   
        $error = 2;

    header( "Location: adm_instal.php");
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