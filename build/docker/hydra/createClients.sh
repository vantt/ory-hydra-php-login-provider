#!/bin/bash

export CALLBACK_URL=http://10.254.254.254:9010/callback

docker run --rm -it \
    -e HYDRA_ADMIN_URL=http://10.254.254.254:4445 \
    oryd/hydra:v1.3.2-alpine \
        clients create --skip-tls-verify \
            --id client1 \
            --secret some-secret \
            --grant-types authorization_code,refresh_token,client_credentials,implicit \
            --response-types token,code,id_token \
            --scope openid,offline,photos.read \
            --callbacks ${CALLBACK_URL}

docker run --rm -it \
	oryd/hydra:v1.3.2-alpine \
		clients create --skip-tls-verify \
			--endpoint http://10.254.254.254:4445 \
			--id client2 \
			--secret some-secret \
			--grant-types authorization_code,refresh_token,client_credentials,implicit \
			--response-types token,code,id_token \
			--scope openid,offline,photos.read \
			--callbacks ${CALLBACK_URL}


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
			--callbacks ${CALLBACK_URL}

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