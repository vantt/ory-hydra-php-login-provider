<?php

namespace spec\App\Controller;

use App\Controller\LoginController;
use App\Service\HydraClient;
use PhpSpec\ObjectBehavior;

class LoginControllerSpec extends ObjectBehavior
{
    function let(HydraClient $hydraClient) {
        $this->beConstructedWith($hydraClient);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(LoginController::class);
    }


}
