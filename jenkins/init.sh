#!/bin/bash

mkdir -p /var/app/var/cache
mkdir -p /var/app/var/logs
mkdir -p /var/app/var/logs/supervisord

composer install

pkill python
ENV=test supervisord -c /var/app/supervisor/supervisord.conf
sleep 2
supervisorctl -c /var/app/supervisor/supervisord.conf status

bin/console bunny:setup

chown -R 106 /var/app/
chmod -R 0777 /var/app/var/cache
chmod -R 0777 /var/app/var/logs
chown -R www-data:www-data /var/app/var/cache
chown -R www-data:www-data /var/app/var/logs

mkdir /var/app/build
bin/phpunit -c /var/app/phpunit.xml /var/app/src --log-junit /var/app/build/phpunit.xml
