#!/bin/bash

cd ..
composer update

/var/app/bin/console doctrine:schema:update --force

mkdir /var/app/build
/var/app/bin/phpunit -c /var/app/phpunit.xml /var/app/src --log-junit /var/app/build/phpunit.xml

chown -R 106 /var/app/
chown -R www-data:www-data /var/app/var/logs
chown -R www-data:www-data /var/app/var/cache
chmod -R 777 /var/app/var/cache
#rm -R /var/app/var/cache/*
#rm -R /var/app/vendor/*

