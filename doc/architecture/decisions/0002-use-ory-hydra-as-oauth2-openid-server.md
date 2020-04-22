# 2. Use Ory Hydra as OAuth2 and OpenId server

Date: 2020-04-22

## Status

Accepted

## Context
We don't have expertise on security to implement our own Oauth2 Server, so using a ready-made solution is the way.

## Decision

After evaluate many solutions:
-   Laravel Passport
-   https://github.com/trikoder/oauth2-bundle
-  [KeyCloak](https://www.keycloak.org/) 

I made a decision to use [Ory Hydra](https://www.ory.sh/hydra) to implement our first OAuth2 server.

## Reasons:
-   Golang, very small and lightweight, easy to deploy
-   We can re-use our user-management system as a Identity Provider for Hydra
-   Hydra opens many more authorization solutions

## Consequences

### What becomes easier:
-   We rapidly have an OAuth Server 
-   We can start moving to micro-service architecture

### What become more difficult:


### Risks introduced
-   We don't understand the code
