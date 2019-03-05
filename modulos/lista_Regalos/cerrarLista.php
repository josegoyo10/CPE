<?php
session_start();
$SIN_PER = 1;
include_once('../../includes/aplication_top.php');
require('../../wsLiqFletes/CalculoFlete/Fletes.php');

$local_id = get_local_usr( $ses_usr_id );
$USR_LOGIN = get_login_usr( $ses_usr_id );
$idLista = $_GET['idLista'];
$valLocal = '';
$valido = '1';

$validaEst = "SELECT DISTINCT LR.id_Local, LD.OS_idOT, LO.ot_idEstado
				FROM list_os_det LD
				LEFT JOIN list_os_enc LE ON LE.idLista_OS_enc = LD.idLista_OS_enc
				LEFT JOIN list_ot LO ON LO.ot_idList =LD.OS_idOT
                LEFT JOIN list_regalos_enc LR ON LR.idLista=LE.idLista_enc
				WHERE LE.idLista_enc=".$idLista." AND LD.OS_idOT IS NOT NULL
				GROUP BY LD.OS_idOT";

	$res = tep_db_query($validaEst);     
	for ($i=0; $i < $result=tep_db_fetch_array($res); $i++){
		$valLocal = $result['id_Local'];
		$ot_idList=$result['OS_idOT'].','.$ot_idList;
		if($result['ot_idEstado'] != 'PD'){
			$valido = '0';
		}
		$cont++;
	}

	$ot_idList = substr ($ot_idList, 0, strlen($ot_idList) - 1); 
	
	if($valLocal != ''){
		if($valLocal == $local_id){
			if($valido == 1){
				if($cont != 0){
					$zona = Zona_Despacho($idLista);
					
					if($zona){
						header("Location: despachoLista.php?zona=".$zona."&idLista=".$idLista."&ot_idList=".$ot_idList."");
					}
				}	
				else{
					$msg = "¡¡ La lista de regalos no posee OT’s asociadas o estas no se encuentran en un estado válido para despachar !!";
					alert($msg);
					?>
					<SCRIPT LANGUAGE="JavaScript">
						location.href='../lista_Regalos/nueva_lista_sumario_01.php?idLista=<? echo $idLista; ?>';
					</SCRIPT>
					<?
				}	
			}
			else{
					$msg = "¡¡ La lista de regalos no posee OT’s asociadas o estas no se encuentran en un estado válido para despachar !!";
					alert($msg);
					?>
					<SCRIPT LANGUAGE="JavaScript">
						location.href='../lista_Regalos/nueva_lista_sumario_01.php?idLista=<? echo $idLista; ?>';
					</SCRIPT>
					<?
			}
		}
		else{
			$msg = "¡¡ No se puede cerrar esta lista, el local no corresponde al origen de la Lista de Regalos !!";
			alert($msg);
			?>
				<SCRIPT LANGUAGE="JavaScript">
					location.href='../lista_Regalos/nueva_lista_sumario_01.php?idLista=<? echo $idLista; ?>';
				</SCRIPT>
			<?
			}
	}
	else{
		$msg = "¡¡ No se puede cerrar esta lista, no tiene OS asociadas !!";
		alert($msg);
			?>
				<SCRIPT LANGUAGE="JavaScript">
					location.href='../lista_Regalos/nueva_lista_sumario_01.php?idLista=<? echo $idLista; ?>';
				</SCRIPT>
			<?
	}

function Zona_Despacho($idLista){
	$QryDes = "SELECT DR.dire_direccion, DR.dire_localizacion, LC.cod_local_pos, LC.id_localizacion
				FROM list_regalos_enc LE 
				JOIN direcciones DR ON DR.id_direccion=LE.id_Direccion 
				JOIN locales LC ON LC.id_local=LE.id_Local
				WHERE idLista = $idLista";
	$res = tep_db_query($QryDes);        
	$res = tep_db_fetch_array($res);
	
	$clie_localizacion = getStringlocalizacion($res['dire_localizacion']);
	$local_localizacion = getStringlocalizacion($res['id_localizacion']);
	
	$xml="<despacho>
		  	<direccion>" . $res['dire_direccion'] . "</direccion>
		  	<idDepartamento>" .$clie_localizacion['departamento']. "</idDepartamento> 
		  	<idMunicipio>" .$clie_localizacion['provincia']. "</idMunicipio> 
		  	<idCentroPoblado>" .$clie_localizacion['ciudad']. "</idCentroPoblado> 
		  	<idLocalidad>" .$clie_localizacion['localidad']. "</idLocalidad> 
		  	<idBarrio>" .$clie_localizacion['barrio']. "</idBarrio> 
		  </despacho>
		    <centroSuministro>
		      <idLocal>" . $res['cod_local_pos'] . "</idLocal> 
		  	 	<idDepartamento>" .$local_localizacion['departamento']. "</idDepartamento> 
		  	 	<idMunicipio>" .$local_localizacion['provincia']. "</idMunicipio> 
		       	<idCentroPoblado>" .$local_localizacion['ciudad']. "</idCentroPoblado> 
		  	 	<idLocalidad>" .$local_localizacion['localidad']. "</idLocalidad> 
		  	 	<idBarrio>" .$local_localizacion['barrio']. "</idBarrio> 
		   </centroSuministro>
		     <entregaProductos>
		  		<lstTipoDespacho>
		  			<codigoTipo>1</codigoTipo> 
		  			<peso>1</peso> 
		  		</lstTipoDespacho>
		     </entregaProductos>
		  <codEmpresaTransportadora>0</codEmpresaTransportadora>";	
 
	$service = new Fletes();
	$response = $service->calcular($xml);

		if ($response) {			
			$zona = $response[data][zone];
			if($zona == ''){
				$zona = 'ZONA 1';
			}	
		}
		else {
			echo "<SCRIPT LANGUAGE='JavaScript'>";
				echo "alert('Se Produjo un Error en WS \n Se liquidara ZONA 1 por defecto.');";
			echo "</SCRIPT>";		
		}

	return $zona;
}

?>
