<?
//$pag_ini = '../monitor_ot_pe/monitor_ot_pe_00.php';
$SIN_PER = 1;
include "../../includes/aplication_top.php";
include "activewidgets.php";

global $el_comentario;
// *************************************************************************************
$qry=" select o.id_os,date_format(ot_fechacreacion, '%d/%m/%Y ')ot_fechacreacion ,e.esta_nombre,t.ot_comentario
	from os_detalle o join ot t on (o.id_os=t.id_os) 
	join estados e on (e.id_estado=t.id_estado)
	where t.ot_id=" . ($id_ot + 0) ;
$rq = tep_db_query($qry);
$res = tep_db_fetch_array( $rq );
$id_os=$res['id_os'];
$estado=$res['esta_nombre'];
$fecha_creacion=$res['ot_fechacreacion'];
$f=$res['ot_fechacreacion'];
$el_comentario=$res['ot_comentario'];
/***********************/


$MiTemplate = new Template();
// asignamos degug maximo
$MiTemplate->debug = 0;
// root directory de los templates
$MiTemplate->set_root(DIRTEMPLATES);
// variables perdidas
$MiTemplate->set_unknowns("remove");

$MiTemplate->set_var("id_os",$id_os);
$MiTemplate->set_var("id_ot",$id_ot);
$MiTemplate->set_var("select_prov",$select_prov);
$MiTemplate->set_var("ot_fcompra_pe",$ot_fcompra_pe);

$MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
$MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);
$MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));

//preguntamos si existe proveedor //
$qry_prov="select date_format(ot_freactivacion, '%d/%m/%Y')ot_fcompra_pe,o.ot_id, o.id_tipodespacho,o.id_os,pv.id_proveedor,pp.cod_prod1,pv.rut_prov,  pv.razsoc_prov,pv.nom_prov from os_detalle o
	join ot t on (t.ot_id=o.ot_id)
	join prodxprov pp on (pp.id_producto=o.id_producto)
	join proveedores pv on (pp.cod_prov=pv.cod_prov)
	where rut_prov<>0  and t.ot_id =".($id_ot+0);
	$rq = tep_db_query($qry_prov);
	$res = tep_db_fetch_array( $rq );
	$csap=$res['cod_prod1'];
	if (!$res['rut_prov']){
	?>
	<script language="JavaScript">
		window.alert('Este producto no tiene proveedores');
		window.close();
	</script>
	<?
	exit();
	}else{
// Agregamos el main
$MiTemplate->set_file("main","monitor_ot_pe/pop_compra_pe.htm");
/* recuperamos los proveedores*/
$MiTemplate->set_block("main", "proveedores", "BLO_pro");
$qry_prov="select DISTINCT pv.id_proveedor, date_format(ot_freactivacion, '%d/%m/%Y')ot_freactivacion,o.ot_id, o.id_tipodespacho,o.id_os,pp.cod_prod1,pv.rut_prov,pv.razsoc_prov,pv.nom_prov
	from os_detalle o
	join ot t on (t.ot_id=o.ot_id)
	join prodxprov pp on (pp.id_producto=o.id_producto)
 	join proveedores pv on (pp.cod_prov=pv.cod_prov)
where rut_prov<>0  and t.ot_id =".($id_ot+0);
query_to_set_var( $qry_prov, $MiTemplate, 1, 'BLO_pro', 'proveedores' );
	


if ($accion){
	//Asigno el tipo de flujo para ver el cambio de estado correspondiente
	$query = "	SELECT	id_tipodespacho, ot_tipo, desp_ddp,id_estado
				FROM	ot
				WHERE	ot_id = " . ($id_ot + 0) ; 
	$rq = tep_db_query($query);
	$res = tep_db_fetch_array( $rq );

	if ($res['desp_ddp'])
		$flujo = 3;
	elseif ($res['id_tipodespacho'] == '3')
		$flujo = 1;
	else 
		$flujo = 2;
	$queryHist="SELECT distinct esta_nombre,desc_transicion, ce.orden,flujo,id_estado_destino, estadoterminal ,ot_comentario
			FROM ot join cambiosestado ce on ce.id_estado_origen = ot.id_estado 
					join estados e on  e.id_estado = id_estado_destino 
			WHERE ot_id = ".($id_ot+0)." and ot_tipo = 'PE' and flujo in (0, $flujo) and id_estado_destino not in ('EN')  
			ORDER BY ce.orden";
	$rq = tep_db_query($queryHist);
	$res1 = tep_db_fetch_array( $rq );
	$nombre =$res1['esta_nombre'];
	$estado=$res1['id_estado_destino'];
	$el_comentario=$res1['ot_comentario'];

		if ($des_prov=="no")
			$des_ddp=0;
		if ($des_prov=="si"){
			$des_ddp=1;
			$estado='EP';
		}
/*updatiar la ot*/
	$komentario="";
	$qU="update ot SET desp_ddp=".$des_ddp.",ot_comentario='".$comentario."', id_estado='".$estado."',id_proveedor=".$select_prov." ,noc_sap=".$noc_sap.", ot_freactivacion='" . fecha_php2db_new($ot_fcompra_pe)."' where ot_id=".($id_ot+0);
	tep_db_query($qU);
	/*para los datos del proveedor*/
	$qery_prov="SELECT cod_prov,rut_prov,nom_prov FROM proveedores where id_proveedor=".($select_prov+0); 
	$rq = tep_db_query($qery_prov);
	$res1 = tep_db_fetch_array( $rq );
	$nom_p=$res1['nom_prov'];
	$rut_p=$res1['rut_prov']."-".dv($res1['rut_prov']);
	$orden_com=$noc_sap;
	$comentario=$comentario;
	//Se registra en el tracking varia segun si lleva o no comentarios
		if ($comentario){
			$komentario=", agrega el siguiente comentario: ".$comentario;
		}

		insertahistorial("OT $id_ot asigna Proveedor (Rut: $rut_p, Nombre: $nom_p),cambia a estado $nombre, asigna número Nº $orden_com a la orden de compra$komentario y deja la fecha de reactivación a $ot_fcompra_pe");	
	?>
	<script language="JavaScript">
		window.returnValue = 'refresh';
		window.close();
	</script>
	<?
	//tep_exit();
}
	if ($des_prov=="no"){
		$checkedno="checked";
		$MiTemplate->set_var("checkedno",$checkedno);
	}	
	if ($des_prov=="si"){
		$checkedsi="checked";
		$MiTemplate->set_var("checkedsi",$checkedsi);
	}
	if ($des_prov==''){
			$MiTemplate->set_var("checkedno","checked");
	}
	$MiTemplate->set_var("ot_comentario",$el_comentario);
$MiTemplate->pparse("OUT_H", array("header"), false);
$MiTemplate->parse("OUT_M", array("main"), true);
$MiTemplate->p("OUT_M");
include "../../includes/application_bottom.php";
	}
?>
