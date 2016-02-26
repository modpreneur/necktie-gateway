FROM php:7-apache

MAINTAINER Tomáš Jančar <jancar@modpreneur.com>

RUN apt-get update && apt-get -y install \
    apt-utils \
    curl \
    git \
    libcurl4-openssl-dev \
    libpq-dev \
    libpq5 \
    zlib1g-dev \
    wget\
    libmcrypt-dev \
    sqlite3

# install postfix
#RUN echo "postfix postfix/main_mailer_type string Internet site" > preseed.txt \
# && echo "postfix postfix/mailname string modpreneur.com" >> preseed.txt \
# && debconf-set-selections preseed.txt \
# && DEBIAN_FRONTEND=noninteractive apt-get install -q -y postfix

RUN docker-php-ext-install curl json mbstring opcache pdo_pgsql zip mcrypt

# Install apcu
RUN pecl install -o -f apcu-5.1.2 apcu_bc-beta \
    && rm -rf /tmp/pear \
    && echo "extension=apcu.so" > /usr/local/etc/php/conf.d/apcu.ini \
    && echo "extension=apc.so" >> /usr/local/etc/php/conf.d/apcu.ini \
    && docker-php-ext-configure bcmath \
    && docker-php-ext-install bcmath

RUN pecl install xdebug-beta && \
    docker-php-ext-enable xdebug

# prepare php and apache
RUN rm -rf /etc/apache2/sites-available/* /etc/apache2/sites-enabled/*

ENV APP_DOCUMENT_ROOT /var/app/ \
 && APACHE_RUN_USER www-data \
 && APACHE_RUN_GROUP www-data \
 && APACHE_LOG_DIR /var/log/apache2

ADD docker/php.ini /usr/local/etc/php/
ADD docker/000-default.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/app

# Install composer
RUN curl -sS https://getcomposer.org/installer | php \
    && cp composer.phar /usr/bin/composer

RUN usermod -u 1000 www-data \
 && usermod -G staff www-data

# Install app
RUN rm -rf /var/app/*
ADD . /var/app

# enable apache and mod rewrite
RUN a2ensite 000-default.conf \
    && a2enmod expires \
    && a2enmod rewrite \
    && service apache2 restart

#RUN composer install --no-scripts --optimize-autoloader

RUN apt-get update && apt-get -y install supervisor \
    nano \
    openssh-server \
    openssh-client


COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
RUN echo "export TERM=xterm" >> /etc/bash.bashrc
RUN echo "alias composer=\"php -n /usr/bin/composer\"" >> /etc/bash.bashrc

EXPOSE 22 9608 9001

RUN echo "/usr/local/lib/php/extensions/no-debug-non-zts-20151012/xdebug.so" >> /usr/local/etc/php/php.ini
RUN echo "xdebug.remote_enable=1" >> /usr/local/etc/php/php.ini
RUN echo "xdebug.remote_host=192.168.99.100" >> /usr/local/etc/php/php.ini

RUN chmod +x entrypoint.sh
ENTRYPOINT ["sh", "entrypoint.sh", "service postfix start"]
