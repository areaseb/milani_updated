#!/bin/bash

# salvo backup db settimanale
SQLADMINPASSWORD=$(cat /etc/mysql/debian.cnf  | head -n 6  | grep "password" --color=none| awk {'print $3'});
mysqldump -u root -p$SQLADMINPASSWORD --all-databases > /var/backups/crm/tutti_i_db.sql

# Configurazione FTP
HOST="u196112.onthecloud.srl"
USER="u196112"
PASS="alNbihYQdzzykBeN"
PATH_RMT="/MILANI"
PATH_LCL="/var/backups/crm"

# cancello backup db giornalieri
cd /var/backups/crm/db/
rm -rf ./*

cd /home/web/
ls > /var/backups/crm/elenco_siti.txt

while IFS= read -r line || [ -n "$line" ]; do
	
	tar -zcvf /var/backups/crm/$line.tar.gz /home/web/$line
	
	# sposto in FTP
	bash /root/SCRIPT/backup_ftp.sh $line.tar.gz
	
	# elimino il file tar.gz dal server locale
	rm /var/backups/crm/$line.tar.gz
	   
done </var/backups/crm/elenco_siti.txt	

# sposto in FTP il db
bash /root/SCRIPT/backup_ftp.sh tutti_i_db.sql