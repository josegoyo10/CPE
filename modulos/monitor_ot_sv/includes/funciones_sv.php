<?
function cambia_estado($id_ot,$destino,$desctransaccion,$flujo,$accion,$id_estado) {
	$query_ES=" UPDATE ot SET id_estado ='".($destino)."'  WHERE ot_id =".$id_ot;
	tep_db_query($query_ES);

	/*nombre del estado*/
	$qn="SELECT id_estado,esta_nombre,estadoterminal FROM estados where id_estado='".($destino)."'";
	$rq = tep_db_query($qn);
	$res = tep_db_fetch_array($rq);
	$nombre=$res['esta_nombre'];
	$final =$res['estadoterminal'];
	insertahistorial("OT $id_ot ha cambiado a estado $nombre");

	//ìnserta fecha termino si el estado es terminal//
	if ($final){
			$query_ES=" UPDATE ot SET ot_ftermino =now()  WHERE ot_id =".$id_ot;
			tep_db_query($query_ES);
	}
}

/* botones de accion para las paginas*/
function Botones($id_estado,$esta_tipo,$tipoDes,$ot_tipo){
	$otipo=$ot_tipo;
	$origen=$id_estado;
	$tDes=$tipoDes;
	$ie="'".$id_estado."'";
	$et="'".$esta_tipo."'";

	$fl=Flujo($otipo,$tDes,$esta_tipo,$origen);
	$query_B="select desc_transicion,orden, flujo,id_estado_destino as destino from cambiosestado
	where esta_tipo=$et and id_estado_origen=$ie and flujo in (0,$fl) order by orden";
	return $query_B;
}

function Flujo($otipo,$tDes,$esta_tipo,$origen){
	if ($esta_tipo=='TP'){
		if (($origen=='PC')||($origen=='PG')||($origen=='PD')||($origen=='PE')){
			if (($otipo=='PS')&&($tDes==3)){
				$f=1;
			}elseif (($otipo=='PS')&&( ($tDes==2)||($tDes==1) )){
				$f=2;
			}
		}
	}

	if ($esta_tipo=='TE'){
		if (($origen=='ER')||($origen=='ED')||($origen=='EE')||($origen=='EG')){
			if (($otipo=='PE')&&($tDes==3)){
				$f=1;
			}elseif (($otipo=='PE')&&( ($tDes==2)||($tDes==1) )){
				$f=2;
			}
		}
		if (($origen=='ET')||($origen=='EP')||($origen=='EC')){
			$f=3;
		}
	}
return $f;
}

?>