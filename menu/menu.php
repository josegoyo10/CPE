<link rel="stylesheet" href="../../menu/menu.css">
<script src="../../menu/menu.js"></script>
<script src="../../menu/menu_tpl.js"></script>
<script>
<?php
$lista_mod = get_modulos();

 
if( $lista_mod == '' )
    $lista_mod = -1;


$query_1 = "select distinct mod_id, mod_url, mod_nombre from permisosxmodulo, modulos where mod_id = pemo_mod_id and pemo_mod_id in ( $lista_mod ) and mod_estado = 1 and mod_padre_id is NULL order by mod_orden, mod_nombre";

 file_put_contents("menuquery1.txt", $query_1);

//echo "query 1:".$query_1."<br>";

$db_1 = tep_db_query($query_1);

echo "var MENU_ITEMS = ["; 

while( $res_1 = tep_db_fetch_array( $db_1 ) ) {

	$query_2 = "select distinct mod_id, mod_url, mod_nombre from permisosxmodulo, modulos where mod_id = pemo_mod_id and pemo_mod_id in ( $lista_mod ) and mod_padre_id = ".$res_1['mod_id']." and mod_estado = 1 order by mod_orden, mod_nombre";

   file_put_contents("menuquery2.txt", print_r($query_2,true));
 
    $db_2 = tep_db_query($query_2);


   
    if( tep_db_num_rows($db_2) == 0 )  // No tiene hijos
		$link="'".$res_1['mod_url']."'";
	else
		$link="null";

?>

	['&nbsp;<?= $res_1['mod_nombre'] ?>', <?= $link ?>, null,

		// Submenu
		<? while( $res_2 = tep_db_fetch_array( $db_2 ) ) { 
             file_put_contents("menuarray.txt", print_r($res_2,true));
           
			?>

              
          

		 ['&nbsp;<?= $res_2['mod_nombre'] ?>', '<?= $res_2['mod_url'] ?>'],
		<? } // End While ?>

	],





<?
}//End while1
echo "];"; // cierra js
?>

new menu (MENU_ITEMS, MENU_POS);
</script>
