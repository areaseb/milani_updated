#!/bin/bash

SQLADMINPASSWORD=$(cat /etc/mysql/debian.cnf  | head -n 6  | grep "password" --color=none| awk {'print $3'});

mysqldump -u root -p"$SQLADMINPASSWORD" --all-databases > /var/backups/crm/db/$(date +%A)_tutti_i_db.sql

# Configurazione
HOST="u196112.onthecloud.srl"
USER="u196112"
PASS="alNbihYQdzzykBeN"
PATH_RMT="/MILANI"
PATH_LCL="/var/backups/crm/db"

# Rendo attiva la cartella con il file da copiare
cd $PATH_LCL

# Connessione al server FTP e trasferimento file
ftp -n -i $HOST <<EOF
user $USER $PASS
binary
passive
cd $PATH_RMT
put $(date +%A)_tutti_i_db.sql
quit
EOF