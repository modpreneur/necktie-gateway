FROM modpreneur/apache-framework:1.0

MAINTAINER Tomáš Jančar <jancar@modpreneur.com>

RUN  apt-get install python-setuptools -y \
  && easy_install supervisor

ADD . /var/app

RUN composer install --no-dev --optimize-autoloader --no-scripts --prefer-dist --no-interaction

EXPOSE 80 8666 9005

RUN chmod +x entrypoint.sh
ENTRYPOINT ["sh", "entrypoint.sh"]
