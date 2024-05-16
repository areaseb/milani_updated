#!/bin/bash
#Attivazione Lamp automatica

echo "Individuo il sistema operativo..."
if [[ -e "/etc/centos-release" ]]; then
  SISTEMA_OPERATIVO="centos"
elif [[ -e "/etc/debian_version" ]]; then
  SISTEMA_OPERATIVO="debian"
else
  echo "Non ho trovato alcun OS di default, esco"
  exit
fi

case $SISTEMA_OPERATIVO in

  "centos" )
    WEBSERVER="httpd"
    echo "inserire l'utente MySQl di root"
    read SQLADMIN
    echo "inserire la password dell'utente MySQl di root"
    read SQLADMINPASSWORD
    mysql -u$SQLADMIN -p$SQLADMINPASSWORD -e "exit"
    if [[ "$?" != "0" ]]; then
      echo "credenziali MySQL non corrette, esco..."
      exit
    fi
    ;;

  "debian" )
    WEBSERVER="nginx"
    SQLADMIN=$(cat /etc/mysql/debian.cnf  | head -n 6  | grep "user" --color=none| awk {'print $3'});
    SQLADMINPASSWORD=$(cat /etc/mysql/debian.cnf  | head -n 6  | grep "password" --color=none| awk {'print $3'});
    ;;

  * )
    echo "Non ho trovato alcun OS di default, esco"
    exit
    ;;

esac

dominio="$1"
id="$3"

tld="$(echo $dominio | cut -d '.' -f 2-)"
ftp_user=$(echo "$dominio" | cut -b-2)$(echo "$tld" | cut -b-2)$(pwgen -A1 4)
ftp_password=$(pwgen -s1 8);

if [[ ! -e /root/SCRIPT/.template-vhost_nginx.conf ]]; then
  echo "template del vhost non presnete in /root/SCRIPT/.template-vhost.conf, esco"
  exit
fi
if [[ ! -e /root/SCRIPT/.template-fpm-pool.conf  ]]; then
  echo "template del pool php-fpm non presnete in /root/SCRIPT/.template-fpm-pool.conf, esco"
  exit
fi

tmp_vhost=$(mktemp)
cp /root/SCRIPT/.template-vhost_nginx.conf $tmp_vhost
sed -i "s/DOMAIN/$dominio/g" $tmp_vhost

mkdir -p /home/web/www."$dominio"/{www,logs,.cache,.config}
useradd -d /home/web/www."$dominio" -s /bin/bash -p "$ftp_password" "$ftp_user"
if [[ $SISTEMA_OPERATIVO == "centos" ]]; then
  usermod -a -G apache $ftp_user
elif [[ $SISTEMA_OPERATIVO == "debian" ]]; then
  usermod -aG $ftp_user www-data
fi

echo "$ftp_user:$ftp_password" | chpasswd

mv $tmp_vhost /etc/${WEBSERVER}/sites-available/www.$dominio.conf
ln -s "/etc/${WEBSERVER}/sites-available/www.$dominio.conf" "/etc/${WEBSERVER}/sites-enabled/www.$dominio.conf"
#Do i permessi alle cartelle
chown -R $ftp_user: "/home/web/www.$dominio/www/"
chown -R $ftp_user: "/home/web/www.$dominio/.cache/"
chown -R $ftp_user: "/home/web/www.$dominio/.config/"
chmod -R 755 /home/web/www.$dominio/.cache
chmod -R 755 /home/web/www.$dominio/.config

echo $(date +%Y-%m-%d.%H:%M:%S)" - OK - Creazione utente, FTP e vhost" > /var/log/attivita/$id"_"$dominio.log

bash /root/SCRIPT/switchphp_nginx.sh "$dominio" "7.4"

echo $(date +%Y-%m-%d.%H:%M:%S)" - OK - Attivazione PHP" >> /var/log/attivita/$id"_"$dominio.log

mysql_user="$ftp_user"
mysql_password=$(pwgen -s1 8);

mysql -u $SQLADMIN -p$SQLADMINPASSWORD -sN -e "CREATE USER '$mysql_user'@'localhost' IDENTIFIED BY '$mysql_password'; GRANT USAGE ON *.* TO  '$mysql_user'@'localhost' IDENTIFIED BY  '$mysql_password' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0; "
for i in {0..5}; do
  mysql -u $SQLADMIN -p$SQLADMINPASSWORD -sN -e "CREATE DATABASE \`$mysql_user"_"$i\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; ";
  mysql -u $SQLADMIN -p$SQLADMINPASSWORD -sN -e "GRANT ALL PRIVILEGES ON \`$mysql_user"_"$i\` . * TO '$mysql_user'@'localhost';"
  mysql -u $SQLADMIN -p$SQLADMINPASSWORD -sN -e "FLUSH PRIVILEGES;"
done

echo $(date +%Y-%m-%d.%H:%M:%S)" - OK - Creazione database" >> /var/log/attivita/$id"_"$dominio.log

echo -e "Host FTP: $(cat /etc/hostname) o  ftp.$dominio\n\
FTP user: $ftp_user\n\
FTP password: $ftp_password\n\n\
PHPMyAdmin: http://$(hostname)/phpmyadmin o http://$dominio/phpmyadmin\n\
MySQL user: $mysql_user\n\
MySQL password: $mysql_password\n\n\
Laravel installato correttamente\n\n\

Le credenziali sono state inviate via e-mail a assistenza@areaseb.it
"

echo "<html>
<b>Host FTP: $(cat /etc/hostname) o  ftp.$dominio</b><br>
<b>FTP user:</b> $ftp_user<br>
<b>FTP password:</b> $ftp_password<br><br>
<b>PHPMyAdmin: http://$(hostname)/phpmyadmin o http://$dominio/phpmyadmin</b><br>
<b>MySQL user:</b> $mysql_user<br>
<b>MySQL password:</b> $mysql_password<br><br>
Laravel installato correttamente
</html>" | mail -a "Content-type: text/html;MIME-Version: 1.0;X-Mailer: Dave's mailer" -r info@areaseb.it -s "Attivazione servizio $dominio" assistenza@areaseb.it

echo -e "Per modificare la versione PHP eseguire lo script /root/SCRIPT/switchphp.sh nel seguente formato:\n\
/root/SCRIPT/switchphp.sh dominio.tld versione_php\n\n\
versioni PHP accettate: [8.1]\n\n
Versione PHP attuale: 8.1"

chown -R $ftp_user: "/home/web/www.$dominio/www/"

echo $(date +%Y-%m-%d.%H:%M:%S)" - OK - Reset permessi cartelle e file" >> /var/log/attivita/$id"_"$dominio.log

apt purge postfix -y
apt purge mailutils -y

exit 0;