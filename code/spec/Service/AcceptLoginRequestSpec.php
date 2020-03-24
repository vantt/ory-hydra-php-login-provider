<?php

namespace spec\App\Service;

use App\Service\AcceptLoginRequest;
use PhpSpec\ObjectBehavior;

class AcceptLoginRequestSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(AcceptLoginRequest::class);
    }
}
