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
Please follow the 5 minutes tutorial here
https://www.ory.sh/docs/next/hydra/5min-tutorial

`
git clone https://github.com/ory/hydra.git

cd hydra

URLS_SELF_LOGIN="http://localhost:8080/login" \
URLS_SELF_CONSENT="http://localhost:8080/consent" \
URLS_SELF_LOGOUT="http://localhost:8080/logout" \
docker-compose \
    -f quickstart.yml \
	-f quickstart-mysql.yml \
	-f quickstart-tracing.yml \
	-f quickstart-prometheus.yml \
	up --build
`

$ docker network create hydraguide

docker run \
  --network hydraguide \
  --name ory-hydra-example--postgres \
  -e POSTGRES_USER=hydra \
  -e POSTGRES_PASSWORD=secret \
  -e POSTGRES_DB=hydra \
  -d postgres:9.6
  
/// create database structure 
docker run -it --rm \
  --network hydraguide \
  oryd/hydra:v1.3.2 \
  migrate sql --yes postgres://hydra:secret@ory-hydra-example--postgres:5432/hydra?sslmode=disable	

/// run hydra 
docker run -d \
  --name ory-hydra-example--hydra \
  --network hydraguide \
  -p 4444:4444 \
  -p 4445:4445 \
  -e SECRETS_SYSTEM=123456789abcdefgh \
  -e DSN=postgres://hydra:secret@ory-hydra-example--postgres:5432/hydra?sslmode=disable	 \
  -e URLS_SELF_ISSUER=https://localhost:9000/ \
  -e URLS_CONSENT=http://localhost:8080/consent \
  -e URLS_LOGIN=http://localhost:8080/login \
  oryd/hydra:v1.3.2 serve all --dangerous-force-http 


  /// create client
 /// create client
  docker run --rm -it \
  -e HYDRA_ADMIN_URL=http://YOUR_EXPOSED_IP:4445 \
  --network hydraguide \
  oryd/hydra:v1.3.2 \
  clients create --skip-tls-verify \
    --id client2 \
    --secret some-secret \
    --grant-types authorization_code,refresh_token,client_credentials,implicit \
    --response-types token,code,id_token \
    --scope openid,offline,photos.read \
    --callbacks http://127.0.0.1:9010/callback


 /// test client 
  docker run --rm -it \
    --network hydraguide \
    -p 9010:9010 \
    oryd/hydra:v1.3.2 \
    token user --skip-tls-verify \
      --port 9010 \
      --auth-url http://192.168.5.119:4444/oauth2/auth \
      --token-url http://192.168.5.119:4444/oauth2/token \
      --client-id client4 \
      --client-secret some-secret \
      --scope openid,offline,photos.read
    
## Create Hydra Client
`
docker-compose -f quickstart.yml exec hydra \
    hydra clients create \
    --endpoint http://127.0.0.1:4445 \
    --callbacks http://127.0.0.1:5555/callback \
    --id auth-code-client \
    --secret secret \
    --grant-types authorization_code,refresh_token \
    --response-types code,id_token \
    --scope openid,offline
`

## Perform the OAuth 2.0 Authorization Code Grant
`
docker-compose -f quickstart.yml exec hydra  \
	hydra token user  \
	--endpoint http://127.0.0.1:4444/ \
	--client-id auth-code-client  \
	--client-secret secret \
	--port 5555 \
	--scope openid,offline
`

## Run Login & Consent app
cd build/docker/nginx
docker-compose up


# References:
### About Ory Hydra
- https://github.com/ory/hydra-login-consent-node
- https://www.ory.sh/docs/hydra/implementing-consent
- https://www.ory.sh/docs/hydra/5min-tutorial

- https://oauth2.thephpleague.com/

### About RoadRunner
- https://github.com/baldinof/roadrunner-bundle
- https://github.com/MarkusCooks/php-roadrunner