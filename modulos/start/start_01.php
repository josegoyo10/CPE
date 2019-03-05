<?
$SIN_PER = 1;
include_once('../../includes/aplication_top.php');
require_once('../../wsClientUnique/ClientUnique.php');

include_idioma_mod( $ses_idioma, "start" );

$MiTemplate = new Template();
// asignamos degug maximo
$MiTemplate->debug = 0;
// root directory de los templates
$MiTemplate->set_root(DIRTEMPLATES);
// variables perdidas
$MiTemplate->set_unknowns("remove");

$MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
$MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);
$MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));
//$MiTemplate->set_var("TEXT",ver_contenido_editable( 1 ));

// Busca los Clientes con inconsistencia en los Nombres.
try{
	$queryCl =  "SELECT * FROM clientes
				 WHERE ( ((TRIM(clie_nombre))='') OR ((TRIM(clie_nombre)) is NULL) ) 
				 AND ( ((TRIM(clie_paterno)='') OR (TRIM(clie_paterno) is NULL)) AND ((TRIM(clie_materno)='') OR (TRIM(clie_materno) is NULL)) )";
	$result = tep_db_query($queryCl);
	while( $row = tep_db_fetch_array( $result ) ) {
		$clie_rut = $row['clie_rut'];

		$response = ClientUnique::searchById($clie_rut);
			if($response){
				$dirServ = $response['Location'];
				$Location = getlocalizacion($dirServ);

				$queryUpC = "UPDATE clientes SET clie_nombre='".$response['FirstName']."' , 
											clie_paterno='".$response['Surname1']."', 
											clie_materno='".$response['Surname2']."', 
											clie_telefonocasa='".$response['Phone']."', 
											clie_telcontacto2='".$response['Phone2']."', 
											clie_email='".$response['Email']."', 
											clie_localizacion='".$response['Location']."', 
											clie_departamento='".$Location['departamento']."', 
											clie_provincia='".$Location['provincia']."', 
											clie_ciudad='".$Location['ciudad']."', 
											clie_localidad='".$Location['localidad']."', 
											clie_barrio='".$Location['barrio']."' 
							WHERE clie_rut='".$clie_rut."'";
				tep_db_query($queryUpC);	
				} 
	}
}
catch (ErrorException $E){
	writelog("No se corrigieron los Datos de Clientes con inconsistencias");
}  

// Agregamos el header
$MiTemplate->set_file("header","header_ident.html");

// Agregamos el main
$MiTemplate->set_file("main","start/home.htm");

// Agregamos el footer
include "../../includes/footer_cproy.php";

// Agregamos el footer
$MiTemplate->set_file("footer","footer_ident.html");

$MiTemplate->pparse("OUT_H", array("header"), true);
include "../../menu/menu.php";
$MiTemplate->parse("OUT_M", array("main","footer"), true);
$MiTemplate->p("OUT_M");

include "../../includes/application_bottom.php";
?>