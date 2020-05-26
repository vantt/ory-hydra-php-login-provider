<?php

namespace App\Tests\Controller;

use Symfony\Component\Panther\PantherTestCase;
use Symfony\Component\Panther\Client;

class ClientCredentialsTest extends PantherTestCase {
    const BASEURL = 'https://id.dev.mio';

    /**
     * Please creat test-user using following command
     *
     *  Machine Client: for client credentials
     *  docker run --rm -it --network=https-proxy ${HYDRA_IMG} \
     *      clients create \
     *      --fake-tls-termination --skip-tls-verify \
     *      --endpoint "http://hydra:4445" \
     *      --id machine \
     *      --secret some-secret \
     *      --token-endpoint-auth-method client_secret_post \
     *      --grant-types client_credentials \
     *      --response-types token,id_token
     *
     */
    public function test_ClientCredentials() {
        $options = [
          'external_base_uri'        => self::BASEURL,
          'connection_timeout_in_ms' => 5000,
          'request_timeout_in_ms'    => 120000,
        ];

        // $client = Client::createChromeClient(null, null, $options, self::BASEURL);
        $client = static::createPantherClient($options);
        $client->followRedirects(true);
        $client->followMetaRefresh(true);
        $client->request('GET', '/test-connect/client-credentials');

        $crawler = $client->waitFor('pre');
        $token   = json_decode($crawler->getText(), true);

        $this->assertArrayHasKey('expires', $token);
        $this->assertArrayHasKey('access_token', $token);
        $this->assertArrayHasKey('token_type', $token);
        $this->assertSame('bearer', $token['token_type']);
        $this->assertNotEmpty($token['access_token']);
        $this->assertIsInt($token['expires']);

        var_dump($token);

        $client->quit();
    }
}