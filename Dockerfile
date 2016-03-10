FROM modpreneur/necktie:dev

MAINTAINER Martin Kolek <kolek@modpreneur.com>

# Install app
ADD . /var/app

RUN chmod +x entrypoint.sh

RUN echo "export PHP_IDE_CONFIG=\"serverName=necktie\"" >> /etc/bash.bashrc

RUN apt-get install wget  \
    && wget https://phar.phpunit.de/phpunit.phar \
    && chmod +x phpunit.phar \
    && mv phpunit.phar /usr/local/bin/phpunit

EXPOSE 8666


RUN rm /etc/supervisor/conf.d/supervisord.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

ENTRYPOINT ["sh", "entrypoint.sh", "service postfix start"]