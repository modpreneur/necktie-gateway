FROM modpreneur/apache-framework:0.3

MAINTAINER Tomáš Jančar <jancar@modpreneur.com>


RUN docker-php-ext-install pdo pdo_mysql

RUN  apt-get install python-setuptools -y \
  && easy_install supervisor

RUN mkdir /etc/supervisor
RUN echo_supervisord_conf > /etc/supervisord.conf


COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Install app
ADD . /var/app

RUN echo "export PHP_IDE_CONFIG=\"serverName=necktie\"" >> /etc/bash.bashrc

EXPOSE 8666
EXPOSE 9005

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
