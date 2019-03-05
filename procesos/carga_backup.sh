#!/bin/sh
DIA=`date +%d`
#Para el backup de la BD
rm -f /mysql/backup/centroproy/centroproy_$DIA.dump.gz
mysqldump --opt -u root -parcano CPROYV3P > /mysql/backup/centroproy/centroproy_$DIA.dump
gzip /mysql/backup/centroproy/centroproy_$DIA.dump
#Para el backup de la WEB
rm -f /mysql/backup/centroproy/centroproy_$DIA.tar.gz
tar cf /mysql/backup/centroproy/centroproy_$DIA.tar /var/www/html/centroproy/
gzip /mysql/backup/centroproy/centroproy_$DIA.tar

