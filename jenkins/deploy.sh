#!/bin/bash

mkdir -p /var/app/var/cache
mkdir -p /var/app/var/cache/prod
mkdir -p /var/app/var/logs
mkdir -p /var/app/var/logs/supervisord

composer install --no-scripts --no-suggest --optimize-autoloader
composer run-script before-deploy-cmd

rm -R /var/app/var/cache/prod/*

chmod -R 0777 /var/app/var/cache
chmod -R 0777 /var/app/var/logs
chown -R www-data:www-data /var/app/var/cache
chown -R www-data:www-data /var/app/var/logs

rm docker-compose.yml
rm docker-compose-deploy.yml
rm docker-compose-jenkins.yml
rm docker-compose-mac.yml
rm docker-compose-nuc.yml
rm phpunit.xml
rm -R jenkins
rm -R tests/