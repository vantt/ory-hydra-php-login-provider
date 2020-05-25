# Setup and use Ory Hydra  

## Run Quick Start   

Please follow the 5 minutes tutorial here for a quick demo
https://www.ory.sh/docs/next/hydra/5min-tutorial      

```      shell
    
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

```    shell
    
docker network create hydraguide      
    
```

### Setup database server   
```    shell
      
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
   oryd/hydra:v1.4 \     
   migrate sql --yes postgres://hydra:secret@ory-hydra-example--postgres:5432/hydra?sslmode=disable       
    
    
```

### Start  Hydra

```shell
    
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
  oryd/hydra:v1.4 serve all --dangerous-force-http
    
```
URLS_SELF_LOGIN: the URL of the Login Provider      
URLS_SELF_CONSENT: the URL of the Consent Provider      
URLS_SELF_LOGOUT: the URL of the Logout Provider      

## Create OAuth clients

### Read client info

``` shell

docker run --rm -it \
    --network=https-proxy \
    oryd/hydra:v1.4 \
        clients get client3 \
            --endpoint http://hydra_admin:4445 
            
```

### Delete a client

``` shell

docker run --rm -it \
    --network=https-proxy \
    oryd/hydra:v1.4 \
        clients delete theleague \
            --endpoint http://hydra_admin:4445 
        
```

### Create client reference

```shell script

docker run --rm -it oryd/hydra:v1.4 clients create --help

docker run --rm -it oryd/hydra:v1.4 clients get --help

docker run --rm -it oryd/hydra:v1.4 clients delete -- help

```
### Approach 1:

Use docker run, and set Hydra Admin-Endpoint using environment variable.  

```shell
    
docker run --rm -it --network=https-proxy \
    -e HYDRA_ADMIN_URL=http://hydra_admin:4445 \
    oryd/hydra:v1.4 \
        clients create --tls-termination --skip-tls-verify \
            --id client1 \
            --name "The Name of Client1" \
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

``` shell

docker run --rm -it --network=https-proxy \
    oryd/hydra:v1.4 \
        clients create --tls-termination --skip-tls-verify \
        --endpoint http://hydra_admin:4445 \
        --id client2 \
        --name "The Name of Client2" \
        --secret some_secret \
        --grant-types authorization_code,refresh_token \
        --response-types code,id_token \
        --scope openid,offline \
        --token-endpoint-auth-method client_secret_post \
        --callbacks http://127.0.0.1:5555/callback \
      
```

### Approach 3

The command below uses the docker network named https-proxy, so we can use the container name 'hydra' to access the admin-api url

```shell

docker run --rm -it \
    --network=https-proxy \
		oryd/hydra:v1.4 \
			clients create --tls-termination  --skip-tls-verify \
				--endpoint http://hydra_admin:4445 \
				--id client3 \
      	        --name "The Name of Client3" \
				--secret some-secret \
				--grant-types authorization_code,refresh_token,client_credentials,implicit \
				--response-types token,code,id_token \
				--scope openid,offline,photos.read \
                --token-endpoint-auth-method client_secret_post \
				--callbacks http://10.254.254.254:9010/callback

```
--callbacks: the client's callback url  



```shell

docker run --rm -it --network=https-proxy \
    oryd/hydra:v1.4 \
      clients create \
        --fake-tls-termination --skip-tls-verify \
        --endpoint "http://hydra_admin:4445" \
        --id theleague \
        --name "TheLeague Client" \
        --secret some-secret \
        --token-endpoint-auth-method client_secret_post \
        --grant-types authorization_code,refresh_token,client_credentials,implicit \
        --response-types token,code,id_token \
        --scope openid,offline_access,account.profile,photos.read \
        --callbacks "https://id.dev.mio/connect/hydra/check"

```

--callbacks: the client's callback url  

### Approach 4: Client Credentials Flow

A client only capable of performing the Client Credentials Flow can be created as follows:

```shell

docker run --rm -it \
    --network=https-proxy \
		oryd/hydra:v1.4 \
			clients create --fake-tls-termination --skip-tls-verify \
            --endpoint http://hydra_admin:4445 \
            --id machine \
            --secret some-secret \
            -g client_credentials
            
```

## Use Hydra ClientTest-App to test client authentication

Start a client-app listen at port 9010, then expose the port 9010 to host, do authentication grant.
(hydra has a demo client-app for client authentication testing)

```shell

docker run --rm -it \
    oryd/hydra:v1.4 \
        token user  \
            --endpoint http://sso.dev.mio \
            --client-id client3 \
            --client-secret some-secret \
            --scope openid,offline

docker run --rm -it \
    -p 9010:9010 \
    oryd/hydra:v1.4 \
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


```shell

MYIP=`ipconfig getifaddr en0` \
docker run --rm -it \
    -p 9010:9010 \
    oryd/hydra:v1.4 \
        token user --skip-tls-verify \
            --port 9010 \
            --auth-url http://192.168.5.119:4444/oauth2/auth \
            --token-url http://192.168.5.119:4444/oauth2/token \
            --client-id client1 \
            --client-secret some-secret \
            --scope openid,offline,photos.read
           
```

### Use docker-compose to run the test

```shell

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
		oryd/hydra:v1.4 \
			clients create  --tls-termination  --skip-tls-verify \
				--endpoint http://hydra_admin:4445 \
				--id oauthdebugger \
      	        --name "OAuth Debugger" \
				--secret some-secret \
				--grant-types authorization_code,refresh_token,client_credentials,implicit \
				--response-types token,code,id_token \
				--scope openid,offline,photos.read \
                --token-endpoint-auth-method client_secret_basic \
				--callbacks https://oauthdebugger.com/debug

```

### Step 2: Test Authorization Code Grant

- Navigate to: https://oauthdebugger.com/
- Then fill the values to the fields:

| Field Name   | Value  |
|---|---|
| Authorize URI  | http://sso.dev.mio/oauth2/auth   |
| Redirect Url  | https://oauthdebugger.com/debug |
| Client Id  | oauthdebugger  |
| Scope  | openid offline photos.read  |
| State  | a-random-string  |

#### Or submit this url on your browser

```
For Authorization Code 
http://sso.dev.mio/oauth2/auth?client_id=oauthdebugger&redirect_uri=https%3A%2F%2Foauthdebugger.com%2Fdebug&scope=openid%20offline%20photos.read&response_type=code&response_mode=query&state=asdfkjahsdflkajshflaksjhflaskjfhlaskdfjhaslkfjhaslhdfjk&nonce=o460bjdflq

For Access Token
http://sso.dev.mio/oauth2/auth?client_id=oauthdebugger&redirect_uri=https%3A%2F%2Foauthdebugger.com%2Fdebug&scope=openid%20offline%20photos.read&response_type=token&response_mode=query&state=asdfkjahsdflkajshflaksjhflaskjfhlaskdfjhaslkfjhaslhdfjk&nonce=o460bjdflq

```

