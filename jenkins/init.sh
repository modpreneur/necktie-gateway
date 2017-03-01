#!/bin/bash

cd /var/app
composer update

/var/app/bin/console doctrine:schema:update --force

ENV=dev supervisord -c /var/app/supervisor/supervisord.conf
supervisorctl -c /var/app/supervisor/supervisord.conf reload

chown -R 106 /var/app/
chown -R www-data:www-data /var/app/var/logs
chown -R www-data:www-data /var/app/var/cache
chmod -R 777 /var/app/var/cache

#wait for supervisor to start (tests result in error "expected RUNNING, got STARTING instead" otherwise)
sleep 5

mkdir /var/app/build
/var/app/bin/phpunit -c /var/app/phpunit.xml /var/app/src --log-junit /var/app/build/phpunit.xml


#rm -R /var/app/var/cache/*
#rm -R /var/app/vendor/*

