# necktie-gateway

## install
create `docker/.env` file. It should be same as `.env` file in necktie project

## FAQ
### How run local session?
1. `docker-compose up` will start gateway
2. Update `gateway_host` parameter in `parameters.yml` in necktie project to your local IP address (NOT localhost or 127..1). 
This redirects necktie to your local necktie-gateway

### How can I see running consumers (how to see supervisor tasks)
1. Look into `docker-compose.yml` and look for exposed supervisor port (usually 9005).
2. Go to `localhost:<port>` and enter user name and password. 
3. You can find password in Necktie project: `app/config/parametes.yml`.