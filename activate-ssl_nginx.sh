#!/bin/bash
#Attivazione Lamp automatica

dominio=$1
id=$3

echo $(date +%Y-%m-%d.%H:%M:%S)" - OK - Attivazione SSL" >> /var/log/attivita/$id"_"$dominio.log
    
echo "Attivo SSL\n\n"
certbot --nginx -d $dominio --redirect

sed -i 's/listen 443 ssl/listen 443 ssl http2/g' /etc/nginx/sites-available/www.$dominio.conf
systemctl restart nginx

echo $(date +%Y-%m-%d.%H:%M:%S)" - OK - Fatto" >> /var/log/attivita/$id"_"$dominio.log