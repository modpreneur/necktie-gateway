FROM modpreneur/necktie:dev

MAINTAINER Martin Kolek <kolek@modpreneur.com>

# Install app
ADD . /var/app

EXPOSE 9001

RUN chmod +x entrypoint.sh

RUN echo "export PHP_IDE_CONFIG=\"serverName=necktie\"" >> /etc/bash.bashrc

ENTRYPOINT ["sh", "entrypoint.sh", "service postfix start"]