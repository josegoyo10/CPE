<?
$pag_ini = '../lista_Regalos/nueva_lista_00.php';
include "../../includes/aplication_top.php";

include_idioma_mod( $ses_idioma, "print_despachoLista" );

$local_id = get_local_usr( $ses_usr_id );
$USR_LOGIN = get_login_usr( $ses_usr_id );
$idLista = $_GET['idLista'];

// *************************************************************************************
// *************** ACCIONES ****************** //
// Crea las cotizaciones de los invitados de la Lista de regalos

$QryEnc ="SELECT L.idLista, L.fec_creacion, E.esta_nombre, T.nom_local, CONCAT(U.USR_NOMBRES,' ', USR_APELLIDOS) AS asesor, V.nombre AS evento, L.fec_Evento, L.festejado, C.clie_rut, CONCAT(C.clie_nombre,' ',C.clie_materno,' ',C.clie_paterno)AS clie_nombre, C.clie_telefonocasa, D.dire_direccion, C.clie_telcontacto2, L.id_Direccion, D.dire_localizacion, L.GD_numImpresion, GD_id
			FROM list_regalos_enc L 
			LEFT JOIN clientes C ON (C.clie_rut=L.clie_Rut) 
			LEFT JOIN list_eventos V ON (V.idEvento=L.id_Evento) 
			LEFT JOIN direcciones D ON (D.id_direccion=L.id_Direccion) 
			LEFT JOIN locales T ON (T.id_local=L.id_Local)
            LEFT JOIN estados E ON (E.id_estado=L.id_Estado)
            LEFT JOIN usuarios U ON (U.USR_ID=L.id_Usuario)
			WHERE idLista = $idLista";
$res = tep_db_query($QryEnc);
$result = tep_db_fetch_array( $res );

$datosDireccion= getlocalizacion($result['dire_localizacion']);

			
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

$MiTemplate->set_var("idLista", $idLista);
$MiTemplate->set_var("numImp", $result['GD_numImpresion']);
$MiTemplate->set_var("noGuia", $result['GD_id']);
$MiTemplate->set_var("tienda", $result['nom_local']);
$MiTemplate->set_var("fec_crea", fecha_db2php($result['fec_creacion']));
$MiTemplate->set_var("fec_entrega", fecha_db2php($result['fec_Evento']));
$MiTemplate->set_var("estado", $result['esta_nombre']);
$MiTemplate->set_var("evento", $result['evento']);
$MiTemplate->set_var("asesor", $result['asesor']);
$MiTemplate->set_var("festejado", $result['festejado']);
$MiTemplate->set_var("clie_rut", $result['clie_rut']);
$MiTemplate->set_var("clie_nombre", $result['clie_nombre']);
$MiTemplate->set_var("clie_telefonocasa", $result['clie_telefonocasa']);
$MiTemplate->set_var("clie_telcontacto2", $result['clie_telcontacto2']);
$MiTemplate->set_var("direccion", $result['dire_direccion']);
$MiTemplate->set_var("barrio", $datosDireccion['barrio']);
$MiTemplate->set_var("ciudad", $datosDireccion['ciudad']);
$MiTemplate->set_var("localidad", $datosDireccion['localidad']);
$MiTemplate->set_var("departamento", $datosDireccion['departamento']);

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
$MiTemplate->set_var("TEXT_CAMPO_31",TEXT_CAMPO_31);
$MiTemplate->set_var("TEXT_CAMPO_32",TEXT_CAMPO_32);
$MiTemplate->set_var("TEXT_CAMPO_33",TEXT_CAMPO_33);
$MiTemplate->set_var("TEXT_CAMPO_34",TEXT_CAMPO_34);
$MiTemplate->set_var("TEXT_CAMPO_35",TEXT_CAMPO_35);
$MiTemplate->set_var("TEXT_CAMPO_36",TEXT_CAMPO_36);
$MiTemplate->set_var("TEXT_CAMPO_37",TEXT_CAMPO_37);

// Agregamos el header
$MiTemplate->set_file("header","header_ident.html");

// Agregamos el main
$MiTemplate->set_file("main","lista_Regalos/print_despachoLista.htm");


$MiTemplate->set_block("main", "Productos", "BLO_productos");
$Qry_Det = "SELECT DISTINCT LT.cod_Ean, LT.cod_Easy, LT.descripcion, LT.list_Cantcomp AS cantPick
			FROM list_os_det LD
			LEFT JOIN list_regalos_det LT ON (LT.idLista_det=LD.idLista_det)
			LEFT JOIN list_os_enc LE ON LE.idLista_OS_enc = LD.idLista_OS_enc
			LEFT JOIN list_ot LO ON LO.ot_idList =LD.OS_idOT
			WHERE LE.idLista_enc=".$idLista." AND LE.OS_estado='SP'
			GROUP BY LT.cod_Ean";
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
