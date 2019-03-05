<?
$pag_ini = '../lista_Regalos/nueva_lista_00.php';
include "../../includes/aplication_top.php";

$local_id = get_local_usr( $ses_usr_id );
$USR_LOGIN = get_login_usr( $ses_usr_id );
include_idioma_mod( $ses_idioma, "printCotiza_Regalos" );

// *************************************************************************************
// *************** ACCIONES ****************** //
// Crea las cotizaciones de los invitados de la Lista de regalos

$Qry_Enc = "SELECT DISTINCT R.idLista, V.nombre AS evento, R.festejado, E.invitado,L.cod_local, L.nom_local, E.OS_fecCrea, S.nombre AS tipoDespacho, A.esta_nombre AS OS_Estado
		         FROM list_os_det D
		         JOIN list_os_enc E ON (E.idLista_OS_enc = D.idLista_OS_enc)
		         JOIN list_regalos_det T ON (T.idLista_det = D.idLista_det)
		         JOIN list_regalos_enc R ON (R.idLista = E.idLista_enc)
		         JOIN list_eventos V ON (idEvento = R.id_Evento)
		         JOIN locales L ON (L.id_local = E.OS_Local)
		         JOIN tipos_despacho S ON (S.id_tipodespacho = T.list_idTipodespacho)
		         JOIN estados A ON (A.id_estado = E.OS_estado)
		 WHERE D.idLista_OS_enc = $idLista_OS_enc ";
$res1 = tep_db_query($Qry_Enc);
$result1 = tep_db_fetch_array( $res1 );

$Qry_Tot = "SELECT SUM((A.list_precio *  D.OS_cantidad)) AS Total
 			FROM list_os_det D
        	JOIN list_regalos_det A ON (A.idLista_det = D.idLista_det)
			WHERE D.idLista_OS_enc= $idLista_OS_enc ";
$res3 = tep_db_query($Qry_Tot);
$result3 = tep_db_fetch_array( $res3 );

// Arma el código de Barras de la cotizacion
$Codigo_Local = substr($result1['cod_local'], 1);	
$cod_ean_os = calcula_num_os($idLista_OS_enc);		
$codigo = PREFIJO_LISTA_REGALOS . $Codigo_Local . $cod_ean_os;
$dig_verificacion = dvEAN13($codigo);
$codigo_ean = $codigo . $dig_verificacion;	
			
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

$MiTemplate->set_var("idLista",$result1['idLista']);
$MiTemplate->set_var("evento",$result1['evento']);
$MiTemplate->set_var("festejado",$result1['festejado']);
$MiTemplate->set_var("invitado",$result1['invitado']);
$MiTemplate->set_var("local",$result1['nom_local']);
$MiTemplate->set_var("OS_fecCrea",fecha_db2php($result1['OS_fecCrea']));
$MiTemplate->set_var("tipoDespacho",$result1['tipoDespacho']);
$MiTemplate->set_var("OS_Estado",$result1['OS_Estado']);
$MiTemplate->set_var("Total",formato_precio($result3['Total']));
$MiTemplate->set_var("cod_barra_os",gencode_EAN13($codigo_ean, 150, 60));
$MiTemplate->set_var("cond_Comercial1",CON_COM1);
$MiTemplate->set_var("cond_Comercial2",CON_COM2);
$MiTemplate->set_var("cond_Comercial3",CON_COM3);
$MiTemplate->set_var("cond_Comercial4",CON_COM4);
$MiTemplate->set_var("cond_Comercial5",CON_COM5);

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
$MiTemplate->set_var("idLista_OS_enc", $idLista_OS_enc);
$MiTemplate->set_var("idLista", $result1['idLista']);

// Agregamos el header
$MiTemplate->set_file("header","header_ident.html");

// Agregamos el main
$MiTemplate->set_file("main","cotiza_Regalos/printCotiza_Regalos.htm");


$MiTemplate->set_block("main", "Productos", "BLO_productos");
$Qry_Det = "SELECT CONCAT(A.cod_Ean,'<br>','(',A.cod_Easy,')')AS codigo, A.descripcion, REPLACE(FORMAT(A.list_precio,0),',','.') AS precio, 
				REPLACE(FORMAT(((D.OS_cantidad)),0),',','.') AS cantidad 
				FROM list_os_det D
         		JOIN list_regalos_det A ON (A.idLista_det = D.idLista_det)
			WHERE D.idLista_OS_enc= $idLista_OS_enc ";
query_to_set_var($Qry_Det, $MiTemplate, 1, 'BLO_productos', 'Productos');

insertahistorial_ListaReg("Se imprime la Cotización N°.$idLista_OS_enc  para la Lista de Regalos N°. ".$result1['idLista'].".", $USR_LOGIN, null, $result1['idLista'], $idLista_OS_enc, $tipo = 'SYS');

// Agregamos el footer
include "../../includes/footer_cproy.php";

$MiTemplate->pparse("OUT_H", array("header"), true);
include "../../menu/menu.php";
$MiTemplate->parse("OUT_M", array("main","footer"), true);
$MiTemplate->p("OUT_M");

include "../../includes/application_bottom.php";

?>
