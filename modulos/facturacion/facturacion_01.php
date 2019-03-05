<?
$pag_ini = '../monitor_ot_pe/index.php';
include "../../includes/aplication_top.php";
////////////////////////////////////////////////////////////////////
function muestralistacompleta() {
	global $MiTemplate, $id_instalador;
	$entro=false;
	$MiTemplate->set_file("body","facturacion/facturacion_012.htm");
	$qry="select l.id_local, O.id_os,O.ot_id as id_ot ,O.ot_tipo,od.osde_descripcion,od.osde_cantidad,od.osde_precio,od.osde_descuento,date_format(O.ot_ftermino, '%d/%m/%Y') as ot_ftermino,e.esta_nombre,l.nom_local,
		   if (od.osde_descuento<>0,ROUND((od.osde_precio-od.osde_descuento)*od.osde_cantidad),ROUND(SUM(od.osde_precio*od.osde_cantidad))) 'sub_total'
		   FROM `ot` O
			join os_detalle od on (O.ot_id=od.ot_id)  AND od.osde_subtipoprod IN ('IN', 'VI' ) 
			join os OS on (O.id_os=OS.id_os)
			join locales l on (l.id_local=OS.id_local)
			join estados e on (O.id_estado=e.id_estado)
			where O.id_estado='VM' and ot_tipo='SV' and O.id_instalador=".($id_instalador+0)." 
			group by O.ot_id
			order by l.id_local,O.ot_id";

		$dbFAdet =	tep_db_query($qry);
		$id_local = 0;
		$sumast =0;
		$totpagar=0;
		$porcentaje=0;
		$contador=0;
		$id_local_anterior=0;	
		$i=0;
		$MiTemplate->set_block("body", "detalle_factura", "blo_factura");
		while ($resFAdet = tep_db_fetch_array($dbFAdet)) {
			if (($resFAdet['id_local'] != $id_local_anterior)) {
				if ($contador>0){
					//Setear nombre de bpotones
					$MiTemplate->set_var("nada","0");
					$MiTemplate->set_var("id_local_an",$id_local_anterior);
					$MiTemplate->set_var("b_sub_total","b_sub_total".$id_local_anterior);
					$MiTemplate->set_var("b_total_lote","b_total_lote".$id_local_anterior);
					$MiTemplate->set_var("b_monto_ret","b_monto_ret".$id_local_anterior);
					$MiTemplate->pparse("OUT_M", array("body"), false);
					flush();
					$MiTemplate->clear_var("blo_factura");
				}
				$i=0;
				$sumast=0;
				$totpagar=0;
				$MiTemplate->set_var('nom_local',$resFAdet['nom_local']);
			}

			$id_local_anterior=$resFAdet['id_local'];
			++$contador;
			$entro=true;
			$MiTemplate->set_var("id_local",$resFAdet['id_local']);
			$MiTemplate->set_var("id_ot",$resFAdet['id_ot']);
			$MiTemplate->set_var("ot_id",$resFAdet['ot_id']);
			$MiTemplate->set_var("ot_tipo",$resFAdet['ot_tipo']);
			$MiTemplate->set_var("osde_descripcion",$resFAdet['osde_descripcion']);
			/*$MiTemplate->set_var("osde_cantidad",$resFAdet['osde_cantidad']);
			$MiTemplate->set_var("osde_precio",$resFAdet['osde_precio']);*/
			$MiTemplate->set_var("osde_precio_round",$resFAdet['sub_total']);
			$MiTemplate->set_var("ot_ftermino",$resFAdet['ot_ftermino']);
			$MiTemplate->set_var("esta_nombre",$resFAdet['esta_nombre']);
			$MiTemplate->set_var("MARGEN",MARGEN_A_PAGAR);
			$MiTemplate->set_var("MONTO_RETENCION",MONTO_RETENCION);
			$porcentaje=(((MARGEN_A_PAGAR)*$resFAdet['sub_total'])/100);
			$sumast=$sumast+$resFAdet['sub_total'];
			$MiTemplate->set_var("total_nocalulado",$sumast);
			$MiTemplate->set_var("totpagar",round($porcentaje));
			$MiTemplate->set_var("sumast",$sumast);
			$MiTemplate->set_var("esta_nombre",$resFAdet['esta_nombre']);
			$MiTemplate->parse("blo_factura", "detalle_factura", true);
		}

		if (($resFAdet['id_local'] != $id_local_anterior)) {
			if ($contador>0){
				//Setear nombre de bpotones
				$MiTemplate->set_var("id_local_an",$id_local_anterior);
				$MiTemplate->set_var("b_sub_total","b_sub_total".$id_local_anterior);
				$MiTemplate->set_var("b_monto_ret","b_monto_ret".$id_local_anterior);
				$MiTemplate->set_var("b_total_lote","b_total_lote".$id_local_anterior);
				$MiTemplate->pparse("OUT_M", array("body"), false);
				flush();
			}
			$sumast=0;
			$totpagar=0;
			$i=0;
			$MiTemplate->set_var('nom_local',$resFAdet['nom_local']);
		}
	//if (!$entro) {
	//echo"No existen OT en estado de aprobación para este instalador";
	//$MiTemplate->set_var('mensaje','No existen OT en estado de aprobación para este instalador');	
	//}
	return $entro;
}

$MiTemplate = new Template();
$MiTemplate->debug = 0;
$MiTemplate->set_root(DIRTEMPLATES);
$MiTemplate->set_unknowns("remove");

$MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
$MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);
$MiTemplate->set_var("SUBTITULO1",TEXT_2);
$MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));
$MiTemplate->set_var("id_ot",$id_ot);
$MiTemplate->set_var("id_instalador",($id_instalador+0));

// Agregamos el header
$MiTemplate->set_file("header","header_ident.html");
$MiTemplate->pparse("OUT_M", array("header"), true);
flush();
include "../../menu/menu.php";
if (!$accion){
// Agregamos el main
	$MiTemplate->set_file("main","facturacion/facturacion_011.htm");
	$qry_ins="select distinct i.id_instalador,i.inst_rut,i.inst_nombre,i.inst_paterno,i.inst_materno,cte.monto as cuenta	
		from instaladores i
		left join ctacte_instalador cte on (i.id_instalador=cte.id_instalador)
	where i.id_instalador=".($id_instalador+0);
	$rq = tep_db_query($qry_ins);
	$res = tep_db_fetch_array( $rq );

	$MiTemplate->set_var('inst_rut',$res["inst_rut"]);
	$MiTemplate->set_var('rut',$res["inst_rut"]);
	$MiTemplate->set_var('inst_nombre',$res["inst_nombre"]);
	$MiTemplate->set_var('inst_paterno',$res["inst_paterno"]);
	$MiTemplate->set_var('cuenta',$res["cuenta"]);
	$MiTemplate->set_var('nombre',$res["inst_nombre"]."&nbsp;".$res["inst_paterno"]."&nbsp;".$res["inst_materno"]);
	$qery_cte="select sum(monto) as cuenta from ctacte_instalador where id_instalador=".$id_instalador;
	$rq_cte = tep_db_query($qery_cte);
	$res_cte = tep_db_fetch_array( $rq_cte );
	$MiTemplate->set_var('cuenta',formato_precio($res_cte["cuenta"]));
}
//////////////////////////////////////////////////////////////////////////////////
function refresca_instalador(){
global $MiTemplate, $accion,$rut,$id_instalador;
// Agregamos el main
	$MiTemplate->set_file("main","facturacion/facturacion_011.htm");
	$qry_ins="select distinct i.id_instalador,i.inst_rut,i.inst_nombre,i.inst_paterno,i.inst_materno,cte.monto as cuenta 
		from instaladores i  
		left join ctacte_instalador cte on (i.id_instalador=cte.id_instalador)
		where i.inst_rut=".($rut+0);
	$rq = tep_db_query($qry_ins);
	$res = tep_db_fetch_array( $rq );
	$MiTemplate->set_var('inst_rut',$res["inst_rut"]);
	$MiTemplate->set_var('rut',$res["inst_rut"]);
	$MiTemplate->set_var('inst_nombre',$res["inst_nombre"]);
	$MiTemplate->set_var('inst_paterno',$res["inst_paterno"]);
	$MiTemplate->set_var('nombre',$res["inst_nombre"]."&nbsp;".$res["inst_paterno"]."&nbsp;".$res["inst_materno"]);
	$MiTemplate->set_var('cuenta',$res["cuenta"]);
	$id_instalador=$res["id_instalador"];
	$qery_cte="select sum(monto) as cuenta from ctacte_instalador where id_instalador=".$id_instalador;
	$rq_cte = tep_db_query($qery_cte);
	$res_cte = tep_db_fetch_array( $rq_cte );
	$MiTemplate->set_var('cuenta',$res_cte["cuenta"]);
	$MiTemplate->pparse("OUT_M", array("main"), true);
	flush();
}
$MiTemplate->pparse("OUT_M", array("main"), false);
flush();
if ($accion=='buscar'){
	refresca_instalador($rut);
}

if($accion=='generar'){
	$bst="b_sub_total".($id_local+0);
	$botonsubtotal=$$bst;
	$bmr="b_monto_ret".($id_local+0);
	$botonmontoret=$$bmr;
	$btl="b_total_lote".($id_local+0);
	$botontotallote=$$btl;
	$qeryins="INSERT INTO lote_instalador (id_instalador,estado,fechageneracion,usuario,usuarioingreso,num_factura,monto_factura,retencion_lote) 
	values(".($id_instalador+0).",'P',now(),'".get_login_usr( $ses_usr_id)."','',null,".$botontotallote.",".$botonmontoret.")";
	tep_db_query($qeryins);
	$id_lote= tep_db_insert_id('');
	insertahistorial("Se ha generado un nuevo lote de facturación Nº $id_lote, con un total de factura de $botontotallote y una retención de $botonmontoret");
	foreach ($elementos as $key=>$value) { 
		$ot=$caja[$key];
		$cs="osdeprecioround_".($ot+0);
		$m="margen_".($ot+0);
		$subt="subtotalapagar_".($ot+0);
		$costo=$$cs;
		$margen=$$m;
		$subtotal=$$subt;
		if ($subtotal>=0){
				/* cambia de estado la ot de VM a VP se agregan las canidades y todo lo calculador*/
				$qeryot="UPDATE ot SET margenapagar=".($margen+0).",subtotalapagar=".($subtotal+0).
				",id_lote=".($id_lote+0).",id_estado='VP' where ot_id=".($ot+0);
				tep_db_query($qeryot);
					echo $qeryot."<br>";
				insertahistorial("OT $ot cambia de estado Realizado a Pre-facturado");
				insertahistorial("OT $ot a modificado Margen a $margen% ,Subtotal a $subtotal perteneciendo al Lote $id_lote");
		}
	}
	?>
    <script language="JavaScript">
	    document.location = 'facturacion_03.php?id_lote=<?=$id_lote+0?>';
    </script>
	<?
	tep_exit();
}
if (!muestralistacompleta()){
	// Agregamos el mensaje de error
	$MiTemplate->set_file("main3","facturacion/facturacion_014.htm");
	$MiTemplate->pparse("OUT_M", array("main3"), false);
	flush();
}

// Agregamos el main
$MiTemplate->set_file("main2","facturacion/facturacion_013.htm");
$MiTemplate->pparse("OUT_M", array("main2"), false);
flush();
include "../../includes/footer_cproy.php";
$MiTemplate->pparse("OUT_M", array("footer"), true);
include "../../includes/application_bottom.php";
?>