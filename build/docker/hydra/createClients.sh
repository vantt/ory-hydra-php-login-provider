#!/bin/bash

export HYDRA_IMG=oryd/hydra:v1.3

docker run --rm -it --network=https-proxy ${HYDRA_IMG} \
      clients create \
        --fake-tls-termination --skip-tls-verify \
        --endpoint "http://hydra:4445" \
        --id theleague \
        --name "TheLeague Client" \
        --secret some-secret \
        --token-endpoint-auth-method client_secret_post \
        --grant-types authorization_code,refresh_token,implicit \
        --response-types token,code,id_token,refresh_token \
        --scope openid,offline,offline_access,account.profile,account.read,photos.read \
        --callbacks "https://id.dev.mio/test-connect/hydra/check"

docker run --rm -it --network=https-proxy ${HYDRA_IMG} \
      clients create \
        --fake-tls-termination --skip-tls-verify \
        --endpoint "http://hydra:4445" \
        --id oauthdebugger \
        --name "OAuthDebugger Client" \
        --secret some-secret \
        --token-endpoint-auth-method client_secret_basic \
        --grant-types authorization_code,refresh_token,implicit \
        --response-types token,code,id_token,refresh_token \
        --scope openid,offline,offline_access,account.profile,account.read,photos.read \
        --callbacks https://oauthdebugger.com/debug

# Machine Client: for client credentials
docker run --rm -it --network=https-proxy ${HYDRA_IMG} \
      clients create \
        --fake-tls-termination --skip-tls-verify \
        --endpoint "http://hydra:4445" \
        --id machine \
        --secret some-secret \
        --token-endpoint-auth-method client_secret_post \
        --grant-types client_credentials,refresh_token \
        --response-types token,id_token,refresh_token