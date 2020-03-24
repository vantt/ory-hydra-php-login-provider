<?php

namespace spec\App\Service;

use App\Service\LoginRequest;
use PhpSpec\ObjectBehavior;

class LoginRequestSpec extends ObjectBehavior {

    function it_should_be_initialized() {
        $this->shouldHaveType(LoginRequest::class);

    }

    function it_should_return_correct_values()
    {
        $loginRequest = [
          "skip"            => true,

          // The user-id of the already authenticated user - only set if skip is true
          "subject"         => 'vantt',

          // The initial OAuth 2.0 request url
          "request_url"     => 'http://adfadsf.com/adfaf',

          "redirect_url"     => 'http://adfadsf.com/adfafadsfdasf',

          // The OAuth 2.0 client that initiated the request
          "client"          => ['client_id' => 'anphabe_app'],

          // The OAuth 2.0 Scope requested by the client,
          "requested_scope" => ['user.account', 'user.profile'],

          // Information on the OpenID Connect request - only required to process if your UI should support these values.
          "oidc_context"    => ['something'],

          // Context is an optional object which can hold arbitrary data. The data will be made available when fetching the
          // consent request under the "context" field. This is useful in scenarios where login and consent endpoints share
          // data.
          "context"         => ['some_otherthing'],
        ];

        $this->beConstructedThrough('fromArray', [$loginRequest]);
        $this->shouldBeAnInstanceOf(LoginRequest::class);

        $this->isSkipLogin()->shouldReturn($loginRequest['skip']);
        $this->getSubject()->shouldReturn($loginRequest['subject']);
        $this->getRequestUrl()->shouldReturn($loginRequest['request_url']);
        $this->getRequestedScopes()->shouldReturn($loginRequest['requested_scope']);
        $this->getOidcContext()->shouldReturn($loginRequest['oidc_context']);
        $this->getContext()->shouldReturn($loginRequest['context']);
        $this->getRedirectUrl()->shouldReturn($loginRequest['redirect_url']);
    }

    function it_could_not_initialized_without_skip()
    {
        $data = [];
        $this->beConstructedThrough('fromArray', [$data]);
    }

    function it_isSkipLogin_should_return_true()
    {
        $data = ['skip' => true];
        $this->beConstructedThrough('fromArray', [$data]);
        $this->isSkipLogin()->shouldReturn(true);
        $this->needLogin()->shouldReturn(false);
    }

    function it_isSkipLogin_should_return_false()
    {
        $data = ['skip' => false];
        $this->beConstructedThrough('fromArray', [$data]);
        $this->isSkipLogin()->shouldReturn(false);
        $this->needLogin()->shouldReturn(true);
    }
}
