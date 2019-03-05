<?php
$pag_ini = '../nueva_cotizacion/nueva_cotizacion_00.php';
include "../../includes/aplication_top.php";
include "includes/bdIRS.php";
include "activewidgets.php";
require_once('../../wsLiqFletes/CalculoFlete/Fletes.php');

include_idioma_mod( $ses_idioma, "nueva_cotizacion_04" );


$consul_coti = "SELECT id_estado FROM os WHERE id_os = $id_os ";
$resul_coti= tep_db_query($consul_coti);
$arreglo_coti = tep_db_fetch_array($resul_coti);

if($arreglo_coti['id_estado'] == 'SE')
{
    header ('Location: nueva_cotizacion_05.php?id_os=' . $id_os);
}
file_put_contents("id_os.txt", $id_os);
/* Liquidaci�n de Fletes */
if ($accion=="liquidacion"  && $_SESSION['liqTrans'] == 0){
    $_SESSION['liqTrans'] = 1;
    $liquido_flete = 0;

    $consul_locaclizacion = "SELECT id_departamento, id_provincia, id_ciudad, id_localidad, id_comuna  FROM direcciones WHERE clie_rut = $clie_rut AND id_direccion = $direccion";
    $resul_locaclizacion= tep_db_query($consul_locaclizacion);
    $arreglo_locaclizacion = tep_db_fetch_array($resul_locaclizacion);

    $consul_local = "SELECT O.id_local, L.id_localizacion, L.cod_local_pos   FROM os O
                    LEFT JOIN locales L ON L.id_local = O.id_local
                    WHERE id_os = $id_os ";
    
    file_put_contents("consul_local.txt", $consul_local);
    
    $resul_local= tep_db_query($consul_local);
    $arreglo_local = tep_db_fetch_array($resul_local);

    $num = strlen($arreglo_local['id_localizacion']);
//    Mantis 29886: Corregir Consulta WS Fletes
//    Inicio
    if($num < 14){
	$arreglo_local['id_localizacion'] = "0" . $arreglo_local['id_localizacion'];
    }
//  Fin  
    //file_put_contents('id_localizacion.txt', $arreglo_local['id_localizacion']);
    $id_departamento = substr($arreglo_local['id_localizacion'],  0, -12);
    $id_provincia = substr($arreglo_local['id_localizacion'], 2, -9);
    $id_ciudad = substr($arreglo_local['id_localizacion'], 5, -6);
    $id_localidad = substr($arreglo_local['id_localizacion'], 8, -3);
    $id_barrio = substr($arreglo_local['id_localizacion'], 11);


    $consul_dire = "SELECT dire_direccion FROM direcciones WHERE id_direccion = $direccion ";
    $resul_dire = tep_db_query($consul_dire);
    $arreglo_dire = tep_db_fetch_array($resul_dire);

    if($_SESSION['permiso_cruce_coti'] == 0)
    {
    $consul_cruzadas1 =  "SELECT DISTINCT O.id_os,  DE.id_tipodespacho FROM os O
						INNER JOIN clientes C ON O.clie_rut = C.clie_rut
						INNER JOIN os_detalle DE ON DE.id_os = O.id_os
						INNER JOIN direcciones D ON D.clie_rut = C.clie_rut AND O.id_direccion = (SELECT id_direccion FROM os WHERE id_os = $id_os)
						INNER JOIN ot OT ON OT.id_os = O.id_os  AND  O.id_estado NOT IN ('PM', 'EM')  AND OT.ot_tipo IN ('PS', 'PE')
						AND O.id_estado = 'SP' AND id_local = (SELECT id_local FROM os WHERE id_os = $id_os)
						AND D.id_ciudad = (SELECT clie_ciudad FROM clientes WHERE clie_rut = (SELECT clie_rut FROM os WHERE id_os = $id_os))
						AND D.dire_localizacion = (SELECT dire_localizacion FROM direcciones WHERE clie_rut = $clie_rut AND id_direccion = $direccion)
						AND D.id_comuna = (SELECT id_comuna FROM direcciones WHERE clie_rut = $clie_rut AND id_direccion = $direccion)
						AND (DE.osde_fecha_entrega = (SELECT osde_fecha_entrega FROM os_detalle WHERE id_os = $id_os AND id_tipodespacho in (1,6) AND osde_tipoprod IN ('PS', 'PE')  AND osde_fecha_entrega <> '' GROUP BY id_tipodespacho) )
						AND DE.id_tipodespacho in (1,6) ";

    }else{
	$consul_cruzadas1 = "SELECT DISTINCT O.id_os,  DE.id_tipodespacho FROM os O
                            INNER JOIN clientes C ON O.clie_rut = C.clie_rut
                            INNER JOIN os_detalle DE ON DE.id_os = O.id_os
                            INNER JOIN direcciones D ON D.clie_rut = C.clie_rut AND O.id_direccion = (SELECT id_direccion FROM os WHERE id_os = $id_os)
                            WHERE  O.id_estado IN ('SE', 'SP')  AND id_local = (SELECT id_local FROM os WHERE id_os = $id_os)
                            AND D.id_ciudad = (SELECT clie_ciudad FROM clientes WHERE clie_rut = (SELECT clie_rut FROM os WHERE id_os = $id_os))
                            AND D.dire_localizacion = (SELECT dire_localizacion FROM direcciones WHERE clie_rut = $clie_rut AND id_direccion = $direccion)
                            AND D.id_comuna = (SELECT id_comuna FROM direcciones WHERE clie_rut = $clie_rut AND id_direccion = $direccion)
                            AND (DE.osde_fecha_entrega = (SELECT osde_fecha_entrega FROM os_detalle WHERE id_os = $id_os AND id_tipodespacho in (1,6) AND osde_tipoprod IN ('PS', 'PE') AND osde_fecha_entrega <> '' GROUP BY id_tipodespacho) )
                            AND DE.id_tipodespacho in (1,6)
                            AND O.id_os <> $id_os ";

    }

    //echo "Valido coti cruzadas";
	//echo $consul_cruzadas1, "<br><br>";

    $resul_cruzadas1 = tep_db_query($consul_cruzadas1);

    $lista_idos = '';
    $arreglo_idos = array();

    while($arreglo_cruzadas1 = tep_db_fetch_array($resul_cruzadas1))
    {
            $lista_idos = $lista_idos . "," . $arreglo_cruzadas1['id_os'];
    }

    $lista_idos = substr($lista_idos, 1);

    $consul_lista = "UPDATE os SET os_cotizaciones_cruzadas = '$lista_idos' WHERE id_os = $id_os ";
    $resul_lista= tep_db_query($consul_lista);

    if($lista_idos != ''){
        $id_os_lista =  $id_os . ',' . $lista_idos;
    }else{
        $id_os_lista = $id_os;}

    $consul_Peso = "SELECT  SUM((P.peso/1000) * osde_cantidad) AS peso,  T.nombre, O.id_tipodespacho, O.id_os  FROM os_detalle O
                    INNER JOIN productos P ON O.id_producto = P.id_producto
                    INNER JOIN tipos_despacho T ON T.id_tipodespacho =  O.id_tipodespacho
                    WHERE id_os = $id_os AND O.id_tipodespacho <> 3 AND O.id_tipodespacho <> 4 AND osde_tipoprod IN ('PS', 'PE')
                    GROUP BY O.id_tipodespacho ";

    $resul_Peso= tep_db_query($consul_Peso);

    while($arreglo_Peso = tep_db_fetch_array($resul_Peso))
    {
        if($arreglo_Peso['id_tipodespacho'] == 1 || $arreglo_Peso['id_tipodespacho'] == 6){
                $id_tipodespacho = 2;
        }
        else{
                $id_tipodespacho = 1;
        }

	$xml="<despacho>
                    <direccion>" . $arreglo_dire['dire_direccion'] . "</direccion>
                    <idDepartamento>" . $arreglo_locaclizacion['id_departamento'] . "</idDepartamento>
                    <idMunicipio>" . $arreglo_locaclizacion['id_provincia'] . "</idMunicipio>
                    <idCentroPoblado>" . $arreglo_locaclizacion['id_ciudad'] . "</idCentroPoblado>
                    <idLocalidad>" . $arreglo_locaclizacion['id_localidad'] . "</idLocalidad>
                    <idBarrio>" . $arreglo_locaclizacion['id_comuna'] . "</idBarrio>
                </despacho>
	        <centroSuministro>
                    <idLocal>" . $arreglo_local['cod_local_pos'] . "</idLocal>
                    <idDepartamento>$id_departamento</idDepartamento>
                    <idMunicipio>$id_provincia</idMunicipio>
                    <idCentroPoblado>$id_ciudad</idCentroPoblado>
                    <idLocalidad>$id_localidad</idLocalidad>
                    <idBarrio>$id_barrio</idBarrio>
                </centroSuministro>
	        <entregaProductos>
                    <lstTipoDespacho>
                            <codigoTipo>$id_tipodespacho</codigoTipo>
                            <peso>" . $arreglo_Peso['peso'] . "</peso>
                    </lstTipoDespacho>
	        </entregaProductos>
                <codEmpresaTransportadora>0</codEmpresaTransportadora>";

      file_put_contents("xmlFletes.txt",$xml); 
       

		$service = new Fletes();
		$response = $service->calcular($xml);
                file_put_contents("responceok.txt", $response); 
		if ($response) {
			//print_r($response);
			//echo "<br>";
		}
		else {
		echo "<SCRIPT LANGUAGE='JavaScript'>";
			echo "alert('" . $response['exception']['message'] . "');";
		echo "</SCRIPT>";
		}

		$Maximo = count($response['data']['lstValorFlete']);

			$consul_cruza = "SELECT id_tipodespacho, id_os FROM os_detalle WHERE osde_tipoprod IN ('PS', 'PE') AND id_os IN ($lista_idos) GROUP BY id_tipodespacho ";
			$resul_cruza = tep_db_query($consul_cruza);

			//echo $consul_cruza, "<br>";

			$verdad = 0;


			while($arreglo_cruza = tep_db_fetch_array($resul_cruza))
			{
				if($arreglo_cruza['id_tipodespacho'] == $arreglo_Peso['id_tipodespacho'] && $arreglo_cruza['id_tipodespacho'] != 2 )
				{
				$verdad = 1;
				}
			}


		if($Maximo == 2)
		{
			$liquido_flete = 1;
			for($j=0; $j<$Maximo;  $j++)
			{
			$consul_flete = "SELECT  C.cod_barra, P.* FROM productos  P
							 LEFT JOIN codbarra C ON P.cod_prod1 = C.cod_prod1
							 WHERE P.prod_tipo = 'SV' AND P.prod_subtipo = 'DE' 
							 AND C.estadoactivo = 'C' AND  P.estadoactivo = 'C' 
							 AND P.cod_prod1 = " . $response['data']['lstValorFlete'][$j]['codSAP'];
			$resul_flete = tep_db_query($consul_flete);
			$arreglo_flete = tep_db_fetch_array($resul_flete);

				if($response['exception']['message'] == '' )
				{
					if($j==1) {
						$consulta = "INSERT INTO  os_detalle( id_origen,         id_tipodespacho,              id_os,                   osde_tipoprod,                     osde_subtipoprod,                 osde_instalacion,                             osde_precio,                                                    osde_cantidad,                     osde_descuento,                   cod_sap,                                           osde_descripcion,                                  id_producto,                            cod_barra,                 ind_dec )
						                             VALUES(      4, ". $arreglo_Peso['id_tipodespacho'] .",   $id_os,      '" . $arreglo_flete['prod_tipo'] . "',  '" . $arreglo_flete['prod_subtipo'] . "',0, " . $response['data']['lstValorFlete'][$j]['valorFlete'] . ",       " . $response['data']['lstValorFlete'][$j]['cantidad'] . ",         0,       '" . $response['data']['lstValorFlete'][$j]['codSAP'] . "',     '" . $arreglo_flete['des_corta'] . "',   " . $arreglo_flete['id_producto'] . ", '" . $arreglo_flete['cod_barra'] . "',0)";
						$resultado = tep_db_query($consulta);
					}
					else{
						if($verdad != 1)
						{
						$consulta = "INSERT INTO  os_detalle( id_origen,         id_tipodespacho,           id_os,                   osde_tipoprod,                     osde_subtipoprod,                 osde_instalacion,             osde_precio,                                                                osde_cantidad,                         osde_descuento,                       cod_sap,                                           osde_descripcion,                       id_producto,                                   cod_barra,                 ind_dec )
							                      VALUES(      4, ". $arreglo_Peso['id_tipodespacho'] .",   $id_os,      '" . $arreglo_flete['prod_tipo'] . "',  '" . $arreglo_flete['prod_subtipo'] . "',          0, " . $response['data']['lstValorFlete'][$j]['valorFlete'] . ",        " . $response['data']['lstValorFlete'][$j]['cantidad'] . ",              0,       '" . $response['data']['lstValorFlete'][$j]['codSAP'] . "',     '" . $arreglo_flete['des_corta'] . "',   " . $arreglo_flete['id_producto'] . ", '" . $arreglo_flete['cod_barra'] . "',         0)";
						$resultado = tep_db_query($consulta);
						}
					}

				}

			else{
			echo "<SCRIPT LANGUAGE='JavaScript'>";
				echo "alert('" . $response['exception']['message'] . "');";
			echo "</SCRIPT>";
			}


			}

		}
		else if($Maximo > 2){
			$liquido_flete = 1;

			if($verdad != 1)
			{

			$consul_flete = "SELECT  C.cod_barra, P.* FROM productos  P 
							 LEFT JOIN codbarra C ON P.cod_prod1 = C.cod_prod1
							 WHERE P.prod_tipo = 'SV' AND P.prod_subtipo = 'DE' 
							 AND C.estadoactivo = 'C' AND  P.estadoactivo = 'C' 
							 AND P.cod_prod1 = " . $response['data']['lstValorFlete']['codSAP'];
			$resul_flete = tep_db_query($consul_flete);
			$arreglo_flete = tep_db_fetch_array($resul_flete);

				if($response['exception']['message'] == '')
				{
				$consulta = "INSERT INTO  os_detalle( id_origen,         id_tipodespacho,                 id_os,                   osde_tipoprod,                     osde_subtipoprod,                 osde_instalacion,             osde_precio,                                                       osde_cantidad,                               osde_descuento,                   cod_sap,                                           osde_descripcion,                       id_producto,                               cod_barra,                      ind_dec )
						                      VALUES(      4, ". $arreglo_Peso['id_tipodespacho'] .",     $id_os,      '" . $arreglo_flete['prod_tipo'] . "',  '" . $arreglo_flete['prod_subtipo'] . "',          0, " . $response['data']['lstValorFlete']['valorFlete'] . ",       " . $response['data']['lstValorFlete']['cantidad'] . ",              0,       '" . $response['data']['lstValorFlete']['codSAP'] . "',     '" . $arreglo_flete['des_corta'] . "',   " . $arreglo_flete['id_producto'] . ", '" . $arreglo_flete['cod_barra'] . "',            0)";
				$resultado = tep_db_query($consulta);
				}

			else{
			echo "<SCRIPT LANGUAGE='JavaScript'>";
				echo "alert('" . $response['exception']['message'] . "');";
			echo "</SCRIPT>";
			}


			}
		}
		else{
			echo "<SCRIPT LANGUAGE='JavaScript'>";
				echo "alert('" . $response['exception']['message'] . "');";
			echo "</SCRIPT>";
			}



		$consul_zona = " UPDATE os SET zona = '" . $response['data']['zone'] . "'  WHERE id_os = $id_os ";
		$resul_zona= tep_db_query($consul_zona);
	}


}












// *************************************************************************************

/** Inicio Acciones  **/
function recupera_descprod ($ean) {
	$queryest="select des_corta from productos p join codbarra c on c.id_producto = p.id_producto where cod_barra = '$ean'";
    $esta = tep_db_query($queryest);
    $resp = tep_db_fetch_array( $esta );
    $des_corta=str_replace("'"," ",$resp['des_corta']);
    return $des_corta;
}

$consul_visible = "SELECT id_os FROM os_detalle WHERE id_os = $id_os AND id_tipodespacho IN (1, 2, 6) AND osde_tipoprod IN ('PS', 'PE') ";
$resulvisible = tep_db_query($consul_visible);
$visible = tep_db_fetch_array($resulvisible);



/*saca el estado de la cotizacion*/
    $queryest="select id_estado from os WHERE id_os = " . ($id_os+0);
    $esta = tep_db_query($queryest);
    $esta = tep_db_fetch_array( $esta );

    /*saca los nombre de la persona que ingreso los datos en la OS*/
    $qnombre="select U.usr_nombres,U.usr_apellidos from usuarios U inner join os OS on (OS.usr_id=U.usr_id) where id_os=".($id_os+0)."";
    $rq = tep_db_query($qnombre);
    $res = tep_db_fetch_array( $rq );
    $nc=$res['usr_nombres']." ".$res['usr_apellidos'];

/*solo cambiara el estado de la cot si es != de anulada y caducada*/
if (($esta['id_estado']!='SC')&&($esta['id_estado']!='SN')){
    if ($accion == "fin" ){
		if (!$validact) {
			//Iniciamos proceso de validaci�n de c�digos en controlador
			$db_1 =	tep_db_query("SELECT ip_local FROM os join locales l on os.id_local = l.id_local WHERE id_os = " . ($id_os+0));
			$res_1 = tep_db_fetch_array( $db_1 );


			if ($msgerr1) {

			}
			elseif ($msgerr2) {

			}
			else {
				$query = " UPDATE os SET id_estado = 'SE' WHERE id_os = " . ($id_os+0);
				tep_db_query($query);

				/* saca nonbre del estado para poner en el trackin*/
				$querN="select esta_nombre from estados where id_estado = 'SE'";
				$estaN = tep_db_query($querN);
				$estaN = tep_db_fetch_array( $estaN );
				$esta_nombre=$estaN['esta_nombre'];

				insertahistorial("OS $id_os ha cambiado a estado $esta_nombre");

				header ('Location: nueva_cotizacion_05.php?id_os='.($id_os+0));
				tep_exit();
			}
		}
		else {
			$query = " UPDATE os SET id_estado = 'SE' WHERE id_os = " . ($id_os+0);
			tep_db_query($query);

			/* saca nonbre del estado para poner en el trackin*/
			$querN="select esta_nombre from estados where id_estado = 'SE'";
			$estaN = tep_db_query($querN);
			$estaN = tep_db_fetch_array( $estaN );
			$esta_nombre=$estaN['esta_nombre'];

			insertahistorial("OS $id_os ha cambiado a estado $esta_nombre");

			header ('Location: nueva_cotizacion_05.php?id_os='.($id_os+0));
			tep_exit();
		}
	}

    if ($accion == "Nofin" ){
        ?>
        <script language="JavaScript">
        window.alert("Uno o m�s productos de la cotizaci�n no tienen precio definido.\nLa cotizaci�n ha quedado ingresada como 'Pendiente' hasta que se completen todos los precios de los productos");
        </script>
        <?
        $query = " UPDATE os SET id_estado = 'SI' WHERE id_os = " . ($id_os+0);
        tep_db_query($query);

        /* saca nonbre del estado para poner en el trackin*/
        $querN="select esta_nombre from estados where id_estado = 'SI'";
        $estaN = tep_db_query($querN);
        $estaN = tep_db_fetch_array( $estaN );
        $esta_nombre=$estaN['esta_nombre'];

		insertahistorial("OS $id_os ha cambiado a estado $esta_nombre");

    }
    if ($accion == "Des" ){
        ?>
        <script language="JavaScript">
        window.alert("Uno o m�s productos de la cotizaci�n requieren un tipo de despacho.\nDebe Agregar servicio de despacho para estos productos\no cambiar el tipo de despacho a 'Retira Cliente'");
        </script>
        <?php
        }
}else{
        ?>
        <script language="JavaScript">
        window.alert("Estado Final de la cotizaci�n");
        </script>
        <?php
}


/*cuando entra sin accion */
$queryPRI = "SELECT CONCAT(NE.DESCRIPTION, ' - ', LO.DESCRIPTION) AS comu_nombre_des,  DE.DESCRIPTION AS nombre_departamento, CI.DESCRIPTION AS nombre_ciudad,  OS.id_direccion, OS.clie_rut,D.id_direccion,D.dire_nombre AS dire_nombre2, D.dire_direccion AS dire_direccion_des, D.id_direccion ,D.dire_defecto ,  D.dire_telefono AS dire_telefono_des ,D.dire_observacion AS dire_observacion_des
			FROM os OS INNER JOIN  direcciones D on (OS.id_direccion = D.id_direccion)
			LEFT JOIN cu_department DE ON DE.ID = D.id_departamento AND DE.ID = ( SELECT id_departamento FROM direcciones WHERE id_direccion = ( SELECT id_direccion FROM os WHERE id_os = $id_os ) )
			LEFT JOIN cu_city CI ON CI.ID = D.id_ciudad AND CI.ID = ( SELECT id_ciudad FROM direcciones WHERE id_direccion = ( SELECT id_direccion FROM os WHERE id_os = $id_os ) )
			AND CI.ID_PROVINCE = ( SELECT id_provincia FROM direcciones WHERE id_direccion = ( SELECT id_direccion FROM os WHERE id_os = $id_os ) )
			AND CI.ID_DEPARTMENT = ( SELECT id_departamento FROM direcciones WHERE id_direccion = ( SELECT id_direccion FROM os WHERE id_os = $id_os ) )
			LEFT JOIN cu_locality LO ON  LO.ID =  D.id_localidad AND LO.ID_CITY = ( SELECT id_ciudad FROM direcciones WHERE id_direccion = ( SELECT id_direccion FROM os WHERE id_os = $id_os ) )
			AND LO.ID_PROVINCE = ( SELECT id_provincia FROM direcciones WHERE id_direccion = ( SELECT id_direccion FROM os WHERE id_os = $id_os ) )
			AND LO.ID_DEPARTMENT = ( SELECT id_departamento FROM direcciones WHERE id_direccion = ( SELECT id_direccion FROM os WHERE id_os = $id_os ) )
			LEFT JOIN cu_neighborhood NE ON NE.LOCATION = D.dire_localizacion AND NE.LOCATION = ( SELECT dire_localizacion FROM direcciones WHERE id_direccion = ( SELECT id_direccion FROM os WHERE id_os = $id_os ) )
			AND NE.LOCATION = D.dire_localizacion
			WHERE OS.id_os = $id_os AND D.id_direccion = ( SELECT id_direccion FROM os WHERE id_os = $id_os ) ";
file_put_contents("queryPRI.txt", $queryPRI);
    $direna = tep_db_query($queryPRI);
    $direna = tep_db_fetch_array( $direna );



/* datos cliente*/
$queryOsina="select  S.os_cotizaciones_cruzadas, CL.clie_telefonocasa, CL.clie_telcontacto1, CL.clie_telcontacto2, clie_tipo,clie_razonsocial, S.os_fechaestimacion ,S.clie_rut, S.id_os,S.id_estado,S.id_local,S.os_descripcion,S.os_comentarios,S.id_proyecto,S.os_fechacotizacion, E.esta_nombre ,L.nom_local, P.proy_nombre,CL.clie_nombre,CL.clie_tipo,CL.clie_paterno,CL.clie_materno from os S
inner join estados E on (E.id_estado=S.id_estado)
inner join locales L on (L.id_local=S.id_local)
inner join proyectos P on (S.id_proyecto=P.id_proyecto)
inner join clientes CL on (S.clie_rut=CL.clie_rut)
inner join direcciones D on (D.id_direccion=S.id_direccion)
where S.id_os= $id_os ";

    $OsSa = tep_db_query($queryOsina);
    $OsSa = tep_db_fetch_array( $OsSa );


/* cuando llega la pagina y no se ha modificado nada solo trae el id del la os*/

if ($accion=='Onchange'){


$queryDire= "SELECT  CONCAT(NE.DESCRIPTION, ' - ', LO.DESCRIPTION) AS comu_nombre_des,  DE.DESCRIPTION AS nombre_departamento, CI.DESCRIPTION AS nombre_ciudad, D.id_direccion, D.dire_nombre AS dire_nombre2, D.dire_direccion AS dire_direccion_des, D.dire_defecto , D.dire_telefono AS dire_telefono_des ,D.dire_observacion AS dire_observacion_des
FROM direcciones D
LEFT JOIN cu_department DE ON DE.ID = D.id_departamento
AND DE.ID = ( SELECT id_departamento FROM direcciones WHERE id_direccion = $id_direccion )
LEFT JOIN cu_city CI ON CI.ID = D.id_ciudad AND CI.ID = ( SELECT id_ciudad FROM direcciones WHERE id_direccion = $id_direccion )
AND CI.ID_DEPARTMENT = ( SELECT id_departamento FROM direcciones WHERE id_direccion = $id_direccion )
AND CI.ID_PROVINCE = ( SELECT id_provincia FROM direcciones WHERE id_direccion = $id_direccion )
LEFT JOIN cu_locality LO ON LO.ID = D.id_localidad
AND LO.ID = ( SELECT id_localidad FROM direcciones WHERE id_direccion = $id_direccion )
AND LO.ID_CITY = ( SELECT id_ciudad FROM direcciones WHERE id_direccion = $id_direccion )
AND LO.ID_PROVINCE = ( SELECT id_provincia FROM direcciones WHERE id_direccion = $id_direccion )
AND LO.ID_DEPARTMENT = ( SELECT id_departamento FROM direcciones WHERE id_direccion = $id_direccion )
LEFT JOIN cu_neighborhood NE ON NE.LOCATION = D.dire_localizacion
AND NE.ID_CITY = ( SELECT id_ciudad FROM direcciones WHERE id_direccion = $id_direccion )
AND NE.ID_PROVINCE = ( SELECT id_provincia FROM direcciones WHERE id_direccion = $id_direccion )
AND NE.ID_DEPARTMENT = ( SELECT id_departamento FROM direcciones WHERE id_direccion = $id_direccion )
AND NE.LOCATION = ( SELECT dire_localizacion FROM direcciones WHERE id_direccion = $id_direccion )
WHERE  D.id_direccion = $id_direccion   ";


	$dire_des=$id_direccion;
    $Qdirep= tep_db_query($queryDire);
    $Qdirep = tep_db_fetch_array( $Qdirep );

/* se hace el UPDATE con la direccion que el cliente eligio en el onchange*/
        $qupdi="UPDATE os SET id_direccion=".($id_direccion+0)." where id_os=".($id_os+0)." ";
        tep_db_query($qupdi);
        /* si elijo alguna direccion cambia la direccion de despacho*/

/* header ('Location: nueva_cotizacion_04.php?id_os='.($id_os+0));
 tep_exit();*/
}


/*direccion primaria*/
$queryDRI = "SELECT NE.DESCRIPTION AS comu_nombre_pri, CL.clie_rut, D.id_direccion, D.dire_nombre, D.dire_direccion as dire_direccion_pri,D.id_direccion ,D.dire_defecto ,D.dire_telefono as dire_telefono_pri,D.dire_observacion as dire_observacion_pri
			FROM clientes CL
			INNER JOIN direcciones D on (D.clie_rut = CL.clie_rut)  AND D.dire_defecto='p'
			LEFT JOIN cu_neighborhood NE ON NE.LOCATION = D.dire_localizacion
			AND NE.LOCATION = ( SELECT clie_localizacion FROM clientes WHERE clie_rut = ( SELECT clie_rut FROM os WHERE id_os = $id_os) )
			WHERE CL.clie_rut = ( SELECT clie_rut FROM os WHERE id_os = $id_os) ";

    $direPRi = tep_db_query($queryDRI);
    $direPRi = tep_db_fetch_array( $direPRi );


if ($accion=="datos_cliente"){
 header ('Location: nueva_cotizacion_01.php?clie_rut='.($OsSa['clie_rut']+0).'&id_os='.($id_os+0));
 tep_exit();
}



/**********************************/

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
//$MiTemplate->set_var("TEXT",ver_contenido_editable( 1 ));




/* GUARDAR FECHAS POR TIPO DE ENTREGA */
	if ($accion=="GuardarFechas")
	{

		if($fecha_entrega_dp != '')
		{
		$consul_dp = "UPDATE os_detalle  SET  osde_fecha_entrega = '$fecha_entrega_dp'  WHERE id_tipodespacho = 1 AND id_os = $id_os AND osde_tipoprod <> 'PE' AND osde_tipoprod <> 'SV' ";
		$resul_dp= tep_db_query($consul_dp);
		}

		if($fecha_entrega_ex != '')
		{
		$consul_dp = "UPDATE os_detalle  SET  osde_fecha_entrega = '$fecha_entrega_ex'  WHERE id_tipodespacho = 2 AND id_os = $id_os AND osde_tipoprod <> 'PE' AND osde_tipoprod <> 'SV' ";
		$resul_dp= tep_db_query($consul_dp);
		}

		if($fecha_entrega_rc != '')
		{
		$consul_dp = "UPDATE os_detalle  SET  osde_fecha_entrega = '$fecha_entrega_rc'  WHERE id_tipodespacho = 3 AND id_os = $id_os AND osde_tipoprod <> 'PE' AND osde_tipoprod <> 'SV' ";
		$resul_dp= tep_db_query($consul_dp);
		}

	}


    /*COLOCAR CAMPOS DE FECHAS OCULTOS O VISIBLES */

	$visible_dp = 'hidden';
	$visible_ex = 'hidden';
	$visible_rc = 'hidden';
	$visible_boton = 'hidden';

	$consul_tipo = "SELECT id_tipodespacho, osde_fecha_entrega  FROM os_detalle  WHERE  id_os = $id_os AND osde_tipoprod <> 'PE' AND osde_tipoprod <> 'SV' ";
	$resul_tipo = tep_db_query($consul_tipo);

	while( $resul = tep_db_fetch_array($resul_tipo) )
	{
		if($resul['id_tipodespacho'] == 1 && $resul['osde_fecha_entrega'] == '')
		$visible_dp = 'visible';

		if($resul['id_tipodespacho'] == 2 && $resul['osde_fecha_entrega'] == '')
		$visible_ex = 'visible';

		if($resul['id_tipodespacho'] == 3 && $resul['osde_fecha_entrega'] == '')
		$visible_rc = 'visible';
	}

	if($visible_dp == 'visible' || $visible_ex == 'visible' || $visible_rc == 'visible')
	$visible_boton = 'visible';

	$MiTemplate->set_var('visible_boton', $visible_boton);
	$MiTemplate->set_var('visible_dp', $visible_dp);
	$MiTemplate->set_var('visible_ex', $visible_ex);
	$MiTemplate->set_var('visible_rc', $visible_rc);

	/* ################################################### */




	 if ($OsSa['clie_tipo']=='e'){
		 $MiTemplate->set_var("empresa","Empresa");
		 $MiTemplate->set_var("clie_razonsocial",$OsSa['clie_razonsocial']);
	 }
	 $MiTemplate->set_var("os_fechaexpiracion",fecha_db2php($OsSa['os_fechaestimacion']));
     $MiTemplate->set_var("clie_telcontacto1",$OsSa['clie_telefonocasa']);

     $MiTemplate->set_var("clie_telcontacto2", $OsSa['clie_telcontacto2']);

     $MiTemplate->set_var("clie_rut",$OsSa['clie_rut']);
     $MiTemplate->set_var("id_proyecto",$OsSa['id_proyecto']);
     $MiTemplate->set_var("esta_nombre",$OsSa['esta_nombre']);
     $MiTemplate->set_var("nom_local",$OsSa['nom_local']);
     $MiTemplate->set_var("os_cotizaciones_cruzadas",$OsSa['os_cotizaciones_cruzadas']);
     $MiTemplate->set_var("os_comentarios",$OsSa['os_comentarios']);
     $MiTemplate->set_var("os_fechacotizacion",fecha_db2php($OsSa['os_fechacotizacion']));
     $MiTemplate->set_var("proy_nombre",$OsSa['proy_nombre']);
     $MiTemplate->set_var("clie_nombre",$OsSa['clie_nombre']);
     $MiTemplate->set_var("clie_tipo",$OsSa['clie_tipo']);
     $MiTemplate->set_var("clie_paterno",$OsSa['clie_paterno']);
     $MiTemplate->set_var("clie_materno",$OsSa['clie_materno']);
     $MiTemplate->set_var("dire_telefono_pri",$direPRi['dire_telefono_pri']);
     $MiTemplate->set_var("dire_direccion_pri",$direPRi['dire_direccion_pri']);
     $MiTemplate->set_var("comu_nombre_pri",$direPRi['comu_nombre_pri']);
     $MiTemplate->set_var("dire_observacion",$direPRi['dire_observacion']);

     	$MiTemplate->set_var("dire_nombre2",$direna['dire_nombre2']);
		$MiTemplate->set_var("departamento",$direna['nombre_departamento']);
     	$MiTemplate->set_var("ciudad", $direna['nombre_ciudad']);

     $MiTemplate->set_var("dire_telefono_des",$direna['dire_telefono_des']);
     $MiTemplate->set_var("dire_direccion_des",$direna['dire_direccion_des']);
     $MiTemplate->set_var("comu_nombre_des",$direna['comu_nombre_des']);
     $MiTemplate->set_var("dire_observacion_des",$direna['dire_observacion_des']);



	if($accion=='Onchange')
	{
		$MiTemplate->set_var("dire_telefono_des",$Qdirep['dire_telefono_des']);
        $MiTemplate->set_var("dire_direccion_des",$Qdirep['dire_direccion_des']);
        $MiTemplate->set_var("dire_nombre2",$Qdirep['dire_nombre2']);
 			$MiTemplate->set_var("departamento",$Qdirep['nombre_departamento']);
      		$MiTemplate->set_var("ciudad", $Qdirep['nombre_ciudad']);
        $MiTemplate->set_var("comu_nombre_des",$Qdirep['comu_nombre_des']);
        $MiTemplate->set_var("dire_observacion_des",$Qdirep['dire_observacion_des']);
	}


if($visible['id_os'] != '')
{
$MiTemplate->set_var("visible", 'visible');
$MiTemplate->set_var("visible2", 'hidden');
}
else{
$MiTemplate->set_var("visible", 'hidden');
}

$MiTemplate->set_var("TEXT_CAMPO_1",TEXT_CAMPO_1);
$MiTemplate->set_var("TEXT_CAMPO_2",TEXT_CAMPO_2);
$MiTemplate->set_var("TEXT_CAMPO_3",TEXT_CAMPO_3);
$MiTemplate->set_var("TEXT_CAMPO_4",TEXT_CAMPO_4);
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
$MiTemplate->set_var("TEXT_CAMPO_17",TEXT_CAMPO_17);
$MiTemplate->set_var("TEXT_CAMPO_18",TEXT_CAMPO_18);
$MiTemplate->set_var("TEXT_CAMPO_19",TEXT_CAMPO_19);
$MiTemplate->set_var("TEXT_CAMPO_21",TEXT_CAMPO_21);
$MiTemplate->set_var("TEXT_CAMPO_22",TEXT_CAMPO_22);
$MiTemplate->set_var("TEXT_CAMPO_23",TEXT_CAMPO_23);
$MiTemplate->set_var("TEXT_CAMPO_24",TEXT_CAMPO_24);
$MiTemplate->set_var("TEXT_CAMPO_25",TEXT_CAMPO_25);

// Agregamos el header
$MiTemplate->set_file("header","header_ident.html");

// Agregamos el main
$MiTemplate->set_file("main","nueva_cotizacion/nueva_cotizacion_04.htm");


/* para el nombre del que atendio*/
$qnombre="select U.USR_NOMBRES, U.USR_APELLIDOS from usuarios U inner join os OS on (OS.usr_id=U.usr_id) where id_os=".($id_os+0)."";
$osAtendido = tep_db_query($qnombre);
$osAtendido = tep_db_fetch_array( $osAtendido );

$MiTemplate->set_var('atendido', $osAtendido['USR_NOMBRES'] . " " . $osAtendido['USR_APELLIDOS']);

$MiTemplate->set_block("main", "Direcciones", "BLO_Dir");

$queryR = "SELECT OS.clie_rut, D.id_direccion, D.dire_nombre ,D.dire_direccion as dire_direcciond,D.id_direccion ,D.dire_defecto ,NE.DESCRIPTION as comu_nombred ,D.dire_telefono,D.dire_observacion ,if (D.id_direccion=OS.id_direccion,'selected','') selected
from os OS inner join direcciones D on (D.clie_rut = OS.clie_rut)
LEFT JOIN cu_neighborhood  NE  ON NE.LOCATION = D.dire_localizacion
where OS.id_os = $id_os ";

//$queryR = "select OS.id_direccion,OS.clie_rut,D.id_direccion,D.dire_nombre ,D.dire_direccion as dire_direcciond,D.id_direccion ,D.dire_defecto ,C.comu_nombre as comu_nombred ,D.dire_telefono,D.dire_observacion ,if (D.id_direccion=OS.id_direccion,'selected','') selected from os OS inner join direcciones D on (D.clie_rut = OS.clie_rut) inner join comuna C on (C.id_comuna= D.id_comuna ) where OS.id_os=".($id_os+0);

query_to_set_var( $queryR, $MiTemplate, 1, 'BLO_Dir', 'Direcciones');

/* para los productos*/
$MiTemplate->set_file("productos","nueva_cotizacion/os_detalle.htm");
/* para los productos*/
$instn="No";
$insts="S�";
$precioVacio=" - ";
$totalVacio=" 0 ";
$especificacion="<br> <u>Especificaciones</u>:&nbsp;";
$cuentaSVDE=0;
$cuentaPSoPEConDesp=0;
$descuento=0;
     $MiTemplate->set_block("main", "Precio_Total", "PBLModulos");
     
/* Mantis 20530 - CPE - CO - Nueva columna de datos Grupo/Categoria en articulo cotizado.
       Cristian Fernandez | Baufest | 04-07-2016
       Descripcion: Se agrega nueva columna Grupo/Categoria
*/    
     
/* precios y total parciado*/
    $query_OD="	select (P.peso/1000) AS peso, OD.osde_fecha_entrega, OD.osde_descuento, OD.osde_especificacion, (OD.cod_sap+0)  as cod_sap,OD.cod_barra,OD.osde_tipoprod,OD.osde_subtipoprod,OD.osde_descripcion,OD.osde_cantidad,OD.id_tipodespacho,OD.osde_instalacion,OD.osde_precio,OD.osde_descuento,TD.nombre,
					if(osde_descuento is not null, osde_descuento, 0) 'osde_descuento',
					if (osde_descuento<>0,ROUND((osde_precio-osde_descuento)*osde_cantidad),ROUND(osde_precio*osde_cantidad))'Total',
					if(((osde_precio-osde_descuento)*osde_cantidad) is Null,'$totalVacio','') 'totalVacio',
					if (OD.osde_tipoprod='SV', ' - ',  if (OD.osde_instalacion, 'SI', 'NO')) 'osde_instalacion',
					if(OD.osde_precio is null, '$precioVacio', '') 'precioVacio' ,
					if(OD.osde_descuento is null, '$descuento', '') 'descuento',
					if(OD.osde_especificacion is not null, '$especificacion', '') 'especificacion', P.id_catprod
				from os_detalle OD inner join tipos_despacho TD on (TD.id_tipodespacho=OD.id_tipodespacho)
				INNER JOIN productos P ON  OD.id_producto = P.id_producto
				where id_os=".($id_os+0)." order by OD.id_os_detalle desc ";

    if ( $rq = tep_db_query($query_OD) ){
        while( $res = tep_db_fetch_array( $rq ) ) {
            if (($res['osde_tipoprod']=='PS' || $res['osde_tipoprod']=='PE') && ($res['nombre']=='D. Programado' || $res['nombre']=='Prov.Des Domicilio' || $res['nombre']=='Express'))
				$cuentaPSoPEConDesp+=1;

            if ($res['osde_tipoprod']=='SV' && $res['osde_subtipoprod']=='DE')
                $cuentaSVDE+=1;

            if ($res['osde_precio']==''){
                $MiTemplate->set_var('precioVacio', $precioVacio);
                $MiTemplate->set_var('osde_precio', '');
            }else{
                $MiTemplate->set_var('osde_precio', formato_precio($res['osde_precio']));
                $MiTemplate->set_var('precioVacio', '');
            }
            if ($res['Total']==''){
                $MiTemplate->set_var('totalVacio', $totalVacio);
                $MiTemplate->set_var('Total', '');
            }else{
                $MiTemplate->set_var('totalVacio', '');
                $MiTemplate->set_var('Total', formato_precio($res['Total']));
            }

			if ($res['osde_tipoprod']!='SV'){
				   $MiTemplate->set_var('tipo_nombre', ($res['osde_tipoprod']!='SV')?$res['nombre']:'-');
			}else{
				  $MiTemplate->set_var('tipo_nombre', '-');}

        	if ($res['peso'] == ''){
            	$MiTemplate->set_var('peso', '-');
            }
            else{
            	$MiTemplate->set_var('peso', round($res['peso'],DECIMALES_PESO));
            }

			/*$MiTemplate->set_var('tipo_nombre', $res['nombre']);*/
			$MiTemplate->set_var('osde_instalacion', $res['osde_instalacion']);
			$MiTemplate->set_var('cod_barra', $res['cod_barra']);
			$MiTemplate->set_var('cod_sap', $res['cod_sap']);
			$MiTemplate->set_var('osde_tipoprod', $res['osde_tipoprod']);
			$MiTemplate->set_var('osde_fecha_entrega', $res['osde_fecha_entrega']);
			$MiTemplate->set_var('osde_descripcion', $res['osde_descripcion']);
			$MiTemplate->set_var('especificacion', $res['especificacion']);
			$MiTemplate->set_var('osde_especificacion', $res['osde_especificacion']);
			$MiTemplate->set_var('osde_cantidad', $res['osde_cantidad']);
			$MiTemplate->parse("PBLModulos", "Precio_Total", true);
                        $MiTemplate->set_var('catprod', $res['id_catprod']); 
       }
    }

    /*para obtener el total*/
      $query_TG = "select osde_tipoprod,osde_precio,osde_cantidad, if(osde_descuento is not null, osde_descuento, 0) 'osde_descuento' ,if (osde_descuento<>0,ROUND((osde_precio-osde_descuento)*osde_cantidad),ROUND(osde_precio*osde_cantidad))'Total' from os_detalle where id_os=".($id_os+0)." ";

      $suma=0;
      $boton='si';
        if ( $rq = tep_db_query($query_TG) ){
            while( $res = tep_db_fetch_array( $rq ) ) {
                if(!$res['Total'])
                    $boton='no';
            $suma+=$res['Total'];
            }
        }
       $MiTemplate->set_var('TG', formato_precio($suma));

//Si existen productos con despacho y no existe al menos un despacho, error.
if ($cuentaPSoPEConDesp && !$cuentaSVDE)
	$notienedespacho = 1;
else
	$notienedespacho = 0;

	if($liquido_flete == 1)
	$notienedespacho = 0;

if (($boton=='si')&&($notienedespacho==0)) {
    $MiTemplate->set_var('finalizar', '<a onClick="return validar_fechas();" href="nueva_cotizacion_04.php?id_os='.($id_os+0).'&accion=fin">'); }

/*if (($boton=='si')&&($notienedespacho!=0)) {
    $MiTemplate->set_var('finalizar', '<a href="nueva_cotizacion_04.php?id_os='.($id_os+0).'&accion=Des">');}*/

if ($boton=='no'){
    $MiTemplate->set_var('finalizar', '<a onClick="return validar_fechas();" href="nueva_cotizacion_04.php?id_os='.($id_os+0).'&accion=Nofin">');    }

if ($notienedespacho!=0){
    $MiTemplate->set_var('finalizar', '<a onClick="return validar_fechas();" href="nueva_cotizacion_04.php?id_os='.($id_os+0).'&accion=Des">');    }

// Agregamos el footer
include "../../includes/footer_cproy.php";

$MiTemplate->pparse("OUT_H", array("header"), true);
include "../../menu/menu.php";
$MiTemplate->parse("OUT_M", array("main","footer"), true);
$MiTemplate->p("OUT_M");

include "../../includes/application_bottom.php";




/* Liquidaci�n de Fletes */
if ($accion=="liquidacion"){

	echo "<SCRIPT LANGUAGE='JavaScript'>";
	echo "document.nueva_cotizacion_04.Button.disabled = true;";
	echo "document.nueva_cotizacion_04_dire.BotonEd.disabled = true;";
	echo "document.nueva_cotizacion_04_dire.select_direcciones.disabled = true;";
	echo "document.nueva_cotizacion_04_dire.ajustar.disabled = false;";
	echo "bt=document.getElementById('siguiente');";
	echo "bt.style.visibility='visible';";
	echo "</SCRIPT>";

}



?>