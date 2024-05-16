#!/bin/bash
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
    php_version_available=$(ls -1 /etc/opt/remi/)
    php_version_new="$(echo $2 | sed 's/\.//g')"
    WEBSERVER="httpd"
    ;;

  "debian" )
    php_version_available=$(ls -1 /etc/php)
    php_version_new="$2"
    WEBSERVER="nginx"
    ;;

  * )
    echo "Non ho trovato alcun OS di default, esco"
    exit
    ;;

esac

dominio="$1"

if [[ -z $(echo $php_version_available | grep $php_version_new) ]]; then
  echo "versione PHP specificata [${php_version_new}] non disponibile"
  exit
fi
if [[ ! -e /etc/${WEBSERVER}/sites-available/www.$dominio.conf ]]; then
  echo "file di configurazione $WEBSERVER non esistente"
  exit
fi

# rimuovo le vecchie configurazioni del pool
for php_version in $php_version_available; do
  if [[ $SISTEMA_OPERATIVO == "centos" ]]; then
    rm -f /etc/opt/remi/${php_version}/php-fpm.d/${dominio}.conf && systemctl restart "${php_version}-php-fpm"
  elif [[ $SISTEMA_OPERATIVO == "debian" ]]; then
    rm -f /etc/php/${php_version}/fpm/pool.d/${dominio}.conf && service "php${php_version}-fpm" restart
  fi
done

ftp_user=$(ls -lA "/home/web/www.$dominio" | grep "www$" | awk {'printf $3'})
tmp_pool=$(mktemp)
cp /root/SCRIPT/.template-fpm-pool.conf $tmp_pool
sed -i "s/DOMAIN/$dominio/g" $tmp_pool
sed -i "s/USERFTP/$ftp_user/g" $tmp_pool
sed -i "s/PHPVERSION/$php_version_new/g" $tmp_pool

if [[ $SISTEMA_OPERATIVO == "centos" ]]; then
  mv $tmp_pool "/etc/opt/remi/php${php_version_new}/php-fpm.d/${dominio}.conf"
  sed -i -E "s#php[5,7]\.?[0-9]?#php${php_version_new}#g" "/etc/${WEBSERVER}/sites-available/www.${dominio}.conf"
  systemctl restart "php${php_version_new}-php-fpm"
elif [[ $SISTEMA_OPERATIVO == "debian" ]]; then
  mv $tmp_pool "/etc/php/${php_version_new}/fpm/pool.d/${dominio}.conf"
  sed -i -E "s#php[5,8]\.?[0-9]?-fpm-${dominio}#php${php_version_new}-fpm-${dominio}#g" "/etc/${WEBSERVER}/sites-available/www.${dominio}"
  service "php${php_version_new}-fpm" restart
fi

service $WEBSERVER restart

echo "... done!"
