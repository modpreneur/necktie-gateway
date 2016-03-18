FROM modpreneur/apache-framework:0.1

# Install app
ADD . /var/app

#RUN echo "export PHP_IDE_CONFIG=\"serverName=necktie\"" >> /etc/bash.bashrc

EXPOSE 8666

COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN chmod +x entrypoint.sh
ENTRYPOINT ["sh", "entrypoint.sh"]