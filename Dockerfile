FROM modpreneur/apache-framework:0.4

MAINTAINER Tomáš Jančar <jancar@modpreneur.com>

RUN docker-php-ext-install pdo pdo_mysql

RUN  apt-get install python-setuptools -y \
  && easy_install supervisor

# Install app
ADD . /var/app

EXPOSE 80 8666 9005

RUN chmod +x entrypoint.sh
ENTRYPOINT ["sh", "entrypoint.sh"]
