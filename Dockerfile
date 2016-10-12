FROM modpreneur/necktie

MAINTAINER Tomáš Jančar <jancar@modpreneur.com>

RUN  apt-get install python-setuptools -y \
  && easy_install supervisor

# Install app
ADD . /var/app

EXPOSE 80 8666 9005

RUN chmod +x entrypoint.sh
ENTRYPOINT ["sh", "entrypoint.sh"]
