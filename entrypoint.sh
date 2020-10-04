#!/usr/bin/env sh
WWW_DATA_HOME="/var/www/html"
import_directory=$(ls -d /docker-entrypoint-inituploads.d/*/ | sort | tail -n 1 | awk '{print $1"*"}')
uploads_directory="${WWW_DATA_HOME}/web/app/uploads"
if [ ! "$(ls -A $uploads_directory)" ]; then
    sudo -u www-data cp -rfp ${import_directory} "${uploads_directory}"
fi
sudo -u www-data composer install --prefer-dist --optimize-autoloader --no-scripts --no-dev

SERVERCERT="/etc/pki/tls/certs/servercert-${DOMAIN_NAME}.pem"
SERVERKEY="/etc/pki/tls/private/serverkey-${DOMAIN_NAME}.pem"
while :; do
    if [ -r "${SERVERCERT}" ]; then
        break
    fi
    sleep 1
done

cp -p /etc/pki/CA/cacert-${DOMAIN_NAME}.pem /usr/share/ca-certificates/cacert-${DOMAIN_NAME}.crt
echo cacert-${DOMAIN_NAME}.crt >> /etc/ca-certificates.conf
update-ca-certificates

wait-for-it --timeout=90 "${DB_HOST}:${DB_PORT}"
sudo -u www-data -E "${WWW_DATA_HOME}/vendor/bin/wp" language core install ja
sudo -u www-data -E "${WWW_DATA_HOME}/vendor/bin/wp" language plugin install --all ja
sudo -u www-data -E "${WWW_DATA_HOME}/vendor/bin/wp" language theme install --all ja

exec docker-php-entrypoint "$@"
