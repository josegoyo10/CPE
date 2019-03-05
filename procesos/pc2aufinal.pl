#!/usr/bin/env perl -w

use DBI;
require "/var/www/html/centroproy/procesos/general.pl";

sub carga_to_trp {
local $qry;
local $rc;
local $sth;
local $qsql;
$flag,$flag_CATPROD,$flag_PRODUCTOS,$flag_PROVEEDORES,$flag_PRODPROV,$flag_CODBARRA;

	#lee id de carga nuevo
	$qry = "SELECT idcarga FROM car_estadocarga WHERE estado='A' limit 1";
	$sth = $dbh->prepare($qry);
	$rc = $sth->execute();
	if (($sth->err)>0) {
	&welog("Listado de ID CARGA TRP Err:<$DBI::err>-<$DBI::errstr>","carga_to_trp",-1);
	return 0;
	}
	#extrae el idcarga
	@row = $sth->fetchrow_array;
	$idcarga = @row[0];
	
	&wtlog("IDCARGA =$idcarga","carga_to_trp",0);
	
	#vacia tablas TRP
	$qry = "TRUNCATE TABLE trp_productos;";
	exec_sql_non_select($qry);
	$qry = "TRUNCATE TABLE trp_proveedores;";
	exec_sql_non_select($qry);
	$qry = "TRUNCATE TABLE trp_prodxprov;";
	exec_sql_non_select($qry);
	$qry = "TRUNCATE TABLE trp_catprod;";
	exec_sql_non_select($qry);
	$qry = "TRUNCATE TABLE trp_codbarra;";
	exec_sql_non_select($qry);
	$qry = "DELETE FROM trp_osdetprod WHERE estado_disp = 'D'";
	exec_sql_non_select($qry);
	$qry = "DELETE FROM trp_promoscat WHERE estado_disp = 'D'";
	exec_sql_non_select($qry);
	
    $flag =0;
    
    # AutoCommit => 0
    $dbh->{AutoCommit} = 0;
    #revisa los id de carga de categorias */
    $query="select MAX(idcarga) from car_catprod";
	$sth = $dbh->prepare($query);
	$rc = $sth->execute();
	#extrae el idcarga de categorias
	@row = $sth->fetchrow_array;
	$id_CATPROD = @row[0];

    if($id_CATPROD==$idcarga){
     	&wtlog("Insert CATPROD","carga_to_trp",0);
	    # copia catergorias
        $qry = "INSERT INTO trp_catprod (cod_cat,cod_cat_padre,descat,cat_nivel,cat_tipo,car_estadocarga,id_cod_error,id_cadena) 
        SELECT cod_cat,cod_cat_padre,descat,cat_nivel,cat_tipo,0,NULL,id_cadena FROM car_catprod WHERE idcarga=$idcarga";
        if (exec_sql_non_select($qry)<=0) {$flag=1;}
    }else{
        $flag_CATPROD=1;
    }
    #revisa los id de carga de productos */
    $query="select MAX(idcarga) from car_productos";
	$sth = $dbh->prepare($query);
	$rc = $sth->execute();
	#extrae el idcarga de categorias
	@row = $sth->fetchrow_array;
	$id_PRODUCTOS = @row[0];

    if($id_PRODUCTOS==$idcarga){
        &wtlog("Insert PRODUCTOS","carga_to_trp",0);
        # copia productos
        $qry = "INSERT INTO trp_productos (cod_prod1,cod_prod2,des_corta,des_larga, cod_categ, cod_propal, car_estadocarga, id_cod_error, cod_categ_ant, id_cadena,prod_tipo,prod_subtipo,stock_proveedor) 
        SELECT cod_prod1, cod_prod2, des_corta, des_larga, cod_categ, cod_propal,0,NULL,NULL, id_cadena,prod_tipo,prod_subtipo,stock_proveedor FROM car_productos WHERE idcarga=$idcarga and estado=1 ;";
        if (exec_sql_non_select($qry)<=0) {$flag=1;}
    }else{
        $flag_PRODUCTOS=1;
    }
    
    #revisa los id de carga de proveedores */
    $query="select MAX(idcarga) from car_proveedores";
	$sth = $dbh->prepare($query);
	$rc = $sth->execute();
	#extrae el idcarga de categorias
	@row = $sth->fetchrow_array;
	$id_PROVEEDORES = @row[0];

    if($id_PROVEEDORES==$idcarga){
        &wtlog("Insert PROVEEDORES","carga_to_trp",0);
        # copia proveedores
            $qry = "INSERT INTO trp_proveedores (codprov,rutprov,nomprov,razsoc,fonocto,nombcto,emailcto,car_estadocarga) 
            SELECT codprov,rutprov,nomprov,razsoc,fonocto,nombcto,emailcto,0 FROM car_proveedores WHERE idcarga=$idcarga;";
            if (exec_sql_non_select($qry)<=0) {$flag=1;}
    }else{
        $flag_PROVEEDORES=1;
    }	

    #revisa los id de carga de prodprov */
    $query="select MAX(idcarga) from car_prodxprov";
	$sth = $dbh->prepare($query);
	$rc = $sth->execute();
	#extrae el idcarga de categorias
	@row = $sth->fetchrow_array;
	$id_PRODPROV = @row[0];

    if($id_PRODPROV==$idcarga){
        &wtlog("Insert PROD x PROVEEDORES","carga_to_trp",0);
        # copia prodxprov
            $qry = "INSERT INTO trp_prodxprov (cod_prod1,cod_prov,car_estadocarga, id_cadena) 
            SELECT cod_prod1,cod_prov,0, id_cadena FROM car_prodxprov WHERE idcarga=$idcarga;";
            if (exec_sql_non_select($qry)<=0) {$flag=1;}
    }else{
        $flag_PRODPROV=1;
    }

    #revisa los id de carga de codbarra */
    $query="select MAX(idcarga) from car_codbarra";
	$sth = $dbh->prepare($query);
	$rc = $sth->execute();
	#extrae el idcarga de categorias
	@row = $sth->fetchrow_array;
	$id_CODBARRA = @row[0];

    if($id_CODBARRA==$idcarga){
        #copia codbarra  01-08-2005
        &wtlog("Insert COD BARRA PRODUCTOS","carga_to_trp",0);
        # copia prodxprov
        $qry = "INSERT INTO trp_codbarra(cod_prod1,cod_barra,tip_codbar,cod_ppal,unid_med,car_estadocarga, id_cod_error, id_cadena) 
        SELECT cod_prod1, cod_barra, tip_codbar, cod_ppal,unid_med,0, NULL, id_cadena FROM car_codbarra WHERE idcarga=$idcarga;";
        if (exec_sql_non_select($qry)<=0) {$flag=1;}	
    }else{
        $flag_CODBARRA=1;
    }
    
    if ($flag) {
        &welog("Los registros no pueden ser cargados","carga_to_trp",-2);
        print  "MALA la carga";
        $dbh->rollback;
        }
    else {
        &wtlog("Los registros cargados con exito","carga_to_trp",0);
        $dbh->commit;
        }
	
    # AutoCommit => 1
    $dbh->{AutoCommit} = 1;
	}#carga_to_trp

	
# ***************************************************************************
sub main {

    my $config   = read_config();

    carga_to_trp();
	
    db_disc();
}# main
	
main();
