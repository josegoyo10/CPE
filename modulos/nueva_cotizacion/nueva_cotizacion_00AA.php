<?php
$pag_ini = '../nueva_cotizacion/nueva_cotizacion_00.php';
include "../../includes/aplication_top.php";

include_idioma_mod( $ses_idioma, "nueva_cotizacion_00" );

// *************************************************************************************

/*acciones*/

/* ve si los datos corresponden*/
if($clie_rut ){
   //if ($tpcliente=="empresa") $persona='e';
   
   if ($tpcliente=="persona") $persona='p';
            $queryEx =  "SELECT clie_rut,clie_tipo FROM clientes where clie_rut=".($clie_rut+0)." ";
            $eclien = tep_db_query($queryEx);
            $eclien = tep_db_fetch_array( $eclien );
            $ingresadocomo= $eclien['clie_tipo'];
               if($persona!=$ingresadocomo && $ingresadocomo!=''){
                    if ($ingresadocomo=='e'){
                       ?>
                        <script language="JavaScript">
                        window.alert("Este rut ha sido ingresado anteriormente como Empresa");
                        location.href='nueva_cotizacion_empresa_01.php?clie_rut=<?=$eclien['clie_rut']?>' ;
                        </script>
                        <?php
                    }
                    if ($ingresadocomo=='p'){
                        ?>
                        <script language="JavaScript">
                        window.alert("Este rut ha sido ingresado anteriormente como Persona");
                        location.href='nueva_cotizacion_01.php?clie_rut=<?=$eclien['clie_rut']?>' ;
                        </script>
                        <?php
                    }
               }
 }



/* verifica si el rut existe o no y si es persona o empresa*/
    if ($clie_rut and $tpcliente=="persona"){
        $queryCliep =  "SELECT clie_rut FROM clientes where clie_rut=".($clie_rut+0)." and clie_tipo='p'";
        $eclientp = tep_db_query($queryCliep);
        $eclientp = tep_db_fetch_array( $eclientp );
        $rutP=$eclientp['clie_rut'];
            if ($rutP) $rutExisteP=true;
            else $rutExisteP=false;
    }
/*    
if ($clie_rut and $tpcliente=="empresa"){
    $queryClie =  "SELECT clie_rut FROM clientes where clie_rut=".($clie_rut+0)." and clie_tipo='e'";
    $ecliente = tep_db_query($queryClie);
    $ecliente = tep_db_fetch_array( $ecliente );
    $rutE=$ecliente['clie_rut'];
    if ($rutE) $rutExisteE=true;
    else $rutExisteE=false;
}
*/
    
/* reenvia los datos a la pagina que corresponda*/

/*
if ($tpcliente == "empresa" AND $rutExisteE) { // Redir a la cotizacion para empresa con rut Exi
	header ('Location: nueva_cotizacion_empresa_01.php?clie_rut='.($clie_rut).'&clie_tipo='.($tpcliente));
    tep_exit();
} 
if ($tpcliente == "empresa" AND !$rutExisteE) { // Redir a la cotizacion para empre co Nuevo rut
    $querytE =  "INSERT INTO clientes (clie_rut,clie_tipo,clie_activo) VALUES(".($clie_rut+0).",'e',1)";
	tep_db_query($querytE);
	header ('Location: nueva_cotizacion_empresa_01.php?clie_rut='.($clie_rut+0).'&clie_tipo='.($tpcliente));
    tep_exit();
}
*/

 if ($tpcliente == "persona" and $rutExisteP) { // // Redirec a la cotizacion para persona
	header ('Location: nueva_cotizacion_01.php?clie_rut='.($clie_rut).'&clie_tipo='.($tpcliente));
    tep_exit();
}
if ($tpcliente == "persona" AND !$rutExisteP) { // Redir a la cotizacion para empre co Nuevo rut
    $queryP =  "INSERT INTO clientes (clie_rut,clie_tipo,clie_activo) VALUES(".($clie_rut+0).",'p',1)";
    tep_db_query($queryP);
	header ('Location: nueva_cotizacion_01.php?clie_rut='.($clie_rut+0).'&clie_tipo='.($tpcliente));
    tep_exit();
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
$MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));
//$MiTemplate->set_var("TEXT",ver_contenido_editable( 1 ));

$MiTemplate->set_var("TEXT_CAMPO_0",TEXT_CAMPO_0);
$MiTemplate->set_var("TEXT_CAMPO_1",TEXT_CAMPO_1);
$MiTemplate->set_var("TEXT_CAMPO_3",TEXT_CAMPO_3);
$MiTemplate->set_var("TEXT_FLECHA_SIG",TEXT_FLECHA_SIG);

// Agregamos el header
$MiTemplate->set_file("header","header_ident.html");

// Agregamos el main
$MiTemplate->set_file("main","nueva_cotizacion/nueva_cotizacion_00.htm");

// Agregamos el footer
include "../../includes/footer_cproy.php";

$MiTemplate->pparse("OUT_H", array("header"), true);
include "../../menu/menu.php";
$MiTemplate->parse("OUT_M", array("main","footer"), true);
$MiTemplate->p("OUT_M");
include "../../includes/application_bottom.php";
?>
