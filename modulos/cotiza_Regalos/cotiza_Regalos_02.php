<?
$pag_ini = '../lista_Regalos/nueva_lista_00.php';
include "../../includes/aplication_top.php";

$local_id = get_local_usr( $ses_usr_id );
$USR_LOGIN = get_login_usr( $ses_usr_id );
include_idioma_mod( $ses_idioma, "cotiza_Regalos_02" );

// *************************************************************************************

// *************** FUNCIONES DE REGISTRO EN BD ****************** //

function UpdateLista($input_cantidad, $input_cregalar, $Value, $idLista, $invitado){
	//Esta acción debe ser transaccional
	tep_db_query("SET AUTOCOMMIT=0");
	$success = true;

	$QryUpd = "UPDATE list_regalos_det SET list_cantprod='".$input_cantidad."', list_Cantcomp = (list_Cantcomp + '".$input_cregalar."'), invitado = '".$invitado."' WHERE idLista_enc='".$idLista."' AND idLista_det='".$Value."';";	
	$success = $success && tep_db_query($QryUpd);
	
	//Fin de la transacción
	if ($success){
		tep_db_query("commit");
		return true;
	}
	else{
		tep_db_query("rollback");
	}
	
	tep_db_query("SET AUTOCOMMIT=1");
}

function InsertCotiza_enc($idLista, $local_id, $ses_usr_id, $clie_rut, $invitado){
	//Esta acción debe ser transaccional
	tep_db_query("SET AUTOCOMMIT=0");
	$success = true;

	$QryIns_enc = "INSERT INTO `list_os_enc` (`idLista_enc`,`OS_estado`,`OS_local`,`OS_idUsuario`,`OS_clieRut`,`OS_fecCrea`, `invitado`) 
			   VALUES ('".$idLista."', 'SE', '".$local_id."', '".$ses_usr_id."', '".$clie_rut."', now(), '".$invitado."');";
	$success = $success && tep_db_query($QryIns_enc);
	$Id = mysql_insert_id();
	
	//Fin de la transacción
	if ($success){
		tep_db_query("commit");
		return $Id;
	}
	else{
		tep_db_query("rollback");
	}
}

function InsertCotiza_det($idLista_OS_enc, $Value, $input_cregalar){
	//Esta acción debe ser transaccional
	tep_db_query("SET AUTOCOMMIT=0");
	$success = true;

	$QryIns_det = "INSERT INTO `list_os_det` (`idLista_OS_enc`,`idlista_det`, `OS_cantidad`, `OS_idOT`) 
			   VALUES ('".$idLista_OS_enc."', '".$Value."', '".$input_cregalar."', NULL);";
	$success = $success && tep_db_query($QryIns_det);

	//Fin de la transacción
	if ($success){
		tep_db_query("commit");
		return true;
	}
	else
		tep_db_query("rollback");
}

// *************** ACCIONES ****************** //
// Crea las cotizaciones de los invitados de la Lista de regalos
if ($accion == 'crear'){
	$Listas = split(",", $cadenaLista);	

	$idLista_OS_enc = InsertCotiza_enc($idLista, $local_id, $ses_usr_id, $clie_rut, $invitado);
	
	insertahistorial_ListaReg("Se crea la cotización N°.".$idLista_OS_enc." para la Lista de Regalos N°. ".$idLista.".", $USR_LOGIN, null, $idLista, $idLista_OS_enc, $tipo = 'SYS');

	foreach ($Listas AS $key=>$Value) {
		$input_cantidad = $_POST['cantidad_'.$Value];
		$input_cregalar = $_POST['regalar_'.$Value];
		
		if ($input_cregalar != 0){
			$Updt = UpdateLista($input_cantidad, $input_cregalar, $Value, $idLista, $invitado);
			$insr = InsertCotiza_det($idLista_OS_enc, $Value, $input_cregalar);
			
			if($Updt && $insr){
				?>
				<SCRIPT LANGUAGE="JavaScript">
					location.href='printCotiza_Regalos.php?idLista_OS_enc=<?echo $idLista_OS_enc ?>';
				</SCRIPT>
				<?
			}
		}
	} 				
}


$MiTemplate = new Template();
// asignamos degug maximo
$MiTemplate->debug = 0;
// root directory de los templates
$MiTemplate->set_root(DIRTEMPLATES);
// variables perdidas
$MiTemplate->set_unknowns("remove");

$MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
$MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);


$MiTemplate->set_var("USR_LOCAL",get_local_usr( $ses_usr_id ));
$id_local=get_local_usr( $ses_usr_id );

$MiTemplate->set_var("NOMBRE_PAGINA",NOMBRE_PAGINA);
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

// Agregamos el header
$MiTemplate->set_file("header","header_ident.html");

// Agregamos el main
$MiTemplate->set_file("main","cotiza_Regalos/cotiza_Regalos_02.htm");

// Despliega el Bloque de los productos contenidos en la Lista de regalos
if ($clie_rut && $idLista){
	$MiTemplate->set_block("main", "Productos", "BLO_productos");
	$QryList =  "SELECT L.idLista_det, CONCAT(L.cod_Ean,'<br>','(',L.cod_Easy,')')AS codigo, L.descripcion, REPLACE(FORMAT(L.list_precio,0),',','.') AS precio, L.list_cantprod
				FROM list_regalos_det L
				JOIN list_regalos_enc E ON (E.idLista=L.idLista_enc)
        		JOIN clientes C ON (C.clie_rut=E.clie_Rut)
				WHERE idLista_enc=".($idLista+0)." AND L.list_cantprod<>'0'";
	query_to_set_var($QryList, $MiTemplate, 1, 'BLO_productos', 'Productos');
}

$MiTemplate->set_var("idLista", $idLista);
$MiTemplate->set_var("clie_rut", $clie_rut);

// Agregamos el footer
include "../../includes/footer_cproy.php";

$MiTemplate->pparse("OUT_H", array("header"), true);
include "../../menu/menu.php";
$MiTemplate->parse("OUT_M", array("main","footer"), true);
$MiTemplate->p("OUT_M");

include "../../includes/application_bottom.php";

?>
