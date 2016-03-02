FROM modpreneur/necktie:dev

MAINTAINER Martin Kolek <kolek@modpreneur.com>

# Install app
ADD . /var/app


RUN chmod +x entrypoint.sh
ENTRYPOINT ["sh", "entrypoint.sh", "service postfix start"]