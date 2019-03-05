nombre_log=$(date +mantencion%d%m%Y)".log"

mysql -u root -parcano  CPROYV3C < .mantencion.sql> ./../log/$nombre_log

exit;
