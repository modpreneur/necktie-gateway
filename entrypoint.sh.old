#!/bin/bash sh

mkdir -p /var/app/var/cache
mkdir -p /var/app/var/logs

composer run-script post-install-cmd --no-interaction

php /var/app/bin/console bunny:setup

supervisord -c /var/app/supervisor/supervisord.conf
supervisorctl -c /var/app/supervisor/supervisord.conf status

chmod -R 0777 /var/app/var/logs
chmod -R 0777 /var/app/var/cache

exec apache2-foreground
