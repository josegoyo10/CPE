#!/bin/sh
gunzip -q /var/www/html/centroproy/procesos/arch_in/precioscp/*
perl /var/www/html/centroproy/procesos/precios_c1.pl
perl /var/www/html/centroproy/procesos/precios_c2.pl

