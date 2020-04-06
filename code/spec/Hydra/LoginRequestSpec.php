<?php

namespace spec\App\Hydra;

use App\Hydra\DTO\LoginRequest;
use PhpSpec\ObjectBehavior;

class LoginRequestSpec extends ObjectBehavior {

    function let() {
        $loginRequest = [
          "challenge" => 'kjhfkajsdaskjdhflasdf',

          "skip"        => true,

          // The user-id of the already authenticated user - only set if skip is true
          "subject"     => 'vantt',

          // The initial OAuth 2.0 request url
          "request_url" => 'http://adfadsf.com/adfaf',

          "redirect_url"    => 'http://adfadsf.com/adfafadsfdasf',

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
    }

    function it_should_be_initialized() {
        $this->shouldHaveType(LoginRequest::class);
    }

    function it_should_return_correct_values() {
        $loginRequest = [
          "challenge" => 'kjhfkajsdaskjdhflasdf',

          "skip"        => true,

          // The user-id of the already authenticated user - only set if skip is true
          "subject"     => 'vantt',

          // The initial OAuth 2.0 request url
          "request_url" => 'http://adfadsf.com/adfaf',

          "redirect_url"    => 'http://adfadsf.com/adfafadsfdasf',

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

        $this->getChallenge()->shouldReturn($loginRequest['challenge']);
        $this->getSkip()->shouldReturn($loginRequest['skip']);
        $this->getSubject()->shouldReturn($loginRequest['subject']);
        $this->getRequestUrl()->shouldReturn($loginRequest['request_url']);
        $this->getRequestedScopes()->shouldReturn($loginRequest['requested_scope']);
        $this->getOidcContext()->shouldReturn($loginRequest['oidc_context']);
        $this->getContext()->shouldReturn($loginRequest['context']);
        $this->getRedirectUrl()->shouldReturn($loginRequest['redirect_url']);
    }


    function it_should_throw_InvalidArgumentException_when_missing_some_item() {
        $loginRequest = [
          "challenge" => 'kjhfkajsdaskjdhflasdf',

          "skip"        => true,

          // The user-id of the already authenticated user - only set if skip is true
          "subject"     => 'vantt',

          // The OAuth 2.0 client that initiated the request
          "client"          => ['client_id' => 'anphabe_app'],

          // The initial OAuth 2.0 request url
          "request_url" => 'http://adfadsf.com/adfaf',

          "redirect_url"    => 'http://adfadsf.com/adfafadsfdasf',

          // The OAuth 2.0 Scope requested by the client,
          "requested_scope" => ['user.account', 'user.profile'],

          // Information on the OpenID Connect request - only required to process if your UI should support these values.
          "oidc_context"    => ['something'],

          // Context is an optional object which can hold arbitrary data. The data will be made available when fetching the
          // consent request under the "context" field. This is useful in scenarios where login and consent endpoints share
          // data.
          "context"         => ['some_otherthing'],
        ];

        $this->shouldThrow(\InvalidArgumentException::class)->duringFromArray([]);

        $invalidLoginRequest = $loginRequest; unset($invalidLoginRequest['challenge']);
        $this->shouldThrow(\InvalidArgumentException::class)->duringFromArray($invalidLoginRequest);

        $invalidLoginRequest = $loginRequest; unset($invalidLoginRequest['skip']);
        $this->shouldThrow(\InvalidArgumentException::class)->duringFromArray($invalidLoginRequest);

        $invalidLoginRequest = $loginRequest; unset($invalidLoginRequest['subject']);
        $this->shouldThrow(\InvalidArgumentException::class)->duringFromArray($invalidLoginRequest);

        $invalidLoginRequest = $loginRequest; unset($invalidLoginRequest['client']);
        $this->shouldThrow(\InvalidArgumentException::class)->duringFromArray($invalidLoginRequest);

        $invalidLoginRequest = $loginRequest; unset($invalidLoginRequest['requested_scope']);
        $this->shouldThrow(\InvalidArgumentException::class)->duringFromArray($invalidLoginRequest);
    }
}
