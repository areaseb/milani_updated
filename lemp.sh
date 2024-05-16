#!/bin/bash
#Installazione Lamp automatica

# modifico il fuso orario del server
timedatectl set-timezone Europe/Rome

# aggiorno il sistema
apt update && apt upgrade -y

# installo i componenti di base
apt-get install software-properties-common dirmngr ca-certificates apt-transport-https nano wget curl -y

# installo Nginx
apt install nginx apache2-utils mlocate -y
rm /etc/nginx/sites-enabled/default
echo 'server {
  listen 80;
  listen [::]:80;
  server_name _;
  root /home/web/;
  index index.php index.html index.htm index.nginx-debian.html;

  location / {
    try_files $uri $uri/ /index.php;
  }

  location ~ \.php$ {
    fastcgi_pass unix:/run/php/php7.4-fpm.sock;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    include fastcgi_params;
    include snippets/fastcgi-php.conf;
    fastcgi_read_timeout 3000;
  }

 # A long browser cache lifetime can speed up repeat visits to your page
  location ~* \.(jpg|jpeg|gif|png|webp|svg|woff|woff2|ttf|css|js|ico|xml)$ {
       access_log        off;
       log_not_found     off;
       expires           360d;
  }

  # disable access to hidden files
  location ~ /\.ht {
      access_log off;
      log_not_found off;
      deny all;
  }
}' > /etc/nginx/conf.d/default.conf
systemctl restart nginx
systemctl enable nginx
mkdir -p /home/web
chmod -R 755 /home/web
# attivo il restart automatico
mkdir -p /etc/systemd/system/nginx.service.d/
echo "[Service]
Restart=always
RestartSec=5s" > /etc/systemd/system/nginx.service.d/restart.conf
systemctl daemon-reload

# installo MariaDB
apt install mariadb-server mariadb-client -y
systemctl enable mariadb
# attivo il restart automatico
mkdir -p /etc/systemd/system/mariadb.service.d/
echo "[Service]
Restart=always
RestartSec=5s" > /etc/systemd/system/mariadb.service.d/restart.conf
systemctl daemon-reload

# installo PHP 7.4 FPM
apt install php7.4-fpm -y
apt install php7.4-common php7.4-bcmath php7.4-mbstring php7.4-curl php7.4-xml php7.4-zip php7.4-gd php7.4-mysql php7.4-json -y

# installo PHP 8.1 FPM
curl -sSL https://packages.sury.org/php/README.txt | bash -x
apt upgrade -y
apt install php8.1-fpm -y
apt-get update -y
apt install php8.1-common php8.1-bcmath openssl php8.1-mbstring php8.1-curl php8.1-xml php8.1-zip php8.1-gd -y

# installo phpmyadmin
echo "

#################################################################################################
####   Viene ora lanciata l'installazione di PhpMyAdmin, seguire le istruzioni a video.   ######
#################################################################################################

"

sleep 3
apt install phpmyadmin -y
ln -s /usr/share/phpmyadmin /home/web/phpmyadmin
systemctl restart nginx

# installo pwgen per creare le password
apt install pwgen -y

# installo FTP
apt install vsftpd -y

# installo FTP per i backup
apt install ftp -y

# installo Certbot per Let's Encrypt
apt install certbot python3-certbot-nginx -y

# attivo la sicurezza su MariaDB
echo "

##############################################################################################################################################################
####   Viene ora lanciato il comando mysql_secure_installation per mettere in sicurezza MariaDB. Rispondere sempre Y e seguire le istruzioni a video.   ######
##############################################################################################################################################################

"

sleep 3
mysql_secure_installation

# installo l'smtp
echo "

##############################################################################################
####   Viene ora lanciata l'installazione dell'SMTP, seguire le istruzioni a video.   ######
##############################################################################################

"

sleep 3
apt install mailutils -y
apt install postfix -y

# installo zip e nzip
apt install zip -y
apt install unzip -y

# installo composer
cd /root
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
chmod +x /usr/local/bin/composer

# attivo le cron per i backup
mkdir -p /var/backups/crm
mkdir -p /var/backups/crm/db
chmod -R 755 /var/backups/crm
cronjob="SHELL=/bin/bash
PATH=/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin

0 10 * * 0 bash /root/SCRIPT/backup.sh
0 0 * * * bash /root/SCRIPT/backup_db.sh
* * * * * bash /root/SCRIPT/restart_services.sh
0 2 * * * bash /root/SCRIPT/antivirus.sh
0 1 * * * /usr/bin/certbot renew --quiet"
(crontab -u root -l; echo "$cronjob" ) | crontab -u root -
systemctl restart cron

mkdir -p /var/log/attivita
chmod -R 755 /var/log/attivita

# antivirus
apt install clamav -y
mkdir -p /var/backups/crm/antivirus

# firewall
apt install ufw -y
ufw allow 22/tcp
ufw allow proto tcp from any to any port 80,443
ufw allow 25/tcp
ufw allow 465/tcp
ufw allow 21/tcp
ufw allow 20/tcp
ufw allow 10090:10100/tcp
ufw allow 40000:50000/tcp

echo "#################################################################################################################################
####   Controllare firewall, eventualmente sovrascrivere /etc/ufw/user.rules, e copiare /etc/ufw/before.rules, poi attivare   ######
####################################################################################################################################

"

echo "######################################################################
####   Ricordarsi di modificare il file /etc/mysql/debian.cnf   ######
######################################################################

"

echo "#########################################################################
####   Ricordarsi di modificare il file /etc/php/8.1/fpm/php.ini   ######
#########################################################################

"

echo "#################################################################
####   Ricordarsi di modificare il file /etc/vsftpd.conf   ######
#################################################################

"

echo "####################################################################################################
####   Ricordarsi di caricare e adattare i file scripts in /root/SCRIPT, compreso antivirus   ######
####################################################################################################

"

echo "#########################################################################################################
####   Inserire la riga client_max_body_size 100M; nel riquadro http del file nginx.conf           ######
####   Inserire la riga fastcgi_read_timeout 3000; nel riquadro php del file conf.d/default.conf   ######
#########################################################################################################

"


exit 0;