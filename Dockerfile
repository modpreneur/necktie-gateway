FROM modpreneur/apache-framework:0.3

MAINTAINER Tomáš Jančar <jancar@modpreneur.com>

COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/supervisor-manager.sh /opt/supervisor-manager.sh

# Install app
ADD . /var/app

RUN echo "export PHP_IDE_CONFIG=\"serverName=necktie\"" >> /etc/bash.bashrc

EXPOSE 8666

RUN chmod +x entrypoint.sh
ENTRYPOINT ["sh", "entrypoint.sh"]