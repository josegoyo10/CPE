# ------------------------------------------------------------
# - PC1 : Carga de datos al modelo tempral de promociones
# -
# - Por Gisela Estay Jeldes
# - BBR-ingenieria
# - ----------------------------------------------------------
#!/usr/bin/env perl -w

use DBI;
#require "/home/desa/irspromo/general.pl";
require "/var/www/html/centroproy2/procesos/general.pl";
require Text::CSV_XS;

###############################################################
# carga_arch_generico 
###############################################################
sub carga_arch_generico {
    local ($entidad,$Path_load,$Path_load_proc,$Path_name, $Extension) = @_;  # entidad INV
    
    local $i=1;
    local $j=0;
    local $z=0;

    local $flag=0;
    local $campo;
    local $rc=0;
    local $linea;    
    local @a;
    
    local $max_arr;
    local @largos;
    local @campos;
    local $tit;
    local $fname = "$Path_name".tiempo_arch()."$Extension";
    local $archivo_carga = "$Path_load/$fname";
    local $bad_argument, $status;
    #wtlog("quote_char=<$CSV_txt>  escape_char=<$CSV_esc>  sep_char=<$CSV_sep>");
  
    local $csv = Text::CSV_XS->new({  'binary'      => 1 	});    
      
    # datos de campos productos  max_arr= TOTAL-1 (0 - Total-1 = TOTAL)    
   	if ($entidad eq "INV") {
	#inventario
		$tit = "INVENTARIO";	
		$max_arr = 3;    #solo se usan los 4 primeros campos
		@largos = (10,5,20,12);
		@campos = ("FECHA","COD_PROD1","COD_LOCAL","STOCK");
		}
    else {
	   # &exit_error("Entidad de carga erronea, revise la configuración [PRO|PRV|CAT|PXP] : $entidad", "1100", "carga_arch_generico");
		}
	{              
    wtlog("entidad de carga :$tit","carga_arch_generico",0);
    wtlog("archivo de carga :$archivo_carga","carga_arch_generico",0);
    wtlog("n campos :$max_arr+1","carga_arch_generico",0);
    }
    #implementar genera error
    if (!open (ARCH, $archivo_carga)) {
		&welog("No puede abrir el archivo <$archivo_carga>","carga_arch_generico",-1);
		return;
		}
    &wtlog("--- CARGA $tit ---","carga_arch_generico",0);
    while ($linea=<ARCH>) {
    &wtlog (" ----  LINEA $linea --- ");    
	$linea =~ s/\\/\\\\/g; # reemplaza backslash por "\\", escapandolas	siempre primero
	$linea =~ s/'/\\'/g; # reemplaza comillas simples por "\'", escapandolas	segundo
	
	# parser zone begin
if ($entidad ne "PRE") {
    	$status = $csv->parse($linea);
	if ($status==0) {
		$bad_argument = $csv->error_input();	
		&welog("$tit [$i]: El parseador fallo ($status) en :$bad_argument","carga_arch_generico",-2);
	        $i++;
		next;
		}
	@a = $csv->fields();	
	# parser zone ends	
	
#antigua forma
#chop($linea);		
#chop($linea);
#$linea =~ s/"//g;  #saca comillas dobles
#$linea =~ s/'/\\'/g; # reemplaza comillas simples por "\'", escapandolas
#@a = split(/,/, $linea);
	
    #Validar que los largos de campos correspondan
	$flag=0;
	for ($j=0;$j<=$max_arr;$j++) {
		if ( (length @a[$j] )>@largos[$j] ) {
		$flag=1;
		&welog("$tit [$i]: Campo @campos[$j]=<@a[$j]> de largo ".(length @a[$j])."> maximo [@largos[$j]]",
		"carga_arch_generico",-3);
		#&wtlog("$tit [$i]:".@campos[$j]." :<".@a[$j].">\t<--- ERROR Largo (".(length @a[$j]).") > max [@largos[$j]]");
		}
	    else {
			&wtlog("$tit [$i]:".@campos[$j]." :<".@a[$j].">");}
	    } #for
     }#si no es precio	   


	if ($flag) { 
	    $z++; 
	    &welog ("$tit [$i]: El formato del registro es erroneo.","carga_arch_generico",-4);	
	    }
	else {
		if ($entidad eq "INV") {    
             $rc = &myinventario(@a);
			}            
	    }
        $i++;
        }# while
    if ($z>0){
        &welog("$tit : registros erroneo(s) = $z , total(es) = ".($i-1),"carga_arch_generico",-5);	
        }

    #Movemos el archivo procesado a la carpeta $Path_load_proc
    rename ("$Path_load/$fname","$Path_load_proc/$fname");

}#fin mprov

###############################################################
# myinventario
###############################################################
sub myinventario(@a) {
    my @a = @_;
    local $sth;
    local $qsql;

   if (!defined($dbh)) {
       &wtlog("ERROR: rc=".$DBI::err." - ".$DBI::errstr, "myinventario",-1); 
       return -1;
       }
 	
   $qsql = "INSERT INTO car_precios( cod_prod1, cod_local, stock, id_carga, id_cadena) 
   VALUES ('@a[2]', '@a[1]', '@a[3]',  $idcarga, $Id_cadena);";
   if ($rc = exec_sql_non_select($qsql)<=0) { 
   	welog("ERROR DB: sql=<$qsql> err:<$DBI::err>-<$DBI::errstr>", "myinventario",-2); 
   	}
   return $rc;
   }
###############################################################
# nueva_carga
###############################################################

sub nueva_carga() {
local $query;
local $rc;

if (!defined($dbh)) {
       &wtlog("ERROR: rc=".$DBI::err." - ".$DBI::errstr,"nueva_carga",-1);
       return -1;
       }

#genera un idcarga nuevo N: Nuevo A: Actual (al termino de carga) C: Caduco 
$query = "INSERT INTO car_estadocarga (fechacarga, estado) VALUES (Now(), 'N')";

$rc = $dbh->do($query);
$idcarga = $dbh->{'mysql_insertid'};
#$idcarga = $dbh->last_insert_id();
}
###############################################################
# borra_carga_err
###############################################################

# Usada cuando hay un error grave en el procesamiento
# ej: no carga los archivos
sub borra_carga_err() {
    $qry = "DELETE from car_estadocarga WHERE estado ='N'";
    $rc = $dbh->do($qry);
    &wtlog("Elimina carga erronea QRY1:$qry rc=$rc","borra_carga_err",0);
	}
###############################################################
# sub termina_carga
###############################################################

sub termina_carga() {
local $qry;
local $rc;

    # AutoCommit => 0
    $dbh->{AutoCommit} = 0;
    
    #los idcarga en estado A (actual) ->  C (Caducos)
    $qry = "UPDATE car_estadocarga SET estado = 'C' WHERE estado ='A'";
    $rc = $dbh->do($qry);
    &wtlog("Termina carga QRY1:$qry rc=$rc","termina_carga",0);
    
    #el idcarga en estado N (nuevo) - > A (actual)
    $qry = "UPDATE car_estadocarga SET estado = 'A', fechafincarga=now() WHERE estado ='N'";
    $rc = $dbh->do($qry);
    &wtlog("Termina carga QRY2:$qry rc=$rc","termina_carga",0);
    
    #commit o rollback
    &wtlog("Error DB :$err - $errstr","termina_carga",-1);
    if ($err==0) { $rc = $dbh->commit();}
    else         {  $rc = $dbh->rollback();
    print "MALA LA CARGA";
    }

    # AutoCommit => 1
    $dbh->{AutoCommit} = 1;
    }
###############################################################
# sub exit_error
###############################################################
sub exit_error() {
	local ($msg, $code, $subr) = @_;
	
	&welog("code:<$code>:=: funcion:$subr :=: $msg","exit_error",-1);
	borra_carga_err();
	db_disc();
	exit 0;
    }
    
###############################################################
    
# ------- p r i n c i p al -----------------------------------
###############################################################

sub main {
    #config
    local $i;
    my $config   = read_config();
	my $Path_load   = $config->pc1_path_load_maes;
    my $Path_load_proc  = $config->pc1_path_load_proc;
	my $Extension   = $config->pc1_extensionma;
	my $Tipo;

    &wtlog("inicia programa $0...","MAIN","COMIENZO");	
    # genera nueva carga on su id
    nueva_carga();
    
    $qry = "SELECT prefijo, id_cadena, tipo FROM archivos WHERE tipo ='INV'";
    $sth = $dbh->prepare($qry);
    $rc = $sth->execute();
    $i=1;
    while(($Prefijo, $Id_cadena, $Tipo) = $sth->fetchrow_array){
	&wtlog("$i Procesa Archivo prefijo=<$Prefijo> idcadena=<$Id_cadena> Tipo=<$Tipo>","MAIN","COMIENZO");	
	carga_arch_generico($Tipo,$Path_load,$Path_load_proc,$Prefijo,$Extension);
	$i++;
    }
    
    #termina_carga();
    &wtlog("finaliza programa $0...","MAIN","FIN");
    db_disc();
} # fin main

main();
