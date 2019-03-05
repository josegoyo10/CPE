<?
$pag_ini = '../lista_Regalos/monitor_ListRegalos.php';
include "../../includes/aplication_top.php";
include_idioma_mod( $ses_idioma, "sp" );

/****************************************************************/
/****************************************************************
 *
 * Despliega Listado Búsqueda
 *
 ****************************************************************/
global $ses_usr_id;   
	   
	$MiTemplate = new Template();
    // asignamos degug maximo
    $MiTemplate->debug = 0;
    // root directory de los templates
    $MiTemplate->set_root(DIRTEMPLATES);
    // variables perdidas
    $MiTemplate->set_unknowns("remove");
        
    // Agregamos el main
    $MiTemplate->set_file("main","rep_Compras/reporteCompras.htm");     
    $MiTemplate->set_var("id_Lista", $idLista);  
	
	// Se realizan las consultas a la Base de Datos segun los criterios de Consulta establecidos.		
	$queryENC = "SELECT L.idLista,Lc.nom_local 
	             FROM list_regalos_enc L
		         JOIN locales Lc ON (Lc.id_local=L.id_Local)
				  WHERE L.idLista IN (".$idLista.") AND L.id_Estado IN ('CC');";
	$result_ENC = tep_db_query($queryENC);			
			
	//	Se inicializa el bloque en el que se presentaran los resultados
	$MiTemplate->set_block("main", "Bloque_Resultados", "PBLResultados");
	while( $res_ENC = tep_db_fetch_array( $result_ENC ) ) {
			$MiTemplate->set_var("idLista", ($res_ENC['idLista'])?$res_ENC['idLista']:"&nbsp;");
			$MiTemplate->set_var("id_Local", ($res_ENC['nom_local'])?$res_ENC['nom_local']:"&nbsp;");

			$queryDET = "SELECT DISTINCT LD.idLista_enc, LD.cod_Ean, LD.descripcion, (LD.list_cantprod+LD.list_Cantcomp) AS list_cantprod, LD.list_Cantcomp, 
						LD.list_cantprod AS cant_Xcomp
						FROM list_regalos_det LD 
						LEFT JOIN list_os_det OD ON (OD.idLista_det=LD.idLista_det) 
						LEFT JOIN list_ot OT ON (OT.ot_idList=OD.OS_idOT) 
						WHERE LD.idLista_enc = ".($res_ENC['idLista']).";";

			$res_DET = tep_db_query($queryDET);											
			if(tep_db_num_rows( $res_DET ) < 1){
				$MiTemplate->set_var("codigo", "&nbsp;");
				$MiTemplate->set_var("descripcion", "&nbsp;");
				$MiTemplate->set_var("cant_solici", "&nbsp;");
				$MiTemplate->set_var("cant_comp", "&nbsp;");
				$MiTemplate->set_var("cant_Xcomp", "&nbsp;");
			}
			else{
				while( $result_DET = tep_db_fetch_array( $res_DET ) ) {
					$codigo = ($result_DET['cod_Ean']."<br>").$codigo; 
					$descripcion = ($result_DET['descripcion']."<br>").$descripcion; 
					$cant_solici = ($result_DET['list_cantprod']."<br>").$cant_solici; 
					$cant_comp = ($result_DET['list_Cantcomp']."<br>").$cant_comp;
					$cant_Xcomp = (($result_DET['cant_Xcomp'])."<br>").$cant_Xcomp;
				}
				$MiTemplate->set_var("codigo", $codigo?$codigo:"&nbsp;");
				$MiTemplate->set_var("descripcion", $descripcion?$descripcion:"&nbsp;");
				$MiTemplate->set_var("cant_solici", $cant_solici?$cant_solici:"&nbsp;");
				$MiTemplate->set_var("cant_comp", $cant_comp);
				$MiTemplate->set_var("cant_Xcomp", $cant_Xcomp);
				
				$codigo = "";
				$descripcion = "";
				$cant_solici = "";
				$cant_comp = "";
				$cant_Xcomp = "";
			}	
	
	$MiTemplate->parse("PBLResultados", "Bloque_Resultados", true);
	}
	
	// Agregamos el header
    $MiTemplate->set_file("header","header_ident.html");  

    // Agregamos el footer
	include "../../includes/footer_cproy.php";

    $MiTemplate->pparse("OUT_H", array("header"), false);
    include "../../menu/menu.php";
    $MiTemplate->parse("OUT_M", array("main","footer"), true);
    $MiTemplate->p("OUT_M");


/**********************************************************************************************/

include "../../includes/application_bottom.php";

?>