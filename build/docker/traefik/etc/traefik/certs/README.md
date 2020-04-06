# Self Signed Certificate Creation

## 1. Install mkcert 

Please follow the installation instruction here. 
https://github.com/FiloSottile/mkcert

Here, I includes a pre-compiled mkcert for linx/windows.


## 2. Create certificate using mkcert

```
export CAROOT=$(pwd)

./mkcert -key-file myKey.key -cert-file myKey.crt vantt.mio "*.vantt.mio" symfony.mio "*.symfony.mio" dev.mio "*.dev.mio" localhost 127.0.0.1 ::1

```

Above command will create a self-signed certificate for domains (vantt.mio, *.vantt.mio, dev.mio, *.dev.mio, symfony.mio, *.symfony.mio using a pair of CaRoott files (named: rootCA.pem & rootCA-key.pem ) on current directory. Output key will be saved on myKey.key and myKey.crt

## 2. Install rootCA using mkcert

Below commands will automatically install  Certificate Authority(RootCA) for most major browsers running on your machine.

#### For linux

```
./linux_mkcert_install.sh
```


#### For window

```
Run window_mkcert_install.bat
```

## 3. Verify certificates using openssl
https://www.poftut.com/use-openssl-s_client-check-verify-ssltls-https-webserver/

```

openssl verify -verbose -CAfile rootCA.pem yourCertificate.crt

openssl verify -verbose -CAfile rootCA.pem myKey.crt

```

Open a webserver listens at port 8443 to verify the certificate.

```

openssl s_server -accept 8443 -www -key example.pev+1-key.pem -cert example.pev+1.pem 

openssl s_server -accept 8443 -www -key myKey.key -cert myKey.crt 

```

Now type on the browser: http://your_test_domain:8443
or

openssl s_client -connect acb.dev.mio:8443 -CAfile rootCA.pem