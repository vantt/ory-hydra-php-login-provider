<?php

namespace spec\App\Hydra;

use App\Hydra\HydraHttpClient;
use App\Hydra\HydraClientInterface;
use PhpSpec\ObjectBehavior;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;


class HydraClientSpec extends ObjectBehavior {

    function let(ClientInterface $httpClient, RequestFactoryInterface $requestFactory) {
        //$httpClient->sendRequest(Argument::any())->shouldBeCalled();
        $this->beConstructedWith($httpClient, $requestFactory);

    }

    function it_is_initializable() {
        $this->shouldHaveType(HydraHttpClient::class);
        $this->shouldBeAnInstanceOf(HydraClientInterface::class);
    }

    function it_should_return_json() {
        $this->fetchLogin('dsaf');
    }
}