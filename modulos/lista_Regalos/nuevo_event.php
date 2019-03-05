<?
$pag_ini = '../lista_Regalos/nueva_lista_00.php';
include "../../includes/aplication_top.php";

// *************************************************************************************
/** Inicio Acciones  **/

/*Inserta un proyecto*/

function digiVer($r){
    $s=1;for($m=0;$r!=0;$r/=10)
    $s=($s+$r%10*(9-$m++%6))%11;
return chr($s?$s+47:75);}

$digito=digiVer($clie_rut);

if ($accion=="guardar"){
   $queryUpP ="INSERT INTO list_eventos (nombre) VALUES ('$nombre')";
   tep_db_query($queryUpP);
   $ultimoID = tep_db_insert_id('');
?>
        <script language="JavaScript">
            window.opener.location.reload();
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

$MiTemplate->set_var("USR_NOMBRE",get_nombre_usr( $ses_usr_id ));

// Agregamos el main
$MiTemplate->set_file("main","lista_Regalos/nuevo_event.htm");

$MiTemplate->set_var("nombre",$proy_nombre);

$MiTemplate->parse("OUT_M", array("main","footer"), true);
$MiTemplate->p("OUT_M");

include "../../includes/application_bottom.php";
?>