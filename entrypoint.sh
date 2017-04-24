#!/bin/bash sh

composer run-script after-deploy-cmd

composer dump-autoload --optimize --apcu

bin/console necktie:bunny:setup

##supervisor - load config from necktie
ENV=prod supervisord -c /var/app/supervisor/supervisord.conf
supervisorctl -c /var/app/supervisor/supervisord.conf reload

bin/console cache:clear -n -e=prod

chmod -R 0777 /var/app/var/cache
chmod -R 0777 /var/app/var/logs
chown -R www-data:www-data /var/app/var/cache
chown -R www-data:www-data /var/app/var/logs

exec php-fpm