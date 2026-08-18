#!/bin/sh

set -e

echo "========================================"
echo "       Laravel Render Startup"
echo "========================================"

echo
echo "=== Environment ==="
echo "APP_ENV=${APP_ENV:-not-set}"
echo "APP_DEBUG=${APP_DEBUG:-not-set}"
echo "DB_CONNECTION=${DB_CONNECTION:-not-set}"

echo
echo "=== SQLite ==="

if [ -n "$DB_DATABASE" ]; then
    SQLITE_PATH="$DB_DATABASE"
else
    SQLITE_PATH="/var/data/database.sqlite"
fi

export DB_DATABASE="$SQLITE_PATH"

mkdir -p "$(dirname "$SQLITE_PATH")"

if [ ! -f "$SQLITE_PATH" ]; then
    echo "Creating SQLite database: $SQLITE_PATH"
    touch "$SQLITE_PATH"
fi

chown www-data:www-data "$SQLITE_PATH"

echo "SQLite database: $SQLITE_PATH"

echo
echo "=== Laravel cache ==="

php artisan config:clear
php artisan route:clear
php artisan view:clear

echo
echo "=== Laravel migrations ==="

php artisan migrate --force

echo
echo "=== Laravel optimization ==="

php artisan config:cache
php artisan route:cache
php artisan view:cache

echo
echo "=== Starting PHP-FPM ==="

php-fpm -D

echo
echo "=== Starting Nginx ==="

exec nginx -g 'daemon off;'
