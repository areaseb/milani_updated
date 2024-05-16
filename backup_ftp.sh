#!/bin/bash

# Configurazione
HOST="u196112.onthecloud.srl"
USER="u196112"
PASS="alNbihYQdzzykBeN"
PATH_RMT="/MILANI"
PATH_LCL="/var/backups/crm"
FILE="$1"

# Rendo attiva la cartella con il file da copiare
cd $PATH_LCL

# Connessione al server FTP e trasferimento file
ftp -n -i $HOST <<EOF
user $USER $PASS
binary
passive
cd $PATH_RMT
put $FILE
quit
EOF