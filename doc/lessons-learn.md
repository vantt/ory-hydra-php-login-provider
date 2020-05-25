### Client Creation:
- Client redirect_url must be exactly as when you create it.
- For the **auth/token** end point, if you want to use POST method, you should create the client with options:
  "token_endpoint_auth_method": "client_secret_post",
  https://www.ory.sh/hydra/docs/debugging/#oauth-20-client-id-and-secret-are-sent-in-body-instead-of-header

- If Hydra run as **http** with **tls-termination** enable, most of the case, it will be fine with the public-end point (4444) because we often expose this port through a https gateway.
  But the Administration-Endpoint (4445), often, we dont expose it, and we will connect directly to it using pure http.
  In thise case, we must add a X-FORWARDED-PROTO:https header to simulate a proxy. 

### Json web token - JWT
By default Hydra issue Opaque Access Token and ORY Oathkeeper! will forward jwt to your backend service.
If you want to use JWT yourself, change the settings:

```
# version 1.3, docker file (this is working)
environment:
            - STRATEGIES_ACCESS_TOKEN=jwt
            #- OAUTH2_ACCESS_TOKEN_STRATEGY=jwt
            - OIDC_SUBJECT_IDENTIFIERS_SUPPORTED_TYPES=public

# version 1.4, config file (this is still not working)
strategies:
  access_token: jwt 

```

### Refresh Token
[See Document](https://www.ory.sh/hydra/docs/implementing-consent/#oauth-20-refresh-tokens)
-   OAuth 2.0 Refresh Tokens are issued only when an Authorize Code Flow (response_type=code) or an OpenID Connect Hybrid Flow with an Authorize Code Response Type (response_type=code+...) is executed. OAuth 2.0 Refresh Tokens are not returned for Implicit or Client Credentials grants.
-   Additionally, each OAuth 2.0 Client that wants to request an OAuth 2.0 Refresh Token must be allowed to request scope offline_access. When performing an OAuth 2.0 Authorize Code Flow, the offline_access value must be included in the requested OAuth 2.0 Scope

https://www.ory.sh/hydra/docs/implementing-consent/#oauth-20-refresh-tokens

# Deployment Concern:
-  [Symfony Behind Proxy](https://symfony.com/doc/current/deployment/proxies.html#but-what-if-the-ip-of-my-reverse-proxy-changes-constantly)
-  [Symfony Https](https://symfony.com/doc/master/cloud/cookbooks/https.html) 

