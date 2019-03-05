# ------------------------------------------------------------
# - PC1 : Carga de datos al modelo tempral de promociones
# -
# - Por Gisela Estay Jeldes
# - BBR-ingenieria
# - ----------------------------------------------------------
#!/usr/bin/env perl -w

use DBI;
#require "/home/desa/irspromo/general.pl";
require "/var/www/html/centroproy/procesos/general.pl";
require Text::CSV_XS;

###############################################################
# carga_arch_generico 
###############################################################
sub carga_arch_generico {
    local ($entidad,$Path_load,$Path_load_proc,$Path_name, $Extension) = @_;  # entidad PROV - PROD - CATE - PRPV - EANC
    
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
    local $fname = "$Path_name".tiempo_arch(-1)."$Extension";
    local $archivo_carga = "$Path_load/$fname";
    
    local $bad_argument, $status;

    #wtlog("quote_char=<$CSV_txt>  escape_char=<$CSV_esc>  sep_char=<$CSV_sep>");
  
    local $csv = Text::CSV_XS->new({  'binary'      => 1 	});    
      
  #  local $csv = Text::CSV_XS->new({     'quote_char'  => '"',     'escape_char' => '"',     
  #  'sep_char'    => ',',     'binary'      => 1 	});    
  
    # datos de campos productos  max_arr= TOTAL-1 (0 - Total-1 = TOTAL)    
    if ($entidad eq "PRO") {
		#productos 
		$tit = "PRODUCTO";
		$max_arr = 15;
		@largos = (20,20,40,200,100,10,20,20,20,20,20,20,20,20,20,20);
		@campos = ("COD_PROD1","COD_PROD2","DES_CORTA","DES_LARGA","ESTADO","COD_CATEG","MARCA","COD_PPAL",
		"ORIGEN","PROD_TIPO","PROD_SUBTIPO","STOCK_PROVEEDOR","Atrib7","Atrib8","Atrib9", "Atrib10");
		}
    elsif ($entidad eq "PRV") {    
		# proveedores    
		$tit = "PROVEEDOR";	
		$max_arr = 8;        
		@largos = (20,20,30,60,20,50,30,20,30);
		@campos = ("COD_PROV","RUTPROV","NOMPROV","RAZSOC","FONOCTO","NOMBCTO","EMAILCTO","COD_SURT","DES_SURT");
		}
    elsif ($entidad eq "CAT") {   
		#categorias
		$tit = "CATEGORIA";
		$max_arr = 4;    
		@largos = (10,10,100,1,1);
		@campos = ("COD_CAT","COD_CAT_PADRE","DESCAT","CAT_NIVEL","CAT_TIPO");
		}
    elsif ($entidad eq "PXP") {    
	    #prodxprov
		$tit = "PRODxPROV";	
		$max_arr = 1;    #solo se usan los 2 primeros campos
		@largos = (20,20,20,20);
		@campos = ("COD_PROD1","COD_PROV","COD_SURT","COD_PROD2");    
		}
	elsif ($entidad eq "EAN") {
	    #codbarra
		$tit = "COD_BARRA";	
		$max_arr = 4;    
		@largos = (20,14,3,1,3);
		@campos = ("COD_PROD1","COD_BARRA","TIP_CODBAR","COD_PPAL", "UNID_MED");
		}
	elsif ($entidad eq "INV") {
	    #inventario
		$tit = "INVENTARIO";	
		$max_arr = 4;    #solo se usan los 5 primeros campos
		@largos = (20,10,10,10,12);
		@campos = ("COD_PROD1","COD_LOCAL","PREC_COSTO","PREC_VALOR","STOCK");
		}
    else {
	   # &exit_error("Entidad de carga erronea, revise la configuración [PRO|PRV|CAT|PXP] : $entidad", "1100", "carga_arch_generico");
		}

	{              
    wtlog("entidad de carga :$tit","carga_arch_generico",0);
    wtlog("archivo de carga :$archivo_carga","carga_arch_generico",0);
    wtlog("n campos :$max_arr+1","carga_arch_generico",0);
    #wtlog("quote_char=<$CSV_txt>  escape_char=<$CSV_esc>  sep_char=<$CSV_sep>");
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
	    else {&wtlog("$tit [$i]:".@campos[$j]." :<".@a[$j].">");}
	    } #for
     }#si no es precio	   


	if ($flag) { 
	    $z++; 
	    &welog ("$tit [$i]: El formato del registro es erroneo.","carga_arch_generico",-4);	
	    }
	else {
		if ($entidad eq "PRO") {
		     $rc = &myprod(@a);
			}
		elsif ($entidad eq "PRV") {    
		     $rc = &myprov(@a);
			}
		elsif ($entidad eq "CAT") {
	             $rc = &mycat(@a);
			}
		elsif ($entidad eq "PXP") {    
	             $rc = &myprodprov(@a);
			}
		elsif ($entidad eq "EAN") {    
	             $rc = &mycodbarra(@a);
			}
		elsif ($entidad eq "INV") {    
	             $rc = &myinventario(@a);
			}            
	   #$rc = &myprod(@a);
	   #if ($rc<0) {&welog ("PRODUCTO[$i]: Falló la carga del registro en la base de datos ($errstr)"); }
	   #       else {&wtlog ("PRODUCTO[$i]: El registro fue insertado rc=$rc"); }
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
# mycodbarra
###############################################################
sub mycodbarra(@a) {
    my @a = @_;
    local $sth;
    local $qsql;

   if (!defined($dbh)) {
       &wtlog("ERROR: rc=".$DBI::err." - ".$DBI::errstr, "mycodbarra",-1); 
       return -1;
       }
	if ((@a[2] eq "I6")||(@a[2] eq "IC")) {
		&wtlog("no procesa registros con TIPO CODIGO BARRA = I6 o IC", "mycodbarra",0); 
		return 0;
		}
   	
   $qsql = "INSERT INTO car_codbarra (cod_prod1, cod_barra, tip_codbar, cod_ppal, unid_med, idcarga, id_cadena) 
   VALUES ('@a[0]', '@a[1]', '@a[2]', '@a[3]', '@a[4]', $idcarga, $Id_cadena);";
   if ($rc = exec_sql_non_select($qsql)<=0) { 
   	welog("ERROR DB: sql=<$qsql> err:<$DBI::err>-<$DBI::errstr>", "mycodbarra",-2); 
   	}
   return $rc;
   }
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
 	
   $qsql = "INSERT INTO car_inventario(cod_prod1, cod_local, prec_valor, prec_costo,stock, idcarga, id_cadena) 
   VALUES ('@a[2]', '@a[1]', '@a[9]', '@a[10]','@a[3]', $idcarga, $Id_cadena);";
   if ($rc = exec_sql_non_select($qsql)<=0) { 
   	welog("ERROR DB: sql=<$qsql> err:<$DBI::err>-<$DBI::errstr>", "myinventario",-2); 
   	}
   return $rc;
   }
###############################################################
# myprov
###############################################################

sub myprov(@a) {
    my @a = @_;
    local $sth;
    local $qsql;

   if (!defined($dbh)) {
       &wtlog("ERROR: rc=".$DBI::err." - ".$DBI::errstr, "myprov",-1); 
       return -1;
       }
       
   $qsql = "INSERT INTO car_proveedores (codprov, rutprov, nomprov, razsoc, fonocto, nombcto, emailcto, idcarga) VALUES ('@a[0]', '@a[1]', '@a[2]', '@a[3]', '@a[4]', '@a[5]', '@a[6]', $idcarga);";
   $sth = $dbh->prepare($qsql);
   $rc = $sth->execute();
   if ($rc<=0) {
        &welog("ERROR DB: sql=<$qsql> err:<$DBI::err>-<$DBI::errstr>", "myprov",-2);
        }
   return $rc;
   }
###############################################################
# myprod
###############################################################
sub myprod(@a) {
my @a = @_;
local $sth;
local $qsql;

#Para evitar el ingreso de comilla extrana
@a[2] =~ s/\x94/\"/g;
@a[3] =~ s/\x94/\"/g;

   if (!defined($dbh)) {
       &wtlog("ERROR: rc=".$DBI::err." - ".$DBI::errstr, "myprod",-1);
       return -1;
       }
    #el estado indica 0 para los productos inactivos que NO deben ser cargados.
	if (!@a[4]) {
		return 0;
		}
$qsql = "INSERT INTO car_productos (cod_prod1, cod_prod2, des_corta, des_larga, estado, cod_categ,cod_propal,idcarga, id_cadena,prod_tipo,prod_subtipo,stock_proveedor) 
VALUES ('@a[0]', '@a[1]', '@a[2]', '@a[3]', '@a[4]', '@a[5]', '@a[7]',$idcarga, $Id_cadena, '@a[9]','@a[10]','@a[11]');";


$sth = $dbh->prepare($qsql);
$rc = $sth->execute();
	if ($rc<=0) {
	&welog("ERROR DB: sql=<$qsql> err:<$DBI::err>-<$DBI::errstr>", "myprod",-2);
	}
return $rc;
}
###############################################################
# mycat
###############################################################

sub mycat(@a) {
# cod_cat   	varchar(10)
# cod_cat_padre  	varchar(10)
# descat  	varchar(30)
# cat_nivel  	int(1)
# cat_tipo  	char(1)
# idcarga  	int(10)

my @a = @_;
local $sth;
local ($qsql, $str);

   if (!defined($dbh)) {
       &wtlog("ERROR: rc=".$DBI::err." - ".$DBI::errstr, "mycat",-1);
       return -1;
       }
	$str = substr(@a[0],0,2);
	if ( $str > 90) {
		#no procesa categorias superiores a 90
		return 0;
		}
	
	
	$qsql = "INSERT INTO car_catprod(
	cod_cat,cod_cat_padre,descat,cat_nivel,cat_tipo, idcarga, id_cadena
	) VALUES ('@a[0]', '@a[1]', '@a[2]', @a[3], '@a[4]',$idcarga, $Id_cadena);";
	
	$sth = $dbh->prepare($qsql);
	$rc = $sth->execute();
	if ($rc<=0) {
	&welog("ERROR DB: sql=<$qsql> err:<$DBI::err>-<$DBI::errstr>", "mycat",-2);
	}

return $rc;
}
###############################################################
# myprodprov
###############################################################
sub myprodprov(@a) {
	my @a = @_;
	local $sth;
	local $qsql;
	
	if (!defined($dbh)) {
	&wtlog("ERROR: rc=".$DBI::err." - ".$DBI::errstr, "myprodprov",-1);
	return -1;
	}
		
	$qsql = "INSERT INTO car_prodxprov(cod_prod1,cod_prov, idcarga, id_cadena) VALUES ('@a[0]', '@a[1]',$idcarga, $Id_cadena);";

	$sth = $dbh->prepare($qsql);
	$rc = $sth->execute();
	if ($rc<=0) {
	&welog("ERROR DB: sql=<$qsql> err:<$DBI::err>-<$DBI::errstr>", "myprodprov",-2);
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
    
    $qry = "SELECT prefijo, id_cadena, tipo FROM archivos WHERE prefijo <>'PRE'";
    $sth = $dbh->prepare($qry);
    $rc = $sth->execute();
    $i=1;
    while(($Prefijo, $Id_cadena, $Tipo) = $sth->fetchrow_array){
	&wtlog("$i Procesa Archivo prefijo=<$Prefijo> idcadena=<$Id_cadena> Tipo=<$Tipo>","MAIN","COMIENZO");	
	carga_arch_generico($Tipo,$Path_load,$Path_load_proc,$Prefijo,$Extension);
	$i++;
    }
    
    termina_carga();
    &wtlog("finaliza programa $0...","MAIN","FIN");
    db_disc();
} # fin main

main();
