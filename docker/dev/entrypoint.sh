#!/bin/bash sh

mkdir -p /var/app/var/cache
mkdir -p /var/app/var/logs

chmod -R 0777 /var/app/var/logs
chmod -R 0777 /var/app/var/cache

composer install
composer run-script post-install-cmd --no-interaction

supervisord -c /var/app/supervisor/supervisord.conf
supervisorctl -c /var/app/supervisor/supervisord.conf status

php /var/app/bin/console bunny:setup

exec apache2-foreground
