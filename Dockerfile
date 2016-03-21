FROM modpreneur/apache-framework:0.2

MAINTAINER Tomáš Jančar <jancar@modpreneur.com>

# Install app
ADD . /var/app

RUN echo "export PHP_IDE_CONFIG=\"serverName=necktie\"" >> /etc/bash.bashrc

EXPOSE 8666

RUN chmod +x entrypoint.sh
ENTRYPOINT ["sh", "entrypoint.sh"]