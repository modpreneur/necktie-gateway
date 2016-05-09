FROM modpreneur/apache-framework:0.3

MAINTAINER Tomáš Jančar <jancar@modpreneur.com>

COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/supervisor-manager.sh /opt/supervisor-manager.sh

# Install app
ADD . /var/app

RUN echo "export PHP_IDE_CONFIG=\"serverName=necktie\"" >> /etc/bash.bashrc

EXPOSE 8666

RUN apt-get -y install \
    nano \
    phpunit

RUN composer global require codeception/codeception \
    && echo "alias codecept=\"php /var/app/vendor/codeception/codeception/codecept\"" >> /etc/bash.bashrc \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && echo "xdebug.remote_enable=1" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.idekey=PHPSTORM" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.remote_connect_back=1" >> /usr/local/etc/php/php.ini \
    && echo "alias composer=\"php -n -d extension=mbstring.so -d extension=zip.so -d extension=bcmath.so /usr/bin/composer\"" >> /etc/bash.bashrc

# terminal env for nano
ENV TERM xterm

RUN chmod +x ./docker/dev/entrypoint.sh
ENTRYPOINT ["sh", "./docker/dev/entrypoint.sh"]