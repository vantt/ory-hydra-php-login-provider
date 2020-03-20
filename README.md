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
cd build/docker/roadrunner
docker-compose up

http://localhost:8080/hello

# References:
https://github.com/baldinof/roadrunner-bundle
https://github.com/MarkusCooks/php-roadrunner