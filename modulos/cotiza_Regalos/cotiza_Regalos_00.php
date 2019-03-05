<?php
$pag_ini = '../lista_Regalos/nueva_lista_00.php';
include "../../includes/aplication_top.php";
require_once('../../wsClientUnique/ClientUnique.php');

include_idioma_mod( $ses_idioma, "cotiza_Regalos_00" );

 // *************************************************************************************
/*acciones*/
if($clie_rut ){
   if ($tpcliente=="persona") 
   			$persona='p';
            $queryEx =  "SELECT clie_rut,clie_tipo FROM clientes where clie_rut=".($clie_rut+0)." ";
            $eclien = tep_db_query($queryEx);
            $eclien = tep_db_fetch_array( $eclien );
            $ingresadocomo= $eclien['clie_tipo'];

            if ($ingresadocomo == 'p'){
            	?>
                	<script language="JavaScript">
                    location.href='cotiza_Regalos_01.php?clie_rut=<?=$eclien['clie_rut']?>&idLista=<?echo $idLista;?>' ;
                    </script>
                <?php
                }
            if ($ingresadocomo == ''){
				/* MODO BUSQUEDA PARA CLIENTE UNICO*/        
   				$response = ClientUnique::searchById($clie_rut);			
   				if($response){
   					if($response['IdCustomer']){
                        if( $response['IdGroup']==1 || $response['IdGroup']== "")
						{
							?>
							<script language="JavaScript">
	                    	location.href='cotiza_Regalos_01.php?clie_rut=<?=$response['IdCustomer']?>&idLista=<?echo $idLista;?>' ;
	                    	</script>
	                    	<?php
						}
						
						if( $response['IdGroup'] == 2)
						{
							echo $idLista."<br><br>";	
							$delCLie =  "DELETE FROM clientes WHERE clie_rut =".$clie_rut;
							tep_db_query($delCLie);
							?>
							<script language="JavaScript">
							alert('Este cliente no puede realizar cotizaciones en CP\n\tPor Favor Remitalo a CVE');
							location.href='../lista_Regalos/monitor_ListRegalos.php'
							</script>
							<?php	
						}
					}
					else{
							?>
							<script language="JavaScript">
							if(confirm('El Cliente no se encuentra creado en Cliente Único. \n\t        Desea ingresarlo?'))
								location.href='cotiza_Regalos_01.php?clie_rut=<?=$clie_rut?>&idLista=<?echo $idLista;?>' ;
							</script>
							<?php
					}
				}
				else {
					print"<script language=\"JavaScript\">alert(\"Se presento un error en el WS Cliente Único. \");</script>";
					$TypeCustomer = 6;
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

$MiTemplate->set_var("idLista", $idLista);

$MiTemplate->set_var("PAGETITLE",NOMBRE_SITIO . ' - ' . NOMBRE_PAGINA);
$MiTemplate->set_var("TEXT_TITULO",TEXT_TITULO);
$MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));
//$MiTemplate->set_var("TEXT",ver_contenido_editable( 1 ));

$MiTemplate->set_var("NOMBRE_PAGINA",NOMBRE_PAGINA);
$MiTemplate->set_var("TEXT_CAMPO_0",TEXT_CAMPO_0);
$MiTemplate->set_var("TEXT_CAMPO_1",TEXT_CAMPO_1);
$MiTemplate->set_var("TEXT_CAMPO_3",TEXT_CAMPO_3);
$MiTemplate->set_var("TEXT_FLECHA_SIG",TEXT_FLECHA_SIG);
$MiTemplate->set_var("TEXT_FLECHA_ANT",TEXT_FLECHA_ANT);

// Agregamos el header
$MiTemplate->set_file("header","header_ident.html");

// Agregamos el main
$MiTemplate->set_file("main","cotiza_Regalos/cotiza_Regalos_00.htm");

// Agregamos el footer
include "../../includes/footer_cproy.php";

$MiTemplate->pparse("OUT_H", array("header"), true);
include "../../menu/menu.php";
$MiTemplate->parse("OUT_M", array("main","footer"), true);
$MiTemplate->p("OUT_M");
include "../../includes/application_bottom.php";
?>
