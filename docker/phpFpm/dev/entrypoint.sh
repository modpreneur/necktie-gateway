#!/bin/bash sh

mkdir -p /var/app/var/cache
mkdir -p /var/app/var/logs
mkdir -p /var/app/var/logs/supervisord

if [ -f "vendor/autoload.php" ]; then
    echo "Vendors are already installed";
else
    composer install --dev --no-scripts --no-suggest --optimize-autoloader --apcu-autoloader
fi

composer run-script post-install-cmd --no-interaction


if [ $USER_ID ] ; then
    echo "Chown app folder to user with id $USER_ID"
    chown -R $USER_ID /var/app/
fi

chown -R www-data:www-data /var/app/var/logs
chmod -R 0777 /var/app/var/logs

#supervisor - load config from necktie
ENV=dev supervisord -c /var/app/supervisor/supervisord.conf
supervisorctl -c /var/app/supervisor/supervisord.conf reload

bin/console bunny:setup

chmod -R 0777 /var/app/var/cache
chmod -R 0777 /var/app/var/logs
chown www-data:www-data /var/app/var/logs
chown www-data:www-data /var/app/var/cache

exec php-fpm