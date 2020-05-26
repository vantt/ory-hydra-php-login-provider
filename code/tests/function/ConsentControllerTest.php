<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ConsentControllerTest extends WebTestCase {

    /**
     *  @see https://symfony.com/doc/current/form/unit_testing.html
     */
    public function testShowConsent() {

        $client = static::createClient();
        $challenge = 'adsfafasdfadsf';

        $client->request('GET', '/consent?consent_challenge=' . $challenge);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
