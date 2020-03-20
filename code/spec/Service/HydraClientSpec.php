<?php

namespace spec\App\Service;

use App\Service\HydraClient;
use PhpSpec\ObjectBehavior;
use Psr\Http\Client\ClientInterface;


class HydraClientSpec extends ObjectBehavior
{
    function it_is_initializable(ClientInterface $httpClient)
    {
        $this->beConstructedWith($httpClient);
        $this->shouldHaveType(HydraClient::class);
    }
}
