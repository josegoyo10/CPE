<?
$pag_ini = '../nueva_cotizacion/nueva_cotizacion_00.php';
include "../../includes/aplication_top.php";

// include_idioma_mod( $ses_idioma, "nueva_cotizacion_01" );

// *************************************************************************************
/** Inicio Acciones  **/

/*Inserta un proyecto*/

function digiVer($r){
    $s=1;for($m=0;$r!=0;$r/=10)
    $s=($s+$r%10*(9-$m++%6))%11;
return chr($s?$s+47:75);}

$digito=digiVer($clie_rut);

if ($accion=="guardar"){
   $queryUpP ="Insert INTO proyectos (clie_rut,proy_nombre) VALUES (".($clie_rut+0).",'$proy_nombre')";
   tep_db_query($queryUpP);
   $ultimoID = tep_db_insert_id('');
?>
        <script language="JavaScript">
            window.returnValue = 'refresh';
            window.close();
        </script>
<?

   tep_exit();
}

/** Fin Acciones **/

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

// Agregamos el main
$MiTemplate->set_file("main","nueva_cotizacion/nuevo_proy.htm");

$MiTemplate->set_var("digito",$digito);
$MiTemplate->set_var("clie_rut",$clie_rut);
$MiTemplate->set_var("proy_nombre",$proy_nombre);

$MiTemplate->parse("OUT_M", array("main","footer"), true);
$MiTemplate->p("OUT_M");

include "../../includes/application_bottom.php";
?>