#!/bin/bash sh

mkdir -p /var/app/var/cache
mkdir -p /var/app/var/logs

php /var/app/bin/console doctrine:database:create

composer run-script post-install-cmd --no-interaction

chmod -R 0777 /var/app/var/logs
chmod -R 0777 /var/app/var/cache

opt/supervisor-manager.sh start

exec apache2-foreground
