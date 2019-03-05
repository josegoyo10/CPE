#!/bin/sh
gunzip -q /var/www/html/centroproy/procesos/arch_in/*.gz
rename .CSVSJ .CSV /var/www/html/centroproy/procesos/arch_in/*.CSVSJ
perl /var/www/html/centroproy/procesos/pc1.pl
perl /var/www/html/centroproy/procesos/pc2.pl
perl /var/www/html/centroproy/procesos/pc_subtipos.pl

