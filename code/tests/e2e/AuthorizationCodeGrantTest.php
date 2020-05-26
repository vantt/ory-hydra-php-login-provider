<?php

namespace App\Tests\E2e;

use Symfony\Component\Panther\PantherTestCase;
use Symfony\Component\Panther\Client;

class AuthorizationCodeGrantTest extends PantherTestCase {
    const BASEURL = 'https://id.dev.mio';

    protected function createMyClient() {
        $options = [
          'external_base_uri'        => self::BASEURL,
          'connection_timeout_in_ms' => 5000,
          'request_timeout_in_ms'    => 120000,
        ];

        // $client = Client::createChromeClient(null, null, $options, self::BASEURL);
        $client = static::createPantherClient($options);
        $client->followRedirects(true);
        $client->followMetaRefresh(true);

        return $client;
    }

    /**
     */
    public function test_AuthorizationCodeGrant_RedirectTo_LoginForm() {
        $client = $this->createMyClient();
        $client->request('GET', '/test-connect/authorization-code');

        /************************
         * Login Form
         ************************/
        $client->waitFor('#login-form');

        // "https://id.dev.mio/login?login_challenge=fb1f6d390c674526961c58696f6fc870"
        $uri = $this->parseUrl($client->getCurrentURL());
        $this->assertEquals('/login', $uri['path']);
        $this->assertArrayHasKey('login_challenge', $uri['query_params']);

        $client->quit();
    }

    public function test_AuthorizationCodeGrant_RedirectTo_ConsentForm() {
        $client = $this->createMyClient();
        $client->request('GET', '/test-connect/authorization-code');

        /************************
         * Login Form
         ************************/
        $client->waitFor('#login-form');
        $this->submitLogin($client, 'demo', '123456');

        /************************
         * Consent Form
         ************************/
        $client->waitFor('#consent-form');

        // "https://id.dev.mio/consent?consent_challenge=fb1f6d390c674526961c58696f6fc870"
        $uri = $this->parseUrl($client->getCurrentURL());
        $this->assertEquals('/consent', $uri['path']);
        $this->assertArrayHasKey('consent_challenge', $uri['query_params']);

        $client->quit();
    }

    public function test_AuthorizationCodeGrant_RedirectTo_Callback() {
        $client = $this->createMyClient();
        $client->request('GET', '/test-connect/authorization-code');

        /************************
         * Login Form
         ************************/
        $client->waitFor('#login-form');
        $this->submitLogin($client, 'demo', '123456');

        /************************
         * Consent Form
         ************************/
        $client->waitFor('#consent-form');
        $this->submitConsent($client, 'Allow Access', ['photos.read', 'account.profile', 'openid', 'offline']);

        /************************
         * Access Token check
         ************************/
        $crawler = $client->waitFor('pre');

        // https://id.dev.mio/test-connect/hydra/check?code=dgHdjUlgZ4a7aZVGAbPyL5CR_x3fMiLMok6WFXAe17c.b-E7N9j4cOFWhbFlKZZjCozlTNe8HalsqCkLRCtIG88&scope=photos.read%20account.profile%20openid%20offline%20offline_access&state=ae78f111d9a5d13a75a72f464edc557f
        $uri = $this->parseUrl($client->getCurrentURL());
        $this->assertEquals('/test-connect/hydra/check', $uri['path']);
        $this->assertArrayHasKey('code', $uri['query_params']);
        $this->assertArrayHasKey('state', $uri['query_params']);
        $this->assertArrayHasKey('scope', $uri['query_params']);
        $this->assertSame('photos.read account.profile openid offline', $uri['query_params']['scope']);

        $client->quit();
    }

    public function test_AuthorizationCodeGrant_Got_AccessToken() {
        $client = $this->createMyClient();
        $client->request('GET', '/test-connect/authorization-code');

        /************************
         * Login Form
         ************************/
        $client->waitFor('#login-form');
        $this->submitLogin($client, 'demo', '123456');

        /************************
         * Consent Form
         ************************/
        $client->waitFor('#consent-form');
        $this->submitConsent($client, 'Allow Access', ['photos.read', 'account.profile', 'openid', 'offline']);

        /************************
         * Access Token check
         ************************/
        $crawler = $client->waitFor('pre');
        $data  = json_decode($crawler->getText(), true);
        $token = $data['token'];
        $this->assertArrayHasKey('expires', $token);
        $this->assertArrayHasKey('access_token', $token);
        $this->assertArrayHasKey('refresh_token', $token);
        $this->assertArrayHasKey('id_token', $token);

        $this->assertSame('bearer', $token['token_type']);
        $this->assertNotEmpty($token['access_token']);
        $this->assertIsInt($token['expires']);

        $client->quit();
    }

    protected function parseUrl(string $url): array {
        //var_dump($url);
        $uri = parse_url($url);
        parse_str($uri['query'], $uri['query_params']);

        return $uri;
    }

    protected function submitLogin(Client $client, $username, $password) {
        $crawler                = $client->getCrawler();
        $form                   = $crawler->selectButton('Sign in')
                                          ->form(); // submit the form which has the button [Sign in]
        $form['form[username]'] = $username;
        $form['form[password]'] = $password;
        $client->submit($form);
    }

    protected function submitConsent(Client $client, string $button, array $grant_scopes = []) {
        $crawler      = $client->getCrawler();
        $submitButton = $crawler->selectButton($button);
        $form         = $submitButton->form();

        $form['form[grant_scope]']->select($grant_scopes);
        $submitButton->click();
    }
}