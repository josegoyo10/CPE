<?
define('DB_SERVER', 'localhost');
define('DB_SERVER_USERNAME', 'root');
define('DB_SERVER_PASSWORD', 'arcano');
define('DB_DATABASE', 'CPROYV3C');
define('USE_PCONNECT', 0);

include "/var/www/html/centroproy2/includes/funciones/database.php";
include "/var/www/html/centroproy2/includes/funciones/general.php";

// nos conectamos a la base de datos
tep_db_connect() or die('Unable to connect to database server!');

// set the application parameters (can be modified through the administration tool)
$configuration_query = tep_db_query('select var_llave as cfgKey, var_valor as cfgValue from glo_variables');
while ($configuration = tep_db_fetch_array($configuration_query)) {
    define($configuration['cfgKey'], $configuration['cfgValue']);
}

// iniciamos las variables de session
session_start();


/****************************************************************
 *
 * Despliega Listado Búsqueda
 *
 ****************************************************************/
function genera(){
writeevent('                                                                                                             ');
writeevent('                                                                                                             '); 
writeevent('*************************************************************************************************************');
writeevent('*************************************************************************************************************');
writeevent('*************************************************************************************************************');
writeevent('*************************************************************************************************************');
writeevent('*************************************************************************************************************');
writeevent('                                                                                                             ');
writeevent('                                                                                                             ');
writeevent('Incio de Proceso Genera Documentos para Sap por Usuario Crontab a las  '.date( "h:i:s" , time() ));
generadococsap();
writeevent('*************************************************************************************************************');
writeevent('*************************************************************************************************************');
writeevent('*************************************************************************************************************');
writeevent('*************************************************************************************************************');
writeevent('termino de Proceso Genera Documentos para Sap por Usuario crontab a las  '.date( "h:i:s" , time() ));
}


 /****************************************************************/
function generadococsap() {
	$queryos="SELECT distinct OD.id_os, OS.id_estado, OS.os_marcasap,OD.osde_tipoprod, OD.osde_subtipoprod
			FROM os OS
			join os_detalle OD on (OS.id_os=OD.id_os)
			join ot OT on (OT.ot_id=OD.ot_id)
			where OS.id_estado ='SP' and OD.osde_tipoprod='PE' and OD.osde_subtipoprod='CA' and OT.id_estado='EC'
			order by 1";
	$rq_1 = tep_db_query($queryos);
$m=0;
	while( $res = tep_db_fetch_array( $rq_1 ) ) {
		$numos=$res['id_os'];
		$os=generaocos($numos);
		if ($os){
			if (!$texto	= generatexto($numos)){
				writeevent('Error (generadococsap)-> No se pudo generar texto para OS '.$numos);			
			}else{
			/*genera nombre del archivo*/	
				if (!$nombre	= generanombrearchivo()){
					writeevent('Error (generadococsap)-> No se pudo generar el nombre de archivo '.$numos);			
				}else{

					$largo=10;
					$primero=CompletaCerosI($numos, $largo);
					if ($archivo= $primero.$nombre){
						$archivocreado  = escritura($texto,$archivo);
					}			
				}
			}
			if ($archivocreado){
			$m++;
				if (marcaos($numos)){
					writeevent('(generadococsap) -> La OS '.$numos. ' genero archivo sap y fue marcada en el sistema como enviada');
				}else{
					writeevent('Error (generadococsap)-> La OS '.$numos. ' no pudo ser marcada como procesada, el estado no correspondia');
				}
			}else{
				writeevent('Error (generadococsap)-> La OS '.$numos. ' no pudo generar archivo SAP,la Os no cumple con los requisitos de estado');
			}
		}else{
			//writeevent('Error (generadococsap)-> La OS '.$numos. ' no puede generar archivo sap');
		 
		}
	}
	writeevent('El Proceso Genero '.$m .' archivos para SAP  ');
}


/**********************************************************************************************/
     genera();

/**********************************************************************************************/


?>
