<?
$pag_ini = '../monitor_ot_pe/index.php';
include "../../includes/aplication_top.php";

//////////////// ACCIONES /////////////////////
if ($accion=="grabar") {
	if ($ot_freactivacion) {
		//Se marca la OS como pagada
		$queryUP ="UPDATE ot SET ot_comentario='$ot_comentario', ot_freactivacion='" . fecha_php2db_new($ot_freactivacion) . "' where ot_id=".($id_ot+0) ;
		tep_db_query($queryUP);	
		//Se registra en el tracking
		if ($ot_comentario){
			insertahistorial("OT $id_ot cambia fecha de reactivación a $ot_freactivacion y se agregó el siguiente comentario:  $ot_comentario");
		}else{
			insertahistorial("OT $id_ot cambia fecha de reactivación a $ot_freactivacion");
		}
}
	?>
	<script language="JavaScript">
		window.returnValue = 'refresh';
		window.close();
	</script>
	<?
}
///////////////////////////////////////////////

$MiTemplate = new Template();
$MiTemplate->debug = 0;
$MiTemplate->set_root(DIRTEMPLATES);
$MiTemplate->set_unknowns("remove");


$MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . "Fecha Reactivación");
$MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);
$MiTemplate->set_var("SUBTITULO1",TEXT_2);
$MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));
$MiTemplate->set_var("id_ot",$id_ot);

// Agregamos el header
$MiTemplate->set_file("header","header_sc.html");

// Agregamos el main
$MiTemplate->set_file("main","monitor_ot_pe/reactivacion.htm");

$MiTemplate->set_block("main", "Reactivacion", "BLO_reactivacion");
$query="SELECT	id_os,
				date_format(ot_freactivacion, '%d/%m/%Y') as ot_freactivacion,
				ot_comentario
		FROM ot 
		WHERE ot_id = " . ($id_ot + 0) ; 
query_to_set_var( $query, $MiTemplate, 1, 'BLO_reactivacion', 'Reactivacion' );


$MiTemplate->pparse("OUT_H", array("header"), false);
$MiTemplate->parse("OUT_M", array("main"), true);
$MiTemplate->p("OUT_M");
include "../../includes/application_bottom.php";

?>