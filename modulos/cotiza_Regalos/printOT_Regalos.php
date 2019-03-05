<?
$pag_ini = '../lista_Regalos/nueva_lista_00.php';
include "../../includes/aplication_top.php";

$local_id = get_local_usr( $ses_usr_id );
$USR_LOGIN = get_login_usr( $ses_usr_id );
include_idioma_mod( $ses_idioma, "printOT_Regalos" );

// *************************************************************************************
// *************** ACCIONES ****************** //
// Crea las cotizaciones de los invitados de la Lista de regalos

$Qry_Enc = "SELECT DISTINCT LR.idLista, OT.ot_idList, LO.idLista_OS_enc, OT.ot_listFeccrea, LR.fec_Evento, OT.ot_listTipo, ES.esta_nombre, OT.ot_listTiendaPago, LR.festejado, LR.descripcion,TD.nombre, DR.dire_direccion, DR.dire_telefono, DR.dire_localizacion, LE.idLista_enc, LR.clie_Rut, OT.ot_listNumImp
			FROM list_ot OT 
			LEFT JOIN list_os_det LO ON LO.OS_idOT = OT.ot_idList
			LEFT JOIN list_os_enc LE ON LE.idLista_OS_enc = LO.idLista_OS_enc
			LEFT JOIN list_regalos_det LD ON LD.idLista_det = LO.idLista_det
			LEFT JOIN list_regalos_enc LR ON LR.idLista = LD.idLista_enc 
			LEFT JOIN direcciones DR ON DR.id_direccion = LR.id_Direccion
			LEFT JOIN tipos_despacho TD ON TD.id_tipodespacho = LD.list_idTipodespacho
			LEFT JOIN estados ES ON ES.id_estado = OT.ot_idEstado
			LEFT JOIN locales LC ON LC.id_local = LR.id_Local
			WHERE OT.ot_idList= $id_OT ";

$res1 = tep_db_query($Qry_Enc);
$result1 = tep_db_fetch_array( $res1 );

		//Establece el Tipo de Orden de Trabajo
		if($result1['ot_listTipo'] == 'PS')
			$tipoOT = 'Producto Stock';
			
		else if ($result1['ot_listTipo'] == 'SV')
			$tipoOT = 'Servicio';
		
		else if ($result1['ot_listTipo'] == 'PE')
			$tipoOT = 'Pedido Especial';

$datosDireccion= getlocalizacion($result1['dire_localizacion']);

$Qry_Tot = "SELECT SUM(LO.OS_cantidad) AS Total FROM list_ot OT 
			LEFT JOIN list_os_det LO ON LO.OS_idOT = OT.ot_idList 
			LEFT JOIN list_regalos_det LD ON LD.idLista_det = LO.idLista_det
			WHERE OT.ot_idList= $id_OT ";
$res3 = tep_db_query($Qry_Tot);
$result3 = tep_db_fetch_array( $res3 );


			
$MiTemplate = new Template();
// asignamos degug maximo
$MiTemplate->debug = 0;
// root directory de los templates
$MiTemplate->set_root(DIRTEMPLATES);
// variables perdidas
$MiTemplate->set_unknowns("remove");

// Pasamos las variables a la Plantilla
$MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
$MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);

$MiTemplate->set_var("USR_LOCAL",get_local_usr( $ses_usr_id ));
$id_local=get_local_usr( $ses_usr_id );

$MiTemplate->set_var("ot_idList", $result1['ot_idList']);
$MiTemplate->set_var("idLista_OS", $result1['idLista_OS_enc']);
$MiTemplate->set_var("idLista_enc", $result1['idLista_enc']);
$MiTemplate->set_var("clie_Rut", $result1['clie_Rut']);
$MiTemplate->set_var("ot_listFeccrea", fecha_db2php($result1['ot_listFeccrea']));
$MiTemplate->set_var("fec_Evento", fecha_db2php($result1['fec_Evento']));
$MiTemplate->set_var("tipoOT", $tipoOT);
$MiTemplate->set_var("estado", $result1['esta_nombre']);
$MiTemplate->set_var("local", $result1['nom_local']);
$MiTemplate->set_var("festejado", $result1['festejado']);
$MiTemplate->set_var("descripcion", $result1['descripcion']);
$MiTemplate->set_var("tipDespacho", $result1['nombre']);
$MiTemplate->set_var("direccion", $result1['dire_direccion']);
$MiTemplate->set_var("telefono", $result1['dire_telefono']);
$MiTemplate->set_var("departamento", $datosDireccion['departamento']);
$MiTemplate->set_var("provincia", $datosDireccion['provincia']);
$MiTemplate->set_var("ciudad", $datosDireccion['ciudad']);
$MiTemplate->set_var("localidad", $datosDireccion['localidad']);
$MiTemplate->set_var("barrio", $datosDireccion['barrio']);
$MiTemplate->set_var("NumImp", $result1['ot_listNumImp']);

$MiTemplate->set_var("Total",$result3['Total']);

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
$MiTemplate->set_var("TEXT_CAMPO_11",TEXT_CAMPO_11);
$MiTemplate->set_var("TEXT_CAMPO_12",TEXT_CAMPO_12);
$MiTemplate->set_var("TEXT_CAMPO_13",TEXT_CAMPO_13);
$MiTemplate->set_var("TEXT_CAMPO_14",TEXT_CAMPO_14);
$MiTemplate->set_var("TEXT_CAMPO_15",TEXT_CAMPO_15);
$MiTemplate->set_var("TEXT_CAMPO_16",TEXT_CAMPO_16);
$MiTemplate->set_var("TEXT_CAMPO_17",TEXT_CAMPO_17);
$MiTemplate->set_var("TEXT_CAMPO_18",TEXT_CAMPO_18);
$MiTemplate->set_var("TEXT_CAMPO_19",TEXT_CAMPO_19);
$MiTemplate->set_var("TEXT_CAMPO_20",TEXT_CAMPO_20);
$MiTemplate->set_var("TEXT_CAMPO_21",TEXT_CAMPO_21);
$MiTemplate->set_var("TEXT_CAMPO_22",TEXT_CAMPO_22);
$MiTemplate->set_var("TEXT_CAMPO_23",TEXT_CAMPO_23);
$MiTemplate->set_var("TEXT_CAMPO_24",TEXT_CAMPO_24);
$MiTemplate->set_var("TEXT_CAMPO_25",TEXT_CAMPO_25);
$MiTemplate->set_var("TEXT_CAMPO_26",TEXT_CAMPO_26);
$MiTemplate->set_var("TEXT_CAMPO_27",TEXT_CAMPO_27);
$MiTemplate->set_var("TEXT_CAMPO_28",TEXT_CAMPO_28);
$MiTemplate->set_var("TEXT_CAMPO_29",TEXT_CAMPO_29);
$MiTemplate->set_var("TEXT_CAMPO_30",TEXT_CAMPO_30);
$MiTemplate->set_var("TEXT_CAMPO_35",TEXT_CAMPO_35);


$MiTemplate->set_var("idLista_OS_enc", $idLista_OS_enc);
$MiTemplate->set_var("idLista", $result1['idLista']);

// Agregamos el header
$MiTemplate->set_file("header","header_ident.html");

// Agregamos el main
$MiTemplate->set_file("main","cotiza_Regalos/printOT_Regalos.htm");


$MiTemplate->set_block("main", "Productos", "BLO_productos");
$Qry_Det = "SELECT LD.cod_ean, LD.cod_Easy, LD.descripcion, LD.list_precio, LO.OS_cantidad  FROM list_ot OT 
			LEFT JOIN list_os_det LO ON LO.OS_idOT = OT.ot_idList 
			LEFT JOIN list_regalos_det LD ON LD.idLista_det = LO.idLista_det
			WHERE OT.ot_idList= ".$id_OT." GROUP By LD.cod_Easy ";
query_to_set_var($Qry_Det, $MiTemplate, 1, 'BLO_productos', 'Productos');

insertahistorial_ListaReg("Se imprime la Orden de Trabajo N°. ".$id_OT.", correspodiente a la OS N°. ".$result1['idLista_OS_enc'].", del la Lista de Regalos N°. ".$result1['idLista_enc']." ", $USR_LOGIN, $id_OT, $result1['idLista_enc'], $result1['idLista_OS_enc'], $tipo = 'SYS');
// Agregamos el footer
include "../../includes/footer_cproy.php";

$MiTemplate->pparse("OUT_H", array("header"), true);
include "../../menu/menu.php";
$MiTemplate->parse("OUT_M", array("main","footer"), true);
$MiTemplate->p("OUT_M");

include "../../includes/application_bottom.php";

?>
