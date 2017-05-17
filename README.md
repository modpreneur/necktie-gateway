# necktie-gateway

## install
create `docker/.env` file. It should be same as `.env` file in necktie project

## FAQ
### Don't miss
- We recommend to look into all possible logs during debug. How to do that is explained below.
- Before restarting consumer, we recommend to clear its log. **Its not automatic!**
- If you get stuck, and this page didn't help you. Don't hesitate to ask.

### How can I run local session?
1. `docker-compose up` will start gateway
2. Update `gateway_host` parameter in `parameters.yml` in necktie project to your local IP address (NOT localhost or 127..1). 
This redirects necktie to your local necktie-gateway
3. Its recommended to update also `parameters.yml` of necktie-gateway project. Set `rabbit_url` and `elastic_host` to your local IP address.

### How can I see RabitMQ messages?
1. Open rabiMQ url. Its usually [localhost:15672](localhost:15672).
2. If url doesn't work, you can find correct port in necktie [docker-compose.yml](https://github.com/modpreneur/necktie/blob/dev/docker-compose.yml) file
2. Login credentials can be found in necktie [readme](https://github.com/modpreneur/necktie/tree/dev#rabbitmq-credentials) file

### How can I see running consumers (how to see supervisor tasks)
1. Look into `docker-compose.yml` and look for exposed supervisor port (usually 9005).
2. Go to `localhost:<port>` and enter user name and password. 
3. You can find password in Necktie project: `app/config/parametes.yml`.

### How can I see error log of consumers?
1. Go to supervisor page.
2. Click to name of consumer. You will be redirected to consumer log page.

### How can I see errors of gateway itself?
1. Go to [localhost:9080/app_dev.php/](localhost:9080/app_dev.php/).
2. In left menu click to `Logger`


