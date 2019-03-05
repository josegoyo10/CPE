#!/usr/bin/env perl -w

#use strict;
use AppConfig qw( :expand );

#definicion de constantes
use constant ARCH_CONFIG => "/var/www/html/centroproy2/procesos/cfg/cproy.cfg";

use constant ERR_DB_CHILD_FK_REF => 1216; #<1216>-<Cannot add or update a child row: a foreign key constraint fails>
use constant ERR_DB_COL_NOT_EXIST => 1054; #No existe una columna 
use constant ERR_DB_DUP => 1062;   #<1062>-<Duplicate entry '01-1' for key 1>
use constant ERR_DB_SQL_SYNTAX => 1064;    #1064

#var globales

#------------------------------------------------------------------------
# read_config($file)
#
# Handles reading of config file and/or command line arguments.
#------------------------------------------------------------------------

sub read_config {
    local $file;
    local $log_max_days;
    
    $file = ARCH_CONFIG;

    my $config = AppConfig->new(
    	{ ERROR  => sub { die @_, "\t try again\n" }   }, 
		'general_db=s',
		'general_host=s',	
		'general_user=s',	
		'general_pass=s',	
		'general_trace=s',	
		'general_log_days_max=s',
		'pc1_path_load=s',
		'pc1_path_load_proc=s',
		'pc1_path_load_maes=s',
		'pc1_extensionma=s',
        'pc1_extensionpr=s',
		'pc1_extension_vrf=s',
		'pc1_pre_prov=s',
		'pc1_pre_prod=s',
		'pc1_pre_cat=s',
		'pc1_pre_prpv=s',
		'pc1_esc=s',
		'pc1_txt=s',
		'pc1_sep=s',
		'pp1_path_files=s',
		'pp1_promo_prefix=s',
		'pp1_delta_min=s', 
		'pp2_path_files=s',
		'pp2_promo_prefix=s'
		);

    # process main config file, then command line args
    $config->file($file) if -f $file;
    $config->args();
    
	my $Database = $config->general_db;
	my $Host = $config->general_host;
	my $User = $config->general_user;
	my $Pass = $config->general_pass;
	
	#Establece conexion a la base de datos
	db_connect($Database, $Host,$User, $Pass );

	&wtlog("general_db=".$config->general_db.",
		general_host=".$config->general_host.",
		general_user=".$config->general_user.",
		general_pass=".$config->general_pass.",
		general_trace=".$config->general_trace.",	
		general_log_days_max=".$config->general_log_days_max.",
		pc1_path_load=".$config->pc1_path_load.",
		pc1_path_load_proc=".$config->pc1_path_load_proc.",
        pc1_path_load_maes=".$config->pc1_path_load_maes.",
		pc1_extensionma=".$config->pc1_extensionma.",
        pc1_extensionpr=".$config->pc1_extensionpr.",
		pc1_extension_vrf=".$config->pc1_extension_vrf.",
		pc1_pre_prov=".$config->pc1_pre_prov.",
		pc1_pre_prod=".$config->pc1_pre_prod.",
		pc1_pre_cat=".$config->pc1_pre_cat.",
		pc1_pre_prpv=".$config->pc1_pre_prpv.",
		pc1_esc=".$config->pc1_esc.",
		pc1_txt=".$config->pc1_txt.",
		pc1_sep=".$config->pc1_sep.",
		pp1_path_files=".$config->pp1_path_files.",
		pp1_promo_prefix=".$config->pp1_promo_prefix.",
		pp1_delta_min=".$config->pp1_delta_min.",
	    pp2_path_files=".$config->pp2_path_files.",
		pp2_promo_prefix=".$config->pp2_promo_prefix,"read_config",0);
	
	#Elimina los registros posteriores a los dias entregados
	$log_max_days = $config->general_log_days_max;
	if ($log_max_days>0){
		EliminaLogs($log_max_days);
		}	
	#Establece trace si es 1
	$Debug = $config->general_trace;
    $config;
}


#  subrutinas de carga de datos en BD
# ----------------MySQL----------------------------------------
sub db_connect{
	local ($db,$host,$user,$pass)=@_;
	local $dsn;
	
	
	#$dsn = "dbi:mysql:database=IRSPROMO;host=192.168.0.31;mysql_client_found_rows=true";
	$dsn = "dbi:mysql:database=$db;host=$host;mysql_client_found_rows=true";
	$user = $user;
	$password = $pass;

   #&wtlog("Conectando a $dsn con <$user/*****>", "db_connect",0);
   $dbh = DBI->connect($dsn, $user, $password, { RaiseError => 0, PrintError => 0, AutoCommit => 1 });
   
   if (!defined($dbh)) {
   	   printf "ERROR: No puede conectarse a la base de datos ,error : $DBI::err  - $DBI::errstr";
       exit 0;
       }
	&wtlog ("Conectado...$dbh", "db_connect",0);
}

sub exec_sql_non_select {
local ($qrysql, $id_retornado, $cod_errDBI) = @_;
local $rc;

	if (!defined($dbh)) {
		&wtlog("Error de conexion de la base de datos ERROR: rc=".$DBI::err." - ".$DBI::errstr);
		return -1;
		}
	eval { $rc = $dbh->do($qrysql); };
	if ($@) {
		&wtlog("$@");	
		if ($DBI::err!="") {		
			if ($DBI::err== ERR_DB_SQL_SYNTAX) {
				&welog("Error de aplicacion, error en la sintaxis :$DBI::errstr","exec_sql_non_select",$DBI::err);
				}			
			elsif ($DBI::err== ERR_DB_COL_NOT_EXIST) {
				&welog("Error de aplicacion, no existe la columna en la tabla : $DBI::errstr","exec_sql_non_select",$DBI::err);
				}			
			else {
				&welog("Error en la Base de datos: $DBI::errstr","exec_sql_non_select",$DBI::err);
				}			
			db_disc();	
			exit 0;
			}
		}		
	$$id_retornado = $dbh->{'mysql_insertid'};
	$$cod_errDBI = $DBI::err;
	if ($rc<0) {
		&welog("ERROR DB: sql=<$qrysql> BD:<$DBI::errstr>","exec_sql_non_select",$DBI::err);
		return -1;		
		}
	elsif ($rc==0) {
		&wtlog("0 registros actualizados:: sql=<$qrysql>","exec_sql_non_select",$DBI::err);
		}
	else {	
		&wtlog("Lineas intervenidas=$rc sql=<$qrysql>","exec_sql_non_select",0);
		}
	return $rc; #nro_lineas
}

sub db_disc() {
	&wtlog("Cierra la base de datos($rc)","db_disc",0);
	$rc = $dbh->disconnect;
	}

#elimina logs cada $dias
sub EliminaLogs() {
	local $dias = shift;
	local $qry;
	if ($dias>0) {
		$qry ="DELETE FROM log_table WHERE DATEDIFF(now(),fecha)";
		exec_sql_non_select($qry);
		}
	else {
		&welog("Los dias de la configuracion para borrar el log de programas no es valido", "EliminaLogs",0);
		}
	
	}

#nuevas funciones open logs
sub welog {
	local ($msg, $funcion, $cod_err)=@_;
	escribe_log("ERROR", $msg, $funcion, $cod_err);
	}
#nuevas funciones open logs
sub wtlog {
	local ($msg, $funcion, $cod_err)=@_;
	escribe_log("trace", $msg, $funcion, $cod_err);	
	}	
	
sub escribe_log {
	local ($tipo, $msg, $funcion, $cod_err)=@_;
	local ($qry);
	
	$msg =~ s/\\/\\\\/g; # reemplaza backslash por "\\", escapandolas	
	$msg =~ s/'/\\'/g; # reemplaza comillas simples por "\'", escapandolas	
	

	if (!defined($dbh)) {
		printf "escribe_log:Error de conexion de la base de datos ERROR: rc=".$DBI::err." - ".$DBI::errstr;
		exit 0;
		}
	if (($tipo eq "trace")&&($Debug == 0)) {
		return;
		}

	$qry = "INSERT INTO log_table(programa,tipo_log, fecha,subrutina,texto, cod_err) 
	VALUES ('$0','$tipo',now(),LOWER('$funcion'),LOWER('$msg'), '$cod_err' )";
	$dbh->do($qry);
	if ($DBI::err!="") {
		#no puede escribir en el log
		printf "ERROR BD $DBI::err al escribir en el log : $DBI::errstr\n";
		exit 0;
		}
	}#escribe log
	
sub tiempo_log() {
    local $aux;
    ($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst)=localtime(time);
    $aux = sprintf "%4d-%02d-%02d %02d:%02d:%02d",$year+1900,$mon+1,$mday,$hour,$min,$sec;
    return $aux;
    }    
        
#sub tiempo_arch() {
#    local $aux;
#    ($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst)=localtime(time);
#    $aux = sprintf "%02d%02d%4d",$mon+1,$mday,$year+1900;
#    return $aux;
#    }
### RGM 21-11-2005 la fn ahora permite la entrada de un offset que indica los dí máo los dí menos
sub tiempo_arch {
    local ($offsetday) = @_;
    local($seg,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst);
    local ($aux, $seg_dia);
    $seg_dia = 86400;
    ($seg,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst)=localtime(time()+$offsetday*$seg_dia);
    $aux = sprintf "%02d%02d%4d",$mon+1,$mday,$year+1900;
    return $aux;
    }

# ----------------------------------------------------------------------------------------------------
# BuscaSecuencia
#  entrada : ()
#  salida : ($sec_num)
# ----------------------------------------------------------------------------------------------------
sub BuscaSecuencia {
local $sec_num;
local ($qry , $st , $rc);

	$qry = "select MAX(arch_pos) FROM secuencia";
	$st = $dbh->prepare($qry);
	$rc = $st->execute();
	if (($st->err)>0) {
		&welog("BUSCA SEQ : secuencia Err:<$DBI::err>-<$DBI::errstr>","BuscaSecuencia",0);
		return 0;
		}
	if (($sec_num)=$st->fetchrow_array ) {
	
		&wtlog("Secuencia = $sec_num","BuscaSecuencia",0);
		
		if (($sec_num == 0) || ($sec_num =="")){$qry = "INSERT INTO secuencia (arch_pos) VALUES (1)";}
		else {
			$sec_num=$sec_num+1;
			&wtlog("BUSCA SEQ : Secuencia before update= ".$sec_num,"BuscaSecuencia",0);
			$qry = "UPDATE secuencia SET arch_pos = $sec_num";
			}
		
		if (exec_sql_non_select($qry)<=0) { return; }
		return ($sec_num+1);
		}
	}
#verifica si existe el valor en un arreglo retorna TRUE	
sub existe_en_arr{
	local ($valor, @arreglo)=@_;
	local $flag=0;
	foreach $x (@arreglo) {
		wtlog("promocion $x valor=$valor","existe_en_arr",0);
		if ($x == $valor) {
			$flag=1;
			last;
			}
		}
	return $flag;
	}
#para efectos de debuggin
sub muestra_arr{
	local @arreglo=@_;
	local $i;
	$i=0;
	foreach $x (@arreglo){
		printf "Valor $i:$x\n";
		&wtlog("VALOR $i:$x","muestra_ar",0);
		
		$i++;
		}
	return $flag;	
}	
1; # return true
