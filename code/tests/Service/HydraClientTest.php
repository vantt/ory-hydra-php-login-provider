<?php

namespace App\Tests\Service;

use App\Service\HydraHttpClient;
use Http\Discovery\Strategy\MockClientStrategy;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;


class HydraClientTest extends TestCase {

    public function testFetchLogin() {
        $client = new MockHttpClient();
        $client>
        $hydra  = new HydraHttpClient($client);
    }

    public function testAcceptLogin() {

    }

    public function testRejectLogin() {

    }
}
