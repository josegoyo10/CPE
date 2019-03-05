<?
// Agregamos el footer
$MiTemplate->set_file("footer","footer_ident.html");
$MiTemplate->set_var("user", get_login_usr( $ses_usr_id ) );
$mylocal = get_local_usr( $ses_usr_id );
if ($mylocal)
	$MiTemplate->set_var("local", get_locald_usr( $ses_usr_id ) );
else
	$MiTemplate->set_var("local", "TODOS" );
$MiTemplate->set_var("fecha",date("d/m/Y", time()));
?>