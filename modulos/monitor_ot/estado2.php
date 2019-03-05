<?
$pag_ini = '../monitor_ot/index.php';
include "../../includes/aplication_top.php";

include_idioma_mod( $ses_idioma, "monitor_ot" );

/**********************************************************************************************/

/*include_idioma_mod( $ses_idioma, "start" );*/

$MiTemplate = new Template();
// asignamos degug maximo
$MiTemplate->debug = 0;
// root directory de los templates
$MiTemplate->set_root(DIRTEMPLATES);
// variables perdidas
$MiTemplate->set_unknowns("remove");

/* aciones   */
$queryEstado="select ot.*, E.esta_nombre esta_actual, E.esta_tipo, E.estadoterminal estado_terminal from ot LEFT JOIN estados E on E.id_estado=ot.id_estado where ot_id=".($id_ot+0) ;

$Qesta = tep_db_query($queryEstado);
$Qesta = tep_db_fetch_array( $Qesta );
$IDOS=$Qesta['id_os'];


if($accion=="update" && $select_estado2) {
    $queryUP ="UPDATE ot SET id_estado='$select_estado2' where ot_id=".($id_ot+0)." ";
	tep_db_query($queryUP);

		/* para el nombre del que atendio*/
        $qnombre="select U.usr_nombres,U.usr_apellidos from usuarios U inner join os OS on (OS.usr_id=U.usr_id) where id_os=".($IDOS+0)."";
        $rq = tep_db_query($qnombre);
        $res = tep_db_fetch_array( $rq );
        $nc=$res['usr_nombres']." ".$res['usr_apellidos'];

        /* saca nonbre del estado para poner en el trackin*/
        $querN="select esta_nombre from estados where id_estado ='$select_estado2' ";
        $estaN = tep_db_query($querN);
        $estaN = tep_db_fetch_array( $estaN );
        $esta_nombre=$estaN['esta_nombre'];

        /*query que inserta evento en historial*/
        $queryHist="Insert into historial (id_os,ot_id,hist_fecha,hist_usuario,hist_descripcion) values (".($IDOS+0).",".($id_ot+0).",now(),'$nc','La Orden de trabajo Nº ".($id_ot+0)." cambio a estado ".$esta_nombre."')";
        $hist = tep_db_query($queryHist);

		/*Revisamos estados para cambiar estado a la OS si se cumplen las condiciones*/
		$ret_marca_OS = marca_OS_Finalizada (($id_ot+0));

    ?>
	<script language="JavaScript">
		<?if ($ret_marca_OS){?>alert("La OS generadora ha sido cambiada a estado \'OS Completada\'");<?}?>
		window.returnValue = 'refresh';
		window.close();
	</script>
	<?
    tep_exit();
}

/********************************/
// Agregamos el header
$MiTemplate->set_file("header","header_sc.html");

$MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
$MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);
$MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));


$MiTemplate->set_var("id_estado_a",$Qesta['id_estado']);
$MiTemplate->set_var("esta_actual",$Qesta['esta_actual']);

// Agregamos el main
$MiTemplate->set_file("main","monitor_ot/estado2.htm");

$MiTemplate->set_var("accion",$accion);
$MiTemplate->set_var("id_ot",$id_ot);
$MiTemplate->set_var("id_estado_origen",$Qesta['id_estado']);
$MiTemplate->set_var("esta_nombre",$Qesta['esta_nombre']);
$MiTemplate->set_var("estado_terminal",$Qesta['estado_terminal']);

/*para los estados*/
$MiTemplate->set_block("main", "Estados", "BLO_esta");
$queryE ="SELECT distinct CE.id_estado_destino  as id_estado ,CE.id_estado_origen ,CE.esta_tipo, E.esta_nombre ,if('$id_estado'=id_estado, 'selected', '') 'selected', E.estadoterminal FROM cambiosestado CE inner join estados E on (E.id_estado= CE .id_estado_destino) where CE.esta_tipo='".$Qesta['esta_tipo']."' and CE .id_estado_origen='".$Qesta['id_estado']."'";
query_to_set_var( $queryE, $MiTemplate, 1, 'BLO_esta', 'Estados' );

$MiTemplate->pparse("OUT_H", array("header"), true);
$MiTemplate->parse("OUT_M", array("main","footer"), true);
$MiTemplate->p("OUT_M");

include "../../includes/application_bottom.php";
?>