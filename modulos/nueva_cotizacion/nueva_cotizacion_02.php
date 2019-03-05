<?
$pag_ini = '../nueva_cotizacion/nueva_cotizacion_00.php';
include "../../includes/aplication_top.php";

include_idioma_mod( $ses_idioma, "nueva_cotizacion_02" );
// *************************************************************************************

/********************* Acciones********************/////

/* toma el rut y verifica si y EXISTE EL CLIENTE  en la tabla cliente y direccion*/

/************** Inicio Acciones*****************/
if ($clie_rut){
$valor = $clie_rut;

    $queryClie =  "SELECT clie_rut, clie_nombre, clie_paterno, clie_materno,clie_telefonocasa,clie_telcontacto1,clie_telcontacto2,clie_rutcontacto,clie_razonsocial,clie_giro,clie_tipo  FROM clientes where clie_rut=".($clie_rut+0)." and clie_activo=1";

   file_put_contents('queryClie2.txt', $queryClie);



    $eclient = tep_db_query($queryClie);
    $eclient = tep_db_fetch_array( $eclient );
        $rutE=$eclient['clie_rut'];
        if ($rutE){$rutExiste=true;}
    
        $queryDir = "Select dire_direccion, D.id_comuna, NE.DESCRIPTION AS comu_nombre, dire_observacion,dire_telefono 
					from direcciones D 
					LEFT JOIN cu_neighborhood NE ON (D.dire_localizacion = NE.LOCATION) 
					where clie_rut = $clie_rut and dire_activo=1 and dire_defecto='p' ";    
    
    $edirec = tep_db_query($queryDir);
    $edirec = tep_db_fetch_array( $edirec );
}

if ($accion=="editar" && $clie_rut && $clie_tipo=="e"){
    header ('Location: nueva_cotizacion_empresa_01.php?clie_rut='.($clie_rut+0).'&clie_tipo=e');
    tep_exit();
}
if ($accion=="editar" && $clie_rut && $clie_tipo=="p"){
    header ('Location: nueva_cotizacion_01.php?clie_rut='.($clie_rut+0).'&clie_tipo=p');
    tep_exit();
}
if ($accion=="Del" && $clie_rut && $id_os){
    $queryClie = "DELETE FROM os WHERE id_os = $id_os AND id_estado = 'SA' AND clie_rut=".($clie_rut+0);
    tep_db_query($queryClie);
	insertahistorial("Se ha eliminado del sistema la OS $id_os");
    header ('Location: nueva_cotizacion_02.php?clie_rut='.($clie_rut+0).'&clie_tipo='.$clie_tipo);
    tep_exit();
}

if($accion=="enblanco"){
/* INSERTA UNA COTIZACION APENAS SE INGRESA A ELLA*/
    $queryDire =  "Select id_direccion from direcciones  where clie_rut=".($clie_rut+0)." and dire_defecto='p'";
    $eDire= tep_db_query($queryDire);
    $eDire = tep_db_fetch_array( $eDire );

    $queryPro =  "Select id_proyecto,proy_nombre from proyectos where clie_rut=".($clie_rut+0)." order by id_proyecto desc";
    $eproy = tep_db_query($queryPro);
    $eproy = tep_db_fetch_array( $eproy );
    header ('Location: nueva_cotizacion_03.php?clie_rut='.($clie_rut+0).'&id_direccion='.($eDire['id_direccion']+0));
}
/******para locales*/


/* saca datos de la tabla os si es que existen para ese proyecto y para ese cliente*/

/********************************/

$MiTemplate = new Template();
// asignamos degug maximo
$MiTemplate->debug = 0;
// root directory de los templates
$MiTemplate->set_root(DIRTEMPLATES);
// variables perdidas
$MiTemplate->set_unknowns("remove");

$MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
$MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);


$MiTemplate->set_var("USR_LOCAL",get_local_usr( $ses_usr_id ));
$id_local=get_local_usr( $ses_usr_id );

if ($eclient['clie_rut'] ){
     $MiTemplate->set_var("id_comuna",$edirec['id_comuna']);
     $MiTemplate->set_var("digito",$digito);
     $MiTemplate->set_var("clie_rut",$eclient['clie_rut']);
     $MiTemplate->set_var("clie_razonsocial",$eclient['clie_razonsocial']);
     $MiTemplate->set_var("clie_giro",$eclient['clie_giro']);
     $MiTemplate->set_var("clie_rutcontacto",$eclient['clie_rutcontacto']);
     $MiTemplate->set_var("dire_telefono",$edirec['dire_telefono']);
     $MiTemplate->set_var("clie_nombre",$eclient['clie_nombre']);
     $MiTemplate->set_var("clie_paterno",$eclient['clie_paterno']);
     $MiTemplate->set_var("clie_materno",$eclient['clie_materno']);
     $MiTemplate->set_var("clie_telefonocasa",$eclient['clie_telefonocasa']);
     $MiTemplate->set_var("clie_telcontacto1",$eclient['clie_telcontacto1']);
     $MiTemplate->set_var("clie_telcontacto2",$eclient['clie_telcontacto2']);
     $MiTemplate->set_var("clie_materno",$eclient['clie_materno']);
     $MiTemplate->set_var("dire_direccion",$edirec['dire_direccion']);
     $MiTemplate->set_var("dire_observacion",$edirec['dire_observacion']);
     $MiTemplate->set_var("dire_telefono",$edirec['dire_telefono']);
     $MiTemplate->set_var("comu_nombre",$edirec['comu_nombre']);
     $MiTemplate->set_var("clie_tipo",$eclient['clie_tipo']);
     $MiTemplate->set_var("ultimoID",$ultimoID);
 }
$MiTemplate->set_var("editar",$editar );
$MiTemplate->set_var("eliminar",$eliminar );
$MiTemplate->set_var("NOMBRE_PAGINA",NOMBRE_PAGINA);
$MiTemplate->set_var("TEXT_CAMPO_1",TEXT_CAMPO_1);
$MiTemplate->set_var("TEXT_CAMPO_2",TEXT_CAMPO_2);
$MiTemplate->set_var("TEXT_CAMPO_5",TEXT_CAMPO_5);
$MiTemplate->set_var("TEXT_CAMPO_6",TEXT_CAMPO_6);
$MiTemplate->set_var("TEXT_CAMPO_7",TEXT_CAMPO_7);
$MiTemplate->set_var("TEXT_CAMPO_8",TEXT_CAMPO_8);
$MiTemplate->set_var("TEXT_CAMPO_9",TEXT_CAMPO_9);
$MiTemplate->set_var("TEXT_CAMPO_10",TEXT_CAMPO_10);

$MiTemplate->set_var("TEXT_CAMPO_11",TEXT_CAMPO_11);
$MiTemplate->set_var("TEXT_CAMPO_12",TEXT_CAMPO_12);
$MiTemplate->set_var("TEXT_CAMPO_13",TEXT_CAMPO_13);
$MiTemplate->set_var("TEXT_CAMPO_14",TEXT_CAMPO_14);
$MiTemplate->set_var("TEXT_CAMPO_15",TEXT_CAMPO_15);
$MiTemplate->set_var("TEXT_CAMPO_16",TEXT_CAMPO_16);

// Agregamos el header
$MiTemplate->set_file("header","header_ident.html");

// Agregamos el main
$MiTemplate->set_file("main","nueva_cotizacion/nueva_cotizacion_02.htm");


/*para los proyectos*/
$MiTemplate->set_block("main", "Proyectos", "BLO_proy");
    $raya='<option value="0"{selected}>---------</option>';
    $todos='<option value="00"{selected}>Todos</option>';
    $queryP = "select id_proyecto, proy_nombre, if('$select_proyecto'=id_proyecto, 'selected', '') 'selected' ,if(id_proyecto='', '$raya', '') 'raya',if('id_proyecto'=00, '$todos', '') 'todos' from proyectos where clie_rut=".($clie_rut+0)." order by proy_nombre";
    $MiTemplate->set_var("raya",$raya );
    $MiTemplate->set_var("todos",$todos );
query_to_set_var( $queryP, $MiTemplate, 1, 'BLO_proy', 'Proyectos' );
 
/*para los estados*/
$MiTemplate->set_block("main", "Estados", "BLO_esta");
$todosE='<option value="0"{selected}>Todos</option>';
$queryE = "select id_estado, esta_nombre,if('$select_estado'=id_estado, 'selected', '')'selected',if('id_estado'=0, '$todosE', '') 'todosE' from estados where esta_tipo='SS' order by orden";
$MiTemplate->set_var("todosE",$todosE );
query_to_set_var( $queryE, $MiTemplate, 1, 'BLO_esta', 'Estados' );   

/*para las os sin filtrar*/
$MiTemplate->set_block("main", "OS", "BLO_os");
    $editar =' | <a href="nueva_cotizacion_03.php?accion=Editar&id_os={id_os}">Editar</a>';
    $eliminar =" | <a href=\"javascript:validadel(\'{id_os}\',\'nueva_cotizacion_02.php?accion=Del&id_os={id_os}&clie_rut=$clie_rut\')\">Borrar</a>";
    $espacio='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
    if ($accion=="filtra_esta"){
        $filtraE=" and S.id_estado='$select_estado'";
        $estaSel=$select_estado;    
    }
    if ($accion=="filtra_proy"){
        $filtraP=" and S.id_proyecto='$select_proyecto'";
        $proySel=$select_proyecto;
    }
    if ($select_estado=='0'){
            $filtraE=" ";
    }
    if ($select_proyecto=='00'){
            $filtraP=" ";
    }
    $queryOs="Select S.usr_id,S.usuario,S.id_local, date_format(S.os_fechacreacion, '%d/%m/%Y') os_fechacreacion , S.id_estado as Editar,S.id_os,S.id_proyecto,S.os_descripcion,S.os_comentarios ,E.esta_nombre as esta_nombre_os,P.proy_nombre as proy_nombre_os, if((('SA'=S.id_estado) or('SI'=S.id_estado) ) and (S.id_local=($id_local+0)) , '$editar', '$espacio') 'editar', if('SA'=S.id_estado and S.id_local=($id_local+0), '$eliminar', '$espacio') 'eliminar'  from os S inner join estados E on (S.id_estado=E.id_estado) inner join proyectos P on (P.id_proyecto=S.id_proyecto) where S.clie_rut=".($clie_rut+0)." and id_os > 0 $filtraP $filtraE order by id_os ";
    //$MiTemplate->set_var("editar",$editar );
    //$MiTemplate->set_var("eliminar",$eliminar );
    query_to_set_var( $queryOs, $MiTemplate, 1, 'BLO_os', 'OS' );   

// Agregamos el footer
include "../../includes/footer_cproy.php";

$MiTemplate->pparse("OUT_H", array("header"), true);
include "../../menu/menu.php";
$MiTemplate->parse("OUT_M", array("main","footer"), true);
$MiTemplate->p("OUT_M");

include "../../includes/application_bottom.php";
?>
