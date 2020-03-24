<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginControllerTest extends WebTestCase {

    public function testShowLogin() {

        $client = static::createClient();

        $login_challenge = 'adsfafasdfadsf';

        $client->request('GET', '/login?login_challenge=' . $login_challenge);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorExists('#login_form_username', 'username element does not exist');
        $this->assertSelectorExists('#login_form_password', 'password element does not exist');
        $this->assertSelectorExists('input[name="*challenge"]', 'challenge element does not exist');
        $this->assertInputValueSame('login_form[challenge]', $login_challenge);
    }
}
