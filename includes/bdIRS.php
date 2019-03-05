<?
function puede_modificar($idpl) {
	$db_1 =	tep_db_query("select estado from promociones where id_promocion = " . ($idpl+0));
	return (($res_1 = tep_db_fetch_array( $db_1 )) && $res_1['estado'] == "T")?1:0;
}
?>