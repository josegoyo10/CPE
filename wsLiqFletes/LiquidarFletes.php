<?php
require_once('../../includes/aplication_top.php');
require_once('../../CalculoFlete/Fletes.php');

function Liquidar($idViaje){
	
		$queryDET = "SELECT D.idDespacho, D.CDireccion, D.id_localizacion AS despacho_localizacion, D.idTienda, TI.id_localizacion AS tienda_localizacion,  T.idEmp_tran, sum(DD.peso) AS tot_peso,
           			 IF(D.idTipoDespacho = 2, '2', '1') AS idTipoDespacho
					 FROM DespachoEnc D
					 JOIN Viaje V ON V.idViaje = D.idViaje
					 JOIN Transportista T ON T.Trut = V.Trut
					 JOIN Tienda TI ON TI.idTienda = D.idTienda
					 JOIN DespachoDet DD ON DD.idDespacho = D.idDespacho
					 where V.idViaje=$idViaje+0 group by DD.idDespacho";
		$rq = tep_db_query($queryDET);
		while ( $res = tep_db_fetch_array( $rq )){	
			
			$des_localizacion = $res['despacho_localizacion'];
			((strlen($des_localizacion)<14)?$des_localizacioncli='0'.$des_localizacion: $des_localizacioncli=$des_localizacion);
			$des_localizaciondepto=substr($des_localizacioncli, 0, -12);
			$des_localizacionprovin=substr($des_localizacioncli, 2, -9);
			$des_localizacionciudad=substr($des_localizacioncli, 5, -6);
			$des_localizacionlocalidad=substr($des_localizacioncli, 8, -3);
			$des_localizacionbarrio=substr($des_localizacioncli, 11);
			
			$tie_localizacion = $res['tienda_localizacion'];
			((strlen($tie_localizacion)<14)?$tie_localizacioncli='0'.$tie_localizacion: $tie_localizacioncli=$tie_localizacion);
			$tie_localizaciondepto=substr($tie_localizacioncli, 0, -12);
			$tie_localizacionprovin=substr($tie_localizacioncli, 2, -9);
			$tie_localizacionciudad=substr($tie_localizacioncli, 5, -6);
			$tie_localizacionlocalidad=substr($tie_localizacioncli, 8, -3);
			$tie_localizacionbarrio=substr($tie_localizacioncli, 11);

			$xml="<despacho>
			  		<direccion>".$res['CDireccion']."</direccion>
			  		<idDepartamento>".$des_localizaciondepto."</idDepartamento> 
			  		<idMunicipio>".$des_localizacionprovin."</idMunicipio> 
			  		<idCentroPoblado>$des_localizacionciudad</idCentroPoblado> 
			  		<idLocalidad>".$des_localizacionlocalidad."</idLocalidad> 
			  		<idBarrio>".$des_localizacionbarrio."</idBarrio> 
			  	  </despacho>
			  	  <centroSuministro>
			         <idLocal>".$res['idTienda']."</idLocal> 
			  		 <idDepartamento>".$tie_localizaciondepto."</idDepartamento> 
			  		 <idMunicipio>".$tie_localizacionprovin."</idMunicipio> 
			  	     <idCentroPoblado>$tie_localizacionciudad</idCentroPoblado> 
			  		 <idLocalidad>".$tie_localizacionlocalidad."</idLocalidad> 
			  		 <idBarrio>".$tie_localizacionbarrio."</idBarrio> 
			  	   </centroSuministro>
			  	   <entregaProductos>
			  		<lstTipoDespacho>
			  			<codigoTipo>".$res['idTipoDespacho']."</codigoTipo> 
			  			<peso>".$res['tot_peso']."</peso> 
			  		</lstTipoDespacho>
			  	   </entregaProductos>
			  <codEmpresaTransportadora>".$res['idEmp_tran']."</codEmpresaTransportadora>";
			$service = new Fletes();
			$response = $service->calcular($xml);
			if ($response) {
				//print_r ($response);
				$costoViaje = $costoViaje+$response['data']['lstValorFlete']['valorFlete'];		
				if($costoViaje == '0'){
					echo'<SCRIPT LANGUAGE="JavaScript">
							alert("No se encontro una Zona Asociada, para liquidar Fletes.\n No se Liquidaron Fletes para el Viaje N°'.$idViaje.'. ");
					  	</SCRIPT>';		
				}
			}
			else {
				echo'<SCRIPT LANGUAGE="JavaScript">
						alert("Se genero un error en el Web Service.\n No se Liquidaron Fletes para el Viaje N°'.$idViaje.' ");
					  </SCRIPT>';
				//print "Error.";
			}
		}
		$queryUPD= "UPDATE Viaje SET costoviaje = $costoViaje WHERE idViaje = $idViaje ";

        tep_db_query($queryUPD);
}
?>