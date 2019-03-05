<?
$pag_ini = '../monitor_ot_pe/index.php';
include "../../includes/aplication_top.php";


///////////////////// ACCIONES //////////////////////

$MiTemplate = new Template();
$MiTemplate->debug = 0;
$MiTemplate->set_root(DIRTEMPLATES);
$MiTemplate->set_unknowns("remove");

$MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
$MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);
$MiTemplate->set_var("SUBTITULO1",TEXT_2);
$MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));
$MiTemplate->set_var("id_ot",$id_ot);
$MiTemplate->set_var("rut",$rut);

// Agregamos el header
$MiTemplate->set_file("header","header_ident.html");

// Agregamos el main
$MiTemplate->set_file("main","facturacion/facturacion_00.htm");
$existeinst=false;
if ($accion){
	$qry_ins="select distinct i.id_instalador,i.inst_rut,i.inst_nombre,i.inst_paterno,i.inst_materno from instaladores i  where inst_rut like '%".$inst_rut."%'";
	$rq = tep_db_query($qry_ins);
	$res = tep_db_fetch_array( $rq );
		if ($res['id_instalador']){
			$existeinst=true;
			?>
			<script language="JavaScript">
				document.location = 'facturacion_01.php?id_instalador=<?=$res['id_instalador']+0?>';
			</script>
			<?
		}else{
			?>
			<script language="JavaScript">
				alert("No existe el Rut del instalador");
				history.back();
			</script>
			<?
	}
  tep_exit();
}

// Agregamos el footer
include "../../includes/footer_cproy.php";

$MiTemplate->pparse("OUT_H", array("header"), false);
include "../../menu/menu.php";
$MiTemplate->parse("OUT_M", array("main","footer"), true);
$MiTemplate->p("OUT_M");
include "../../includes/application_bottom.php";

?>