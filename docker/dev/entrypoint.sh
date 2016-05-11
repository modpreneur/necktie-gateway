#!/bin/bash sh

mkdir -p /var/app/var/cache
mkdir -p /var/app/var/logs

chmod -R 0777 /var/app/var/logs
chmod -R 0777 /var/app/var/cache

supervisord -c /etc/supervisor/conf.d/supervisord.conf

composer install
composer run-script post-install-cmd --no-interaction

exec apache2-foreground
