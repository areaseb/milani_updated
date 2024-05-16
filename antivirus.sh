#!/bin/bash

# aggiorno l'antivirus
apt-get install clamav clamav-freshclam -y
freshclam

# scansiono
cd /home/web/
ls > /var/backups/crm/elenco_siti.txt

while IFS= read -r line || [ -n "$line" ]; do
	
	cd /home/web/$line/www
	clamscan -r -i > /var/backups/crm/antivirus/$(date +%Y-%m-%d)"-"$line".txt"
	
	check=$(cat "/var/backups/crm/antivirus/"$(date +%Y-%m-%d)"-"$line".txt"  | head -n 7  | grep "Infected files" --color=none| awk {'print $3'});
	
	if [[ $check -gt 0 ]]; then
	
		echo "Risultato della scansione - MINACCE RILEVATE
		
$(cat /var/backups/crm/antivirus/$(date +%Y-%m-%d)-$line.txt)" | mail -a "Content-type: text/plain;MIME-Version: 1.0;X-Mailer: Dave's mailer" -r info@areaseb.it -s "Rilevati virus su $line" assistenza@areaseb.it
		
	fi
	   
done </var/backups/crm/elenco_siti.txt	