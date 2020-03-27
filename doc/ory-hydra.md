# Setup and use Ory Hydra  
  
## Run Quick Start   
Please follow the 5 minutes tutorial here      
https://www.ory.sh/docs/next/hydra/5min-tutorial      
  
```      
    
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
  -e DSN=postgres://hydra:secret@ory-hydra-example--postgres:5432/hydra?sslmode=disable     \      
  -e URLS_SELF_ISSUER=https://localhost:9000/ \      
  -e URLS_CONSENT=http://localhost:8080/consent \      
  -e URLS_LOGIN=http://localhost:8080/login \      
  oryd/hydra:v1.3.2 serve all --dangerous-force-http       
    
```  
  
## Create Hydra clients   
### Approach 1:   
Use docker run, and set Hydra Admin Endpoint using environment variable.  
  
```    
    
docker run --rm -it \
  -e HYDRA_ADMIN_URL=http://YOUR_PUBLIC_IP:4445 \
  oryd/hydra:v1.3.2-alpine \
  clients create --skip-tls-verify \
    --id client1 \
    --secret some-secret \
    --grant-types authorization_code,refresh_token,client_credentials,implicit \
    --response-types token,code,id_token \
    --scope openid,offline,photos.read \
    --callbacks http://127.0.0.1:9010/callback
        
docker run --rm -it \
  -e HYDRA_ADMIN_URL=http://YOUR_PUBLIC_IP:4445 \
  oryd/hydra:v1.3.2-alpine \
  clients create --skip-tls-verify \
    --id client1 \
    --secret some-secret \
    --grant-types authorization_code,refresh_token,client_credentials,implicit \
    --response-types token,code,id_token \
    --scope openid,offline,photos.read \
    --callbacks http://127.0.0.1:9010/callback      

MYIP=`ipconfig getifaddr en0` \
docker run --rm -it \
	oryd/hydra:v1.3.2-alpine \
		clients create --skip-tls-verify \
			--endpoint http://192.168.5.145:4445 \
			--id client2 \
			--secret some-secret \
			--grant-types authorization_code,refresh_token,client_credentials,implicit \
			--response-types token,code,id_token \
			--scope openid,offline,photos.read \
			--callbacks http://127.0.0.1:9010/callback        

```  
  
--callbacks: the client's callback url  
  
### Approach 2  
  
Use docker compose, set Hydra admin endpoint using --endpoint  
  
```      
    
docker-compose -f quickstart.yml exec hydra \      
    hydra clients create \     
        --endpoint http://127.0.0.1:4445 \       
        --id testclient1 \     
        --secret some_secret \     
        --grant-types authorization_code,refresh_token \     
        --response-types code,id_token \     
        --scope openid,offline`\                  
        --callbacks http://127.0.0.1:5555/callback \   
      
```  
  
--endpoint: hydra Admin endpoint    
--callbacks: client's callback url   

## Test Client   
```    
Start a client at 9010

MYIP=`ipconfig getifaddr en0` \
docker run --rm -it \
    -p 9010:9010 \
    oryd/hydra:v1.3.2-alpine \
        token user --skip-tls-verify \
            --port 9010 \
            --endpoint http://$MYIP:4444 \
            --client-id client1 \
            --client-secret some-secret \
            --scope openid,offline,photos.read

```
-p 9010:9010 map client port (container port) to host machine
--port 9010 start the client and listen at port 9010

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
  
### Perform the OAuth 2.0 Authorization Code Grant   
```      
docker-compose -f quickstart.yml exec hydra  \      
    hydra token user  \     
        --port 5555 \    
        --endpoint http://127.0.0.1:4444/ \     
        --client-id auth-code-client  \     
        --client-secret secret \     
        --scope openid,offline    
    
```