<?php

namespace App\Tests\Controller;

use Symfony\Component\Panther\PantherTestCase;
use Symfony\Component\Panther\Client;

class AuthorizationCodeGrantTest extends PantherTestCase {
    const BASEURL = 'https://id.dev.mio';

    /**
     */
    public function test_AuthorizationCodeGrant_RedirectCorrectly() {
        $options = [
          'external_base_uri'        => self::BASEURL,
          'connection_timeout_in_ms' => 5000,
          'request_timeout_in_ms'    => 120000,
        ];

        // $client = Client::createChromeClient(null, null, $options, self::BASEURL);
        $client = static::createPantherClient($options);
        $client->followRedirects(true);
        $client->followMetaRefresh(true);
        $client->request('GET', '/test-connect/authorization-code');

        // Login Form
        // "https://id.dev.mio/login?login_challenge=fb1f6d390c674526961c58696f6fc870"
        $crawler = $client->waitFor('#login-form');
        $url     = $client->getCurrentURL();
        $uri     = parse_url($url);
        parse_str($uri['query'], $query);
        //var_dump($url);

        $this->assertEquals('/login', $uri['path']);
        $this->assertArrayHasKey('login_challenge', $query);

        // submit the form which has the button [Sign in]
        $form                   = $crawler->selectButton('Sign in')->form();
        $form['form[username]'] = 'demo';
        $form['form[password]'] = '123456';
        $client->submit($form);

        // Consent Form
        // "https://id.dev.mio/consent?consent_challenge=fb1f6d390c674526961c58696f6fc870"
        $crawler = $client->waitFor('#consent-form');
        $url     = $client->getCurrentURL();
        $uri     = parse_url($url);
        $query   = null;
        parse_str($uri['query'], $query);
        //var_dump($url);

        $this->assertEquals('/consent', $uri['path']);
        $this->assertArrayHasKey('consent_challenge', $query);

        $submitButton = $crawler->selectButton('Allow Access');
        $form         = $submitButton->form();
        $form['form[grant_scope]']->select(['photos.read', 'account.profile']);
        $form['form[grant_scope]']->select(['openid']);
        $form['form[grant_scope]']->select(['offline']);
        $submitButton->click();

        $crawler = $client->waitFor('pre');
        $url     = $client->getCurrentURL();
        $uri     = parse_url($url);
        $query   = null;
        parse_str($uri['query'], $query);
        //var_dump($url);

        $this->assertEquals('/test-connect/hydra/check', $uri['path']);
        $this->assertArrayHasKey('code', $query);
        $this->assertArrayHasKey('state', $query);
        $this->assertArrayHasKey('scope', $query);
        $this->assertSame('photos.read account.profile openid offline', $query['scope']);

        $data  = json_decode($crawler->getText(), true);
        $token = $data['token'];
        $this->assertArrayHasKey('expires', $token);
        $this->assertArrayHasKey('access_token', $token);
        $this->assertArrayHasKey('refresh_token', $token);
        $this->assertArrayHasKey('id_token', $token);

        $client->quit();
    }



}