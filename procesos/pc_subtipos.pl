# ------------------------------------------------------------
# - PC2 : Traspaso de datos y Validacion de carga 
# -
# - Por GISELA ESTAY JELDES
# - bbr ingenierìa
# - ----------------------------------------------------------
#!/usr/bin/env perl -w


use DBI;
#require "/home/desa/irspromo/general.pl";
require "/var/www/html/centroproy/procesos/general.pl";


# ***************************************************************************
#                            procesa_subtipos 
# ---------------------------------------------------------------------------
# Procesa las marcas especiales TIPO y SUBTIPO a los productos
# ***************************************************************************
sub procesa_subtipos {
    local ($tipo,$subtipo,$tipocatprod)=@_;
	local ($i, $z);
	local $codigos,$tipo,$subtipo,$tipocatprod;
	local ($qrypeC, $sth, @row,$rc, $sth1, $rc1);

	#query para updatiar tipos y subtipos productos especiales
    #$dbh->{AutoCommit} = 0;
    $qrypeC = "select codigos  from tipo_subtipo_adm where tipo='$tipo' and subtipo='$subtipo' and  tipocatprod='$tipocatprod'";
    $sth = $dbh->prepare($qrypeC);
    $rc = $sth->execute();
    $cond = "('";

	$i=0;
	if (($codigo) = $sth->fetchrow_array) {
            @arr_cod = split(',', $codigo);
            $str = join('\',\'',@arr_cod);
            $i++;			
	}#if
    $cond.="$str')";
#si tiene largo mayor que 4 el length de condicion
    if (length($cond)>4 ) {
        if ($tipocatprod eq 'C') {
            $qryuPS="update productos  set prod_tipo='$tipo', prod_subtipo='$subtipo'  where id_catprod in $cond ";
            $sth1  = $dbh->prepare($qryuPS);
            $rc1   = $sth1->execute();
        }
        else {
            $qryuPS="update productos  set prod_tipo='$tipo', prod_subtipo='$subtipo'  where cod_prod1 in $cond ";
    	    $sth1  = $dbh->prepare($qryuPS);
            $rc1   = $sth1->execute();
        }
     #   print $qryuPS;
    }
}

# ***************************************************************************
sub centro_proyectos_subtipos {
	if (&procesa_subtipos('PE','CA','C')) {		&wtlog("productos_subtipos","trp_centro_proyectos",0);}
	if (&procesa_subtipos('PE','GE','P')) {		&wtlog("productos_subtipos","trp_centro_proyectos",0);}
    #instalaciones
    if (&procesa_subtipos('SV','IR','C')) {		&wtlog("productos_subtipos","trp_centro_proyectos",0);}
    if (&procesa_subtipos('SV','IR','P')) {		&wtlog("productos_subtipos","trp_centro_proyectos",0);}
    if (&procesa_subtipos('SV','IN','C')) {		&wtlog("productos_subtipos","trp_centro_proyectos",0);}
    if (&procesa_subtipos('SV','IN','P')) {		&wtlog("productos_subtipos","trp_centro_proyectos",0);}
    if (&procesa_subtipos('SV','DE','C')) {		&wtlog("productos_subtipos","trp_centro_proyectos",0);}
    if (&procesa_subtipos('SV','DE','P')) {		&wtlog("productos_subtipos","trp_centro_proyectos",0);}
    if (&procesa_subtipos('SV','VI','C')) {		&wtlog("productos_subtipos","trp_centro_proyectos",0);}
    if (&procesa_subtipos('SV','VI','P')) {		&wtlog("productos_subtipos","trp_centro_proyectos",0);}
}

# ***************************************************************************
sub main {

    my $config   = read_config();

    #tipo de logs
    &wtlog("inicia programa $0...","MAIN","COMIENZO");	

    #proceso principal
    centro_proyectos_subtipos();
	
    &wtlog("finaliza programa $0...","MAIN","FIN");
	
    #proceso principal	
    db_disc();
}# main
	
main();
