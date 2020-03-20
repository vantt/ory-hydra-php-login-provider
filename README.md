# symfony-roadrunner-skeleton
Symfony project template with RoadRunner as server.

# Introduction
This project template doing fresh-setup for Symfony Web/API project running with RoadRunner (https://roadrunner.dev/), high-performance PHP application server.
- With RoadRunner we can serve a Symfony (php) restful webservice through a small application-server developed using Golang.
- With RoadRunner we can expose Symfony application through restful http/http2 or Grpc.

# Project Init
Belows are commands used to create this skeleton

composer create-project symfony/website-skeleton roadrunner
composer require

# Running:
## Run Ory Hydra
docker-compose -f quickstart.yml \
    -e URLS_SELF_LOGIN="http://localhost:8080/login" \
    -e URLS_SELF_CONSENT="http://localhost:8080/consent" \
    -e URLS_SELF_LOGOUT="http://localhost:8080/logout" \
	-f quickstart-mysql.yml \
	-f quickstart-tracing.yml \
	-f quickstart-prometheus.yml \
	up --build

## Create Hydra Client
docker-compose -f quickstart.yml exec hydra \
    hydra clients create \
    --endpoint http://127.0.0.1:4445 \
    --id auth-code-client \
    --secret secret \
    --grant-types authorization_code,refresh_token \
    --response-types code,id_token \
    --scope openid,offline \
    --callbacks http://127.0.0.1:5555/callback

## Perform the OAuth 2.0 Authorization Code Grant
docker-compose -f quickstart.yml exec hydra  \
	hydra token user  \
	--client-id auth-code-client  \
	--client-secret secret \
	--endpoint http://127.0.0.1:4444/ \
	--port 5555 \
	--scope openid,offline


## Run Login & Consent app
cd build/docker/nginx
docker-compose up


# References:
https://github.com/ory/hydra-login-consent-node
https://www.ory.sh/docs/hydra/implementing-consent
https://www.ory.sh/docs/hydra/5min-tutorial

https://oauth2.thephpleague.com/
https://github.com/baldinof/roadrunner-bundle
https://github.com/MarkusCooks/php-roadrunner