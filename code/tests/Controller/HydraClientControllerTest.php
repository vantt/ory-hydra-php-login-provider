<?php

namespace App\Tests\Controller;

use Symfony\Component\Panther\PantherTestCase;
use Symfony\Component\Panther\Client;

class HydraClientControllerTest extends PantherTestCase {

    /**
     */
    public function test_AuthorizationCodeGrant_RedirectCorrectly() {
        $client = static::createPantherClient();
        $client->followRedirects(true);
        $client->followMetaRefresh(true);
        $crawler = $client->request('GET', '/test-connect/authorization-code');
var_dump($crawler->getUri());
        $uri = parse_url($crawler->getUri());
        parse_str($uri['query'], $query);

        $this->assertArrayHasKey('client_id', $query);
        $this->assertArrayHasKey('redirect_uri', $query);
        $this->assertArrayHasKey('state', $query);
        $this->assertArrayHasKey('response_type', $query);
        $this->assertArrayHasKey('approval_prompt', $query);
        $this->assertArrayHasKey('scope', $query);
    }

    /**
     */
    public function test_AuthorizationCodeGrant() {
        $client = static::createClient();
        $client->followRedirects(true);
        $client->followMetaRefresh(true);
        $crawler = $client->request('GET', '/test-connect/authorization-code');
        $crawler = $client->request('GET', $crawler->getUri());

        var_dump($crawler->getUri());
        $uri = parse_url($crawler->getUri());
        parse_str($uri['query'], $query);

        // "https://sso.dev.mio/oauth2/auth?scope=photos.read%20account.profile%20openid%20offline%20offline_access&state=b70ad3698460feec2d7ae70008555b5e&response_type=code&approval_prompt=auto&redirect_uri=https%3A%2F%2Flocalhost%2Fconnect%2Fhydra%2Fcheck&client_id=theleague"
        //var_dump($crawler->getUri());

        //$crawler = $client->followRedirect();

        //
        //var_dump($crawler->getUri());


        //        $crawler = $client->submitForm('Sign ', [
        //          'form[username]' => '',
        //          'form[password]' => '',
        //        ]);
        //
        //        $response = $client->getResponse();
        //
        //        var_dump($crawler->getUri());
        //
        //        $this->assertEquals(200, $response->getStatusCode());
        //        $data = json_decode($response->getContent(), true);
        //
        //        $this->assertEquals('', $data['scope']);
        //        $this->assertEquals('bearer', $data['token_type']);
        //        $this->assertNotEmpty($data['access_token']);
        //        $this->assertIsInt($data['expires']);
    }
}