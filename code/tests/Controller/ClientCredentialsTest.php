<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ClientCredentialsTest extends WebTestCase {

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
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/test-connect/client-credentials');

        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);

        $this->assertEquals('', $data['scope']);
        $this->assertEquals('bearer', $data['token_type']);
        $this->assertNotEmpty($data['access_token']);
        $this->assertIsInt($data['expires']);
    }
}