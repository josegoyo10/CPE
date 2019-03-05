<?



include "/var/www/html/centroproy2/includes/funciones/general.php";
include "/var/www/html/centroproy2/includes/funciones/database.php";

echo "Hola BBRI";


writeevent('*************************************************************************************************************');
$queryDespacho="select id_os from os where id_os=31861";

$rq = tep_db_query($queryDespacho);
$res = tep_db_fetch_array( $rq );
$TipoidDespacho=$res['id_os'];
 echo $TipoidDespacho;



?>
