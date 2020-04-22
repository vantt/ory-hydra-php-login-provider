# References:

### OAuth2
-   https://oauth2.thephpleague.com/
-   https://oauthdebugger.com
-   https://oidcdebugger.com/

### About Ory Hydra
-   https://github.com/ory/hydra-login-consent-node
-   https://www.ory.sh/docs/hydra/implementing-consent
-   https://www.ory.sh/docs/hydra/5min-tutorial


### About RoadRunner
-   https://github.com/baldinof/roadrunner-bundle
-   https://github.com/MarkusCooks/php-roadrunner

### SSL
ADD your_ca_root.crt /usr/local/share/ca-certificates/foo.crt
RUN chmod 644 /usr/local/share/ca-certificates/foo.crt && update-ca-certificates


# Install ca-certificates
# Please locate cert_file_name.crt file in the same directory as Dockerfile.
COPY cert_file_name.crt /usr/share/ca-certificates/
RUN echo cert_file_name.crt >> /etc/ca-certificates.conf
RUN update-ca-certificates