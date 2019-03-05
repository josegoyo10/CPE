<?
//$pag_ini = '../nueva_cotizacion/nueva_cotizacion_00.php';
$SIN_PER = 1;
include "../../includes/aplication_top.php";

// include_idioma_mod( $ses_idioma, "nueva_cotizacion_0303" );
// include_idioma_mod( $ses_idioma, "monitor_cotizaciones" );

// *************************************************************************************

$MiTemplate = new Template();
// asignamos degug maximo
$MiTemplate->debug = 0;
// root directory de los templates
$MiTemplate->set_root(DIRTEMPLATES);
// variables perdidas
$MiTemplate->set_unknowns("remove");
/* aciones   */
    $queryEstado="select S.id_os, S.id_direccion, S.id_estado, E.esta_nombre as esta_actual, E.estadoterminal estado_terminal from os S inner join estados E on (E.id_estado=S.id_estado) where id_os=".($id_os+0)." ";
    $Qesta = tep_db_query($queryEstado);
    $Qesta = tep_db_fetch_array( $Qesta );

if($accion=="update" && $select_estado2){
	$queryUP ="UPDATE os SET id_estado='$select_estado2' where id_os=".($id_os+0)." ";
	tep_db_query($queryUP);
	$ultimoID = tep_db_insert_id('');

	/* saca nonbre del estado para poner en el trackin*/
	$queyNE="select esta_nombre from estados where id_estado ='$select_estado2' ";
	$esta_ne = tep_db_query($queyNE);
	$esta_ne = tep_db_fetch_array($esta_ne );
	$nombre_estado=$esta_ne['esta_nombre'];

	insertahistorial("OS $id_os ha cambiado a estado $nombre_estado");
?>
        <script language="JavaScript">
            window.returnValue = 'refresh';
            window.close();
        </script>
<?

   tep_exit();
}


/********************************/
$MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
$MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);
$MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));


     $MiTemplate->set_var("id_estado",$Qesta['id_estado']);
     $MiTemplate->set_var("esta_nombre",$Qesta['esta_nombre']);
     $MiTemplate->set_var("esta_actual",$Qesta['esta_actual']);

// Agregamos el main
$MiTemplate->set_file("main","nueva_cotizacion/estado2.htm");

$MiTemplate->set_var("clie_rut",$clie_rut);
$MiTemplate->set_var("accion",$accion);
$MiTemplate->set_var("id_os",$id_os);
$MiTemplate->set_var("id_estado_origen",$Qesta['id_estado']);
$MiTemplate->set_var("esta_nombre",$Qesta['esta_nombre']);
$MiTemplate->set_var("estado_terminal",$Qesta['estado_terminal']);

/*para los estados*/
$MiTemplate->set_block("main", "Estados", "BLO_esta");
$queryE ="SELECT distinct CE.id_estado_destino  as id_estado ,CE.id_estado_origen ,CE.esta_tipo,E.esta_nombre ,if('$id_estado'=id_estado, 'selected', '') 'selected', E.estadoterminal FROM cambiosestado CE inner join estados E on (E.id_estado= CE .id_estado_destino) where CE.esta_tipo='SS' and CE .id_estado_origen='".$Qesta['id_estado']."'";
query_to_set_var( $queryE, $MiTemplate, 1, 'BLO_esta', 'Estados' );   

$MiTemplate->pparse("OUT_H", array("header"), true);
$MiTemplate->parse("OUT_M", array("main","footer"), true);
$MiTemplate->p("OUT_M");

include "../../includes/application_bottom.php";
?>