#!/bin/bash

mkdir -p /var/app/var/cache
mkdir -p /var/app/var/cache/prod
mkdir -p /var/app/var/logs
mkdir -p /var/app/var/logs/supervisord

composer install --no-scripts --no-suggest --optimize-autoloader

rm -R /var/app/var/cache/prod/*

rm docker-compose.yml
rm docker-compose-deploy.yml
rm docker-compose-jenkins.yml
rm docker-compose-mac.yml
rm docker-compose-nuc.yml
rm phpunit.xml
rm -R jenkins
rm -R tests/

