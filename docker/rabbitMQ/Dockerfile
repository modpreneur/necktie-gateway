FROM rabbitmq:3-management

#add default definitions for the rabbitMQ
ADD ./definitions.json /etc/rabbitmq/default_definitions.json

#add config for the rabbitMQ
#section load_definitions @see https://www.rabbitmq.com/management.html#load-definitions
RUN echo '[ { rabbit, [ { loopback_users, [ ] }] }, {rabbitmq_management, [{load_definitions, "/etc/rabbitmq/default_definitions.json"}]} ].' > /etc/rabbitmq/rabbitmq.config

# Define default command
CMD ["rabbitmq-server"]