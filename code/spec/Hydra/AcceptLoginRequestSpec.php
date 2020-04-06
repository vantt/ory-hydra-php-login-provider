<?php

namespace spec\App\Hydra;

use App\Hydra\DTO\AcceptLoginRequest;
use PhpSpec\ObjectBehavior;

class AcceptLoginRequestSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(AcceptLoginRequest::class);
    }
}
