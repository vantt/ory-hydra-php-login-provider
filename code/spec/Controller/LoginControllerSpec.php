<?php

namespace spec\App\Controller;

use App\Controller\LoginController;
use App\Service\HydraClient;
use PhpSpec\ObjectBehavior;

class LoginControllerSpec extends ObjectBehavior
{
    function it_is_initializable(HydraClient $hydraClient)
    {
        $this->beConstructedWith($hydraClient);
        $this->shouldHaveType(LoginController::class);
    }

//    function it_should_response(HydraClient $hydraClient)
//    {
//        $this->beConstructedWith($hydraClient);
//        $response = $this->login();
//    }
}
