<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginControllerTest extends WebTestCase {

    /**
     *  @see https://symfony.com/doc/current/form/unit_testing.html
     */
    public function testShowLogin() {

        $client = static::createClient();
        $challenge = 'adsfafasdfadsf';

        $client->request('GET', '/login?login_challenge=' . $challenge);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorExists('input[name*=username]', 'username element does not exist');
        $this->assertSelectorExists('input[name*=password]', 'password element does not exist');
        $this->assertSelectorExists('input[name*=challenge]', 'challenge element does not exist');
        $this->assertInputValueSame('form[challenge]', $challenge);
    }
}
