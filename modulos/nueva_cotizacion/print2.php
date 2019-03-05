<?
//$pag_ini = '../nueva_cotizacion/nueva_cotizacion_00.php';
$SIN_PER = 1;
include "../../includes/aplication_top.php";
// include "activewidgets.php";

// include_idioma_mod( $ses_idioma, "nueva_cotizacion_01" );

// *************************************************************************************

/* acciones*/
$querysel="select CL.clie_rut,S.os_descripcion,S.os_comentarios ,S.id_os ,S.clie_rut ,CL.clie_nombre,CL.clie_paterno,CL.clie_materno,L.nom_local ,E.esta_nombre,P.proy_nombre,D.dire_direccion,D.dire_observacion,D.dire_telefono,CO.comu_nombre from os S inner join clientes CL on (CL.clie_rut=S.clie_rut) Inner join locales L on (L.id_local=S.id_local) inner join estados E on (S.id_estado=E.id_estado) inner join proyectos P on (P.id_proyecto=S.id_proyecto) inner join direcciones D on (D.id_direccion=S.id_direccion) inner join comuna CO on (D.id_comuna=CO.id_comuna) where id_os=".($id_os+0)."";
$osSel = tep_db_query($querysel);
$osSel = tep_db_fetch_array( $osSel );

/* si viene SIN direccion de despacho , como en sumario*/
    $querydiredes="select D.dire_direccion as dire_direccion_des,D.dire_observacion as dire_observacion_des,D.dire_telefono as dire_telefono_des,CO.comu_nombre as comu_nombre_des from direcciones D inner join comuna CO on (D.id_comuna=CO.id_comuna) where D.dire_defecto='p'";
    $diredes = tep_db_query($querydiredes);
    $diredes = tep_db_fetch_array( $diredes );

function digiVer($r){
    $s=1;for($m=0;$r!=0;$r/=10)
    $s=($s+$r%10*(9-$m++%6))%11;
return chr($s?$s+47:75);}

$digito=digiVer($osSel['clie_rut']);

/* si viene con una direccionde despacho, se da en al caso de cerra e imprimir la cotizacion*/
if($dire_des){
    $querydiredes="select D.dire_direccion as dire_direccion_des,D.dire_observacion as dire_observacion_des,D.dire_telefono as dire_telefono_des,CO.comu_nombre as comu_nombre_des from direcciones D inner join comuna CO on (D.id_comuna=CO.id_comuna) where D.id_direccion =".($dire_des+0)."";
    $diredes = tep_db_query($querydiredes);
    $diredes = tep_db_fetch_array( $diredes );
}


/***********************/


$MiTemplate = new Template();
// asignamos degug maximo
$MiTemplate->debug = 0;
// root directory de los templates
$MiTemplate->set_root(DIRTEMPLATES);
// variables perdidas
$MiTemplate->set_unknowns("remove");

$MiTemplate->set_var("id_os",$id_os);
$MiTemplate->set_var("dire_des",$dire_des);

$MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
$MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);
$MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));

     $MiTemplate->set_var("digito",$digito);
     $MiTemplate->set_var("esta_nombre",$osSel['esta_nombre']);
     $MiTemplate->set_var("nom_local",$osSel['nom_local']);
     $MiTemplate->set_var("os_descripcion",$osSel['os_descripcion']);
     $MiTemplate->set_var("os_comentarios",$osSel['os_comentarios']);
     $MiTemplate->set_var("os_fechacotizacion",$osSel['os_fechacotizacion']);
     $MiTemplate->set_var("proy_nombre",$osSel['proy_nombre']);
     $MiTemplate->set_var("clie_nombre",$osSel['clie_nombre']);
     $MiTemplate->set_var("clie_paterno",$osSel['clie_paterno']);
     $MiTemplate->set_var("clie_materno",$osSel['clie_materno']);
     $MiTemplate->set_var("clie_rut",$osSel['clie_rut']);
     $MiTemplate->set_var("dire_direccion",$osSel['dire_direccion']);
     $MiTemplate->set_var("dire_telefono",$osSel['dire_telefono']);
     $MiTemplate->set_var("comu_nombre",$osSel['comu_nombre']);

     $MiTemplate->set_var("dire_telefono_des",$osSel['dire_telefono']);
     $MiTemplate->set_var("dire_direccion_des",$osSel['dire_direccion']);
     $MiTemplate->set_var("comu_nombre_des",$osSel['comu_nombre']);
     $MiTemplate->set_var("dire_observacion_des",$osSel['dire_observacion']);



if($dire_des){
     $MiTemplate->set_var("dire_telefono_des",$diredes['dire_telefono_des']);
     $MiTemplate->set_var("dire_direccion_des",$diredes['dire_direccion_des']);
     $MiTemplate->set_var("comu_nombre_des",$diredes['comu_nombre_des']);
     $MiTemplate->set_var("dire_observacion_des",$diredes['dire_observacion_des']);
}



// Agregamos el header
/*$MiTemplate->set_file("header","header_ident.html");*/

// Agregamos el main
$MiTemplate->set_file("main","nueva_cotizacion/print2.htm");

$MiTemplate->set_block("main", "Codigos", "BLO_cod");
$query="
		SELECT	cod_barra cod_barra, 
				cod_barra cod_barra2, 
				osde_descripcion, 
				cod_sap, 
				osde_cantidad, 
				(osde_precio-osde_descuento) osde_precio,
				ROUND((osde_precio-osde_descuento)*osde_cantidad) subtotal
		FROM os_detalle osd 
		WHERE id_os = " . ($id_os+0) . " order by id_os_detalle desc ";
$rq = tep_db_query($query);

if( tep_db_num_rows($rq) > 0 ) {
	$res = mysql_fetch_assoc( $rq );
	$arr_k = array_keys ($res);

	$rq = tep_db_query($query);
	$align = "left";
	$counter = 0;
	$suma = 0;
	while( $res = tep_db_fetch_array( $rq ) ) {
		++$counter;
		for( $i = 0; $i < sizeof( $arr_k ); $i++ ) {
			if ($arr_k[$i] == 'cod_barra')
				if ($align=="left") {
					$MiTemplate->set_var("cod_barraleft",gencode_EAN13($res[$arr_k[$i]], 200, 100));
					$MiTemplate->set_var("cod_barraright","");
				}
				else {
					$MiTemplate->set_var("cod_barraright",gencode_EAN13($res[$arr_k[$i]], 200, 100));
					$MiTemplate->set_var("cod_barraleft","");
				}
			else 
				$MiTemplate->set_var($arr_k[$i],tohtml( $res[$arr_k[$i]] ));
		}
		$MiTemplate->parse('BLO_cod', 'Codigos', true);

		$suma += $res['subtotal'] ; 

		$align = ($align=="left")?"right":"left";
	}

	$MiTemplate->set_var("cant_prod",$counter);
	$MiTemplate->set_var("suma_prod",formato_precio($suma));
}




// Agregamos el footer
/* $MiTemplate->set_file("footer","footer_ident.html");*/

$MiTemplate->parse("OUT_M", array("main","footer"), true);
$MiTemplate->p("OUT_M");

include "../../includes/application_bottom.php";
?>