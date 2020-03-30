# Setup and use Ory Hydra  
  
## Run Quick Start   

Please follow the 5 minutes tutorial here for a quick demo
https://www.ory.sh/docs/next/hydra/5min-tutorial      
  
```      
    
git clone https://github.com/ory/hydra.git      
      
cd hydra      
        
docker-compose \      
    -f quickstart.yml \      
    -f quickstart-mysql.yml \      
    -f quickstart-tracing.yml \      
    -f quickstart-prometheus.yml \      
    up --build     
     
```  

## Step by step install   
### Create a shared docker network
   
```    
    
docker network create hydraguide      
    
```  
  
### Setup database server   
```    
      
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
    
    
```  
  
### Start  Hydra

```
    
docker run -d \      
  --name ory-hydra-example--hydra \
  --network hydraguide \
  -p 4444:4444 \
  -p 4445:4445 \
  -e SECRETS_SYSTEM=123456789abcdefgh \
  -e DSN=postgres://hydra:secret@ory-hydra-example--postgres:5432/hydra?sslmode=disable \
  -e URLS_SELF_ISSUER=https://localhost:9000/ \
  -e URLS_CONSENT=http://localhost:8080/consent \
  -e URLS_LOGIN=http://localhost:8080/login \
  oryd/hydra:v1.3.2 serve all --dangerous-force-http
    
```  
URLS_SELF_LOGIN: the URL of the Login Provider      
URLS_SELF_CONSENT: the URL of the Consent Provider      
URLS_SELF_LOGOUT: the URL of the Logout Provider      
  
## Create OAuth clients
### Approach 1:

Use docker run, and set Hydra Admin-Endpoint using environment variable.  
  
```
    
docker run --rm -it \
    -e HYDRA_ADMIN_URL=http://10.254.254.254:4445 \
    oryd/hydra:v1.3.2-alpine \
        clients create --skip-tls-verify \
            --id client1 \
            --secret some-secret \
            --grant-types authorization_code,refresh_token,client_credentials,implicit \
            --response-types token,code,id_token \
            --scope openid,offline,photos.read \
            --callbacks http://10.254.254.254:9010/callback

```
The above command use HYDRA_ADMIN_URL environment variable to set the admin-api url
--callbacks: is the client's callback url
  
### Approach 2

Set Hydra admin endpoint using --endpoint  (port 4445)
  
``` 

docker run --rm -it \
    oryd/hydra:v1.3.2-alpine \
        --endpoint http://127.0.0.1:4445 \
        --id client2 \
        --secret some_secret \
        --grant-types authorization_code,refresh_token \
        --response-types code,id_token \
        --scope openid,offline \
        --callbacks http://127.0.0.1:5555/callback \
      
```  

### Approach 3

The command below uses the docker network named https-proxy, so we can use the container name 'hydra' to access the admin-api url

```

docker run --rm -it \
    --network=https-proxy \
	oryd/hydra:v1.3.2-alpine \
		clients create --skip-tls-verify \
			--endpoint http://hydra:4445 \
			--id client3 \
			--secret some-secret \
			--grant-types authorization_code,refresh_token,client_credentials,implicit \
			--response-types token,code,id_token \
			--scope openid,offline,photos.read \
			--callbacks http://10.254.254.254:9010/callback

```
--callbacks: the client's callback url  

## Use Hydra ClientTest-App to test client authentication

Start a client-app listen at port 9010, then expose the port 9010 to host, do authentication grant.
(hydra has a demo client-app for client authentication testing)

```

docker run --rm -it \
    -p 9010:9010 \
    oryd/hydra:v1.3.2-alpine \
        token user --skip-tls-verify \
            --port 9010 \
            --endpoint http://sso.dev.mio \
            --client-id client1 \
            --client-secret some-secret \
            --scope openid,offline,photos.read \
            --redirect http://10.254.254.254:9010/callback

```
-p 9010:9010 expose client-app's port (container port) to host machine
--port 9010 start the client-app and listen at port 9010


```

MYIP=`ipconfig getifaddr en0` \
docker run --rm -it \
    -p 9010:9010 \
    oryd/hydra:v1.3.2-alpine \
        token user --skip-tls-verify \
            --port 9010 \
            --auth-url http://192.168.5.119:4444/oauth2/auth \
            --token-url http://192.168.5.119:4444/oauth2/token \
            --client-id client1 \
            --client-secret some-secret \
            --scope openid,offline,photos.read
           
```
  
### Use docker-compose to run the test

```

docker-compose -f quickstart.yml exec hydra  \
    hydra token user  \
        --port 5555 \
        --endpoint http://127.0.0.1:4444/ \
        --client-id auth-code-client \
        --client-secret secret \
        --scope openid,offline
    
```

## Use a WebClient to test client authentication
https://oauthdebugger.com/

### Step 1: create a debug Client

```shell script

docker run --rm -it \
    --network=https-proxy \
	oryd/hydra:v1.3.2-alpine \
		clients create --skip-tls-verify \
			--endpoint http://hydra:4445 \
			--id oauthdebugger \
			--secret some-secret \
			--grant-types authorization_code,refresh_token,client_credentials,implicit \
			--response-types token,code,id_token \
			--scope openid,offline,photos.read \
			--callbacks https://oauthdebugger.com/debug

```

### Step 2: Test the client 

- Navigate to: https://oauthdebugger.com/
- Then fill the values to the fields:

| Field Name   | Value  |
|---|---|
| Authorize URI  | http://sso.dev.mio/oauth2/auth   |
| Redirect Url  | https://oauthdebugger.com/debug |
| Client Id  | oauthdebugger  |
| Scope  | openid,offline,photos.read  |
| State  | a-random-string  |

#### Or submit this url on your browser

```

http://sso.dev.mio/oauth2/auth?client_id=oauthdebugger&redirect_uri=https%3A%2F%2Foauthdebugger.com%2Fdebug&scope=openid%20offline%20photos.read&response_type=code&response_mode=query&state=asdfkjahsdflkajshflaksjhflaskjfhlaskdfjhaslkfjhaslhdfjk&nonce=o460bjdflq

```

