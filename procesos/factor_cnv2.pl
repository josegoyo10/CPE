# ------------------------------------------------------------
# - PC2 : Traspaso de datos y Validacion de precios 
# -
# - Por Gisela Estay
# - bbr ingenierìa
# - ----------------------------------------------------------
#!/usr/bin/env perl -w

use DBI;
require "/var/www/html/centroproy2/procesos/general.pl";

use constant ERR_DB_CHILD_FK_REF => 1216;	#<1216>-<Cannot add or update a child row: a foreign key constraint fails>
use constant ERR_DB_COL_NOT_EXIST => 1054;	#No existe una columna 
use constant ERR_DB_DUP => 1062;			#<1062>-<Duplicate entry '01-1' for key 1>
use constant ERR_DB_SQL_SYNTAX => 1064;		#1064

# ***************************************************************************
#                            carga_to_trp 
# ---------------------------------------------------------------------------
# Levanta las cargas de archivos a las tablas detraspaso, usa el ultimo id
# ***************************************************************************
sub carga_to_trp {
local $qry, $rc, $sth, $qsql, $flag, $total_reg, $count_reg;
local $max_reg = 100000; 
	#lee id de carga nuevo
	$qry = "SELECT idcarga FROM car_estadocarga WHERE estado='I' order by 1 desc limit 0,1";
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
	$qry = "TRUNCATE TABLE trp_codbarra;";
	exec_sql_non_select($qry);	
    $flag =0;
    # AutoCommit => 0
    $dbh->{AutoCommit} = 0;
    	
    &wtlog("Insert PRECIOS","carga_to_trp",0);
	# copia PRECIOS

	$qry = "SELECT count(*) FROM car_codbarra WHERE idcarga = $idcarga";
	$sth = $dbh->prepare($qry);
	$rc = $sth->execute();
	if (($sth->err)>0) {
		&welog("ERROR en cuenta de registros a traspaso Err:<$DBI::err>-<$DBI::errstr>","carga_to_trp",-1);
		return 0;
	}
	#extrae numero de registros
	@row = $sth->fetchrow_array;
	$total_reg = @row[0];
	
	$count_reg = 0; 
	if ($total_reg) {
		while ($count_reg <= $total_reg ) {

			$qry = "INSERT INTO trp_codbarra (cod_prod1,unid_med, id_cadena, factor_conv,car_estadocarga) 
					SELECT cod_prod1, unid_med, id_cadena, factor_conv,0
					FROM car_codbarra 
					WHERE idcarga = $idcarga
					LIMIT $count_reg, $max_reg;";
			$rc = exec_sql_non_select($qry); 
			if ($rc <= 0) {$flag=1;}
			# Si estan todos ok, commit or else....rollback
			if ($flag) {
				&welog("Los registros no pueden ser cargados, rc = [$rc], qry = [$qry]","carga_to_trp",-2);
				print  "MALA la carga";
				$dbh->rollback;
			}
			else {
				&wtlog("Los registros cargados con exito","carga_to_trp",0);
				$dbh->commit;
			}
			$count_reg += $max_reg; 
		}	
	}
	else {
		&wtlog("No hay registros para insertar","carga_to_trp",0);
	}

    # AutoCommit => 1
    $dbh->{AutoCommit} = 1;
}#carga_to_trp
	
################################## procesa_UpdPrecios ################################################################
sub procesa_UpdPrecios{ 
local $i, $z, $local_ant;
local $qry, $qry2, $qryupd, $qryins;
local $sth1, $rc1;
local $err_str, $err_code ;
local $cod_prod1, $cod_local, $Ffactor_conv,$prec_costo,$prec_costo, $stock ,$TCcod,$PRcod;
local $id_producto,$id_local;
   
	wtlog("Inicia proceso","procesa_UPDprecios",0);

    # por cada registro en el modelo TRP debe almacenar o actualizar (si existe el codigo) sus campos
    $qry = "select TC.factor_conv 'Ffactor_conv',c.id_producto, TC.unid_med, TC.cod_prod1 'TCcod',TC.id_cadena
			from trp_codbarra TC
			join codbarra c on (c.cod_prod1 = TC.cod_prod1)
			where TC.car_estadocarga = 0";
	$sth = $dbh->prepare($qry);
    $rc = $sth->execute();
     
    if (($sth->err)>0) {
		&welog("Listado de TRP codbarra Err:<$DBI::err>-<$DBI::errstr>");
		return 0;
		}

	$i = 1;
	$z = 0;
	$local_ant = '';
    while(($Ffactor_conv,$id_producto,$unid_med,$TCcod) = $sth->fetchrow_array) {

        #saca ' comillas simples a la descripcion
		$prec_costo=~ s/'/\\'/g;
		$prec_costo=~ s/'/\\'/g;
		$stock=~ s/'/\\'/g;
		$err_str = "";
		$qryupd = " UPDATE codbarra 
					SET factor_conv= $Ffactor_conv
					WHERE id_producto = $id_producto and unid_med =  '$unid_med'"; 
	    exec_sql_non_select($qryupd);
       $i++;
   }#while

   $qryupe = "
		INSERT INTO log_table (programa, tipo_log, fecha, subrutina, texto, cod_err)
		SELECT distinct 'factor_cnv2.pl', 'ERROR', now(), 'procesa_factor', concat('El producto ', cod_prod1, ' no existe en el catalogo. No se puede actualizar su factor ', Ffactor_conv), 0
		FROM trp_codbarra WHERE car_estadocarga = 0 ";
   if (exec_sql_non_select($qryupe)<0) { #fracaso en UPDATE
       &welog("Error $DBI::err al insertar registros en log_table","procesa_factor",0);
   }

	wtlog("Finalizado procesados=".($i-1).", erroneos=$z","procesa_factor",0);
	return 1;
}



# ***************************************************************************
sub trp_centro_proyectos {
	if (procesa_UpdPrecios()) {		
		&wtlog(" factores de Productos procesados","trp_centro_proyectos",0);
	}
}

# ***************************************************************************
sub main {
    my $config   = read_config();

    #tipo de logs
    &wtlog("inicia programa $0...","MAIN","COMIENZO");	
    
    #proceso principal
    carga_to_trp();
    trp_centro_proyectos();
	
	&wtlog("finaliza programa $0...","MAIN","FIN");
	
    #proceso principal	
    db_disc();
	}# main
	
main();
