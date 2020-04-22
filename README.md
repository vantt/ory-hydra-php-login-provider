# Introduction

PHP Login Provider and Consent Provider for Ory Hydra OAuth2 Authentication Server.

# Table of Content
- [OAuth2 Research](doc/oauth2.md)
- [Hydra Guide](doc/ory-hydra.md)
- Project Creation
- [Tools & References](doc/tools.md)

# Deployment Concern:
-  [Symfony Behind Proxy](https://symfony.com/doc/current/deployment/proxies.html#but-what-if-the-ip-of-my-reverse-proxy-changes-constantly)
-  [Symfony Https](https://symfony.com/doc/master/cloud/cookbooks/https.html) 

# Project Creation

Belows are commands used to create this skeleton

## Project Init
composer create-project symfony/website-skeleton roadrunner
composer require


## Run Login & Consent app
cd build/docker/nginx
docker-compose up
