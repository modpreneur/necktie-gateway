#!/bin/bash sh

alias composer="php -n /usr/bin/composer"

mkdir -p /var/app/app/cache
mkdir -p /var/app/app/logs
chmod -R 0777 /var/app/app/cache
chmod -R 0777 /var/app/app/logs

chmod +x /var/app/docker/supervisor-manager.sh

./docker/supervisor-manager.sh start

composer run-script post-install-cmd --no-interaction

service postfix start
exec apache2-foreground



