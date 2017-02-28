#!/bin/bash sh

mkdir -p /var/app/var/cache
mkdir -p /var/app/var/logs
mkdir -p /var/app/var/logs/supervisord

#bin/console redis:flushdb

composer run-script post-install-cmd --no-interaction

##supervisor - load config from necktie
ENV=prod supervisord -c /var/app/supervisor/supervisord.conf
supervisorctl -c /var/app/supervisor/supervisord.conf reload

bin/console necktie:bunny:setup

chmod -R 0777 /var/app/var/cache
chmod -R 0777 /var/app/var/logs
chown -R www-data:www-data /var/app/var/cache
chown -R www-data:www-data /var/app/var/logs

exec php-fpm