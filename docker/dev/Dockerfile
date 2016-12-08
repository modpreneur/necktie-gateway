FROM modpreneur/necktie-dev:1.0.16

MAINTAINER Tomáš Jančar <jancar@modpreneur.com>

RUN  apt-get install python-setuptools -y \
  && easy_install supervisor


# Install app
ADD . /var/app

RUN echo "export PHP_IDE_CONFIG=\"serverName=necktie.gateway\"" >> /etc/bash.bashrc

EXPOSE 8666
EXPOSE 9005

# terminal env for nano
ENV TERM xterm

RUN chmod +x ./docker/dev/entrypoint.sh
ENTRYPOINT ["sh", "./docker/dev/entrypoint.sh"]