<?
$pag_ini = '../nueva_cotizacion/nueva_cotizacion_00.php';
include "../../includes/aplication_top.php";

include_idioma_mod( $ses_idioma, "nueva_lista_02" );
// *************************************************************************************

/********************* Acciones********************/////

/* toma el rut y verifica si y EXISTE EL CLIENTE  en la tabla cliente y direccion*/

/************** Inicio Acciones*****************/
if ($clie_rut){
$valor = $clie_rut;

    $queryClie =  "SELECT clie_rut, clie_nombre, clie_paterno, clie_materno,clie_telefonocasa,clie_telcontacto1,clie_telcontacto2,clie_rutcontacto,clie_razonsocial,clie_giro,clie_tipo  FROM clientes where clie_rut=".($clie_rut+0)." and clie_activo=1";
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

if ($accion=="editar" && $clie_rut && $clie_tipo=="p"){
    header ('Location: nueva_lista_01.php?clie_rut='.($clie_rut+0).'&clie_tipo=p');
    tep_exit();
}
if ($accion=="Del" && $idLista && $clie_rut){
    $queryClie = "DELETE FROM list_regalos_enc WHERE idLista = $idLista AND id_Estado = 'NP' AND clie_rut=".($clie_rut+0);  
    tep_db_query($queryClie);
	insertahistorial("Se ha eliminado del sistema la Lista de Regalo N° $idLista");
    header ('Location: nueva_lista_02.php?clie_rut='.($clie_rut+0).'&clie_tipo='.$clie_tipo);
    tep_exit();
}

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
$MiTemplate->set_file("main","lista_Regalos/nueva_lista_02.htm");


/*Para los Eventos*/
$MiTemplate->set_block("main", "Eventos", "BLO_event");
$todosEv='<option value="0"{selected}>Todos</option>';
$queryEv = "SELECT idEvento, nombre, if('$select_evento'=idEvento, 'selected', '')'selected', if('idEvento'=0, '$todosEv', '') 'todosEv' FROM list_eventos ORDER BY nombre";
$MiTemplate->set_var("todosEv",$todosEv );
query_to_set_var( $queryEv, $MiTemplate, 1, 'BLO_event', 'Eventos' );
 
/*Para los estados*/
$MiTemplate->set_block("main", "Estados", "BLO_esta");
$todosE='<option value="0"{selected}>Todos</option>';
$queryE = "select id_estado, esta_nombre,if('$select_estado'=id_estado, 'selected', '')'selected',if('id_estado'=0, '$todosE', '') 'todosE' from estados where esta_tipo='LS' order by orden";
$MiTemplate->set_var("todosE",$todosE );
query_to_set_var( $queryE, $MiTemplate, 1, 'BLO_esta', 'Estados' );   

/*para las os sin filtrar*/
$MiTemplate->set_block("main", "LISTA", "BLO_list");
    $editar =' | <a href="nueva_lista_03.php?accion=Editar&idLista={idLista}">Editar</a>';
    $eliminar =" | <a href=\"javascript:validadel(\'{idLista}\',\'nueva_lista_02.php?accion=Del&idLista={idLista}&clie_rut=$clie_rut\')\">Borrar</a>";   
    $espacio='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
    if ($accion=="filtra_esta"){
        $filtraE=" AND L.id_Estado='$select_estado'"; 
    }
    if ($accion=="filtra_event"){
        $filtraEv=" AND L.id_Evento='$select_evento'";
    }
    if ($select_estado=='0'){
            $filtraE=" ";
    }
    if ($select_evento=='0'){
            $filtraEv=" ";
    }
    $queryLi="SELECT L.id_Usuario, L.id_Local, L.id_Evento, date_format(L.fec_creacion, '%d/%m/%Y') fec_creacion, date_format(L.fec_entrega, '%d/%m/%Y') fec_entrega, L.id_Estado as Editar, L.idLista, L.descripcion, E.esta_nombre as esta_nombre, V.nombre as evento, if( ('NP'=L.id_Estado) and (L.id_Local=($id_local+0)), '$eliminar', '$espacio') 'eliminar'  FROM list_regalos_enc L INNER JOIN estados E ON (L.id_Estado=E.id_estado) INNER JOIN list_eventos V ON (L.id_Evento=V.idEvento) WHERE L.clie_Rut=".($clie_rut+0)." AND idLista > 0 $filtraEv $filtraE ORDER BY idLista;";
    query_to_set_var( $queryLi, $MiTemplate, 1, 'BLO_list', 'LISTA' );   
// Agregamos el footer
include "../../includes/footer_cproy.php";

$MiTemplate->pparse("OUT_H", array("header"), true);
include "../../menu/menu.php";
$MiTemplate->parse("OUT_M", array("main","footer"), true);
$MiTemplate->p("OUT_M");

include "../../includes/application_bottom.php";
?>
