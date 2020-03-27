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