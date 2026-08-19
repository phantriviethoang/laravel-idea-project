#!/bin/sh
set -e

mkdir -p /var/www/html/database /var/www/html/storage /var/www/html/bootstrap/cache
touch /var/www/html/database/database.sqlite
chown -R www-data:www-data /var/www/html/database /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/database /var/www/html/storage /var/www/html/bootstrap/cache

php /var/www/html/artisan migrate --force || true
php /var/www/html/artisan config:cache || true
php /var/www/html/artisan route:cache || true

exec /usr/bin/supervisord -c /etc/supervisord.conf
