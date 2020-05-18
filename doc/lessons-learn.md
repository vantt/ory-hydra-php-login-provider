### Client Creation:
- Client redirect_url must be exactly as when you create it.
- For the auth/token end point, if you want to use POST method, you should create the client with options:
  "token_endpoint_auth_method": "client_secret_post",
  https://www.ory.sh/hydra/docs/debugging/#oauth-20-client-id-and-secret-are-sent-in-body-instead-of-header

- If Hydra run as Http with tls-termination enable, most of the case, it will be fine with the public-end point (4444) because we often expose this port through a https gateway.
  But the Administration-Endpoint (4445), often, we dont expose it, and we will connect directly to it using pure http.
  In thise case, we must add a X-FORWARDED-PROTO:https header to simulate a proxy. 
  
### Json web token - JWT
By default Hydra issue Opaque Access Token and ORY Oathkeeper! will forward jwt to your backend service.
If you want to use JWT yourself, change the settings:

```

strategies:
  access_token: jwt 

```
