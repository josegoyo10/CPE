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

/**********************************************************************************************/


/****************************************************************/

function leearchivo(){
	writeevent('                                                                                                             ');
	writeevent('                                                                                                             '); 
	writeevent('*************************************************************************************************************');
	writeevent('*************************************************************************************************************');
	writeevent('*************************************************************************************************************');
	writeevent('*************************************************************************************************************');
	writeevent('*************************************************************************************************************');
	writeevent('                                                                                                             ');
	writeevent('                                                                                                             ');
	writeevent('Incio de Proceso de recuperar OC desde Sap por Usuario Crontab a las  '.date( "h:i:s" , time() ));
	RecuperaOc();
	writeevent('termino de Proceso de recuperar OC desde Sap por Usuario crontab a las  '.date( "h:i:s" , time() ));
	writeevent('*************************************************************************************************************');
	writeevent('*************************************************************************************************************');
	writeevent('                                                                                                             ');
	writeevent('                                                                                                             ');
	}


function RecuperaOc() {
	/*para lectura*/
	$dire   = DIR_SAP_READ;
	while($archivosordenados=leedirectorio($dire)){
		$valor  = lectura();
		foreach ($valor as $key=>$value) { 
			$linea = $valor[$key];
			$datos  = recuperadatos($linea);
			if ($datos){
				$arre   = split('-', $datos);
				$ot		= (($arre[0]*1)+0);
				$oc		= (($arre[1]*1)+0);
				$rut	= (($arre[2]*1)+0);
				$nom	=	$arre[3];
				$sap	= (($arre[4]*1)+0);
				echo " OT  ->".(($arre[0]*1)+0)."<br>";
				echo " OC  ->".(($arre[1]*1)+0)."<br>";
				echo " RUT ->".(($arre[2]*1)+0)."<br>";
				echo " NOM ->".$arre[3]."<br>";
				echo " SAP ->".(($arre[4]*1)+0)."<br>";
				/* recupera el idproveedor*/
				$id_prov= verificaproveedor($rut);
				writeevent('(RecuperaOc) verificaproveedor'.$id_prov);
				if (!$id_prov){
					$id_prov=insertaproveedor($rut,$nom);
					writeevent('(RecuperaOc) insertaproveedor '.$id_prov);
				}
				if ($rutpv=revisapxp($id_prov,$sap)){
					writeevent("(RecuperaOc) revisapxp existe relacion");				
					cambiaestado($ot,$oc,$rutpv,$nom,$id_prov);		
				}else{
					if($producto=buscaidprod($sap)){
						writeevent('(RecuperaOc) buscaidprod id_producto '.$producto);
						if($ipxp =insertapxp($producto,$id_prov,$rut,$sap)){
							cambiaestado($ot,$oc,$rut,$nom,$id_prov);				
						}else{
							writeevent('(RecuperaOc) No se inserto la relacion');
							writeevent("Error (leer) No se pudo cambiar el estado de la ot  ".$ot);				
						}
					}
				}
			}
		}
	}
}





/**********************************************************************************************/
	leearchivo();

/**********************************************************************************************/


?>
