<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginTest extends WebTestCase
{
    /**
     */
    public function testPageIsSuccessful()
    {
        $client = self::createClient();
        $client->request('GET', '/hello');

        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}