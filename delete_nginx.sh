#!/bin/bash

dominio=$1
id=$3

UTENTE=$(cat /etc/passwd | grep /home/web/www."$dominio" | cut -d: -f1);

echo $(date +%Y-%m-%d.%H:%M:%S)" - OK - Delete" >> /var/log/attivita/$id"_"$dominio.log

#fermo tutti i processi in atto per l'utente e poi elimino l'utente
ps -ef | grep $UTENTE | awk '{ print $2 }' | xargs kill -9
systemctl restart php7.4-fpm.service

userdel -rf $UTENTE

rm /etc/php/7.4/fpm/pool.d/"$dominio".conf
rm /etc/nginx/sites-enabled/www."$dominio".conf
rm /etc/nginx/sites-available/www."$dominio".conf
rm -rf /home/web/www."$dominio"
rm /var/spool/cron/crontabs/"$UTENTE"

SQLADMIN=$(cat /etc/mysql/debian.cnf  | head -n 6  | grep "user" --color=none| awk {'print $3'});
SQLADMINPASSWORD=$(cat /etc/mysql/debian.cnf  | head -n 6  | grep "password" --color=none| awk {'print $3'});

mysql -u $SQLADMIN -p$SQLADMINPASSWORD -sN -e "REVOKE GRANT OPTION ON *.* FROM '$UTENTE'@'localhost';"
  
for i in {0..5}; do
  mysql -u $SQLADMIN -p$SQLADMINPASSWORD -sN -e "DROP DATABASE \`$UTENTE"_"$i\`;"
done

mysql -u $SQLADMIN -p$SQLADMINPASSWORD -sN -e "drop user '$UTENTE';"
mysql -u $SQLADMIN -p$SQLADMINPASSWORD -sN -e "use mysql; delete from user where User = '$UTENTE';"

echo $(date +%Y-%m-%d.%H:%M:%S)" - OK - Fatto" >> /var/log/attivita/$id"_"$dominio.log
