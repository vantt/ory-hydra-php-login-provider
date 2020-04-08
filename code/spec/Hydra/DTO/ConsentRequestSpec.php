<?php

namespace spec\App\Hydra\DTO;

use App\Hydra\DTO\ConsentRequest;
use PhpSpec\ObjectBehavior;

class ConsentRequestSpec extends ObjectBehavior {

    function let() {
        $request = [
          "challenge" => 'kjhfkajsdaskjdhflasdf',

          "skip"        => true,

          // The user-id of the already authenticated user - only set if skip is true
          "subject"     => 'vantt',

          // The initial OAuth 2.0 request url
          "request_url" => 'http://adfadsf.com/adfaf',

          "login_challenge" => 'adksjfjkshgfkajshdfgsaf',

          "login_session_id" => 'asldjfhlasjkfhalskfdjhalf',

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

        $this->beConstructedThrough('fromArray', [$request]);
    }

    function it_should_be_initialized() {
        $this->shouldHaveType(ConsentRequest::class);
    }

    function it_should_return_correct_values() {
        $request = [
          "challenge" => 'kjhfkajsdaskjdhflasdf',

          "skip"        => true,

          // The user-id of the already authenticated user - only set if skip is true
          "subject"     => 'vantt',

          "login_challenge" => 'adksjfjkshgfkajshdfgsaf',

          "login_session_id" => 'asldjfhlasjkfhalskfdjhalf',

          // The initial OAuth 2.0 request url
          "request_url" => 'http://adfadsf.com/adfaf',

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

        $this->beConstructedThrough('fromArray', [$request]);

        $this->getChallenge()->shouldReturn($request['challenge']);
        $this->getSkip()->shouldReturn($request['skip']);
        $this->getSubject()->shouldReturn($request['subject']);
        $this->getRequestUrl()->shouldReturn($request['request_url']);
        $this->getRequestedScope()->shouldReturn($request['requested_scope']);
        $this->getRequestedAccessTokenAudience()->shouldReturn($request['requested_access_token_audience']);
        $this->getOidcContext()->shouldReturn($request['oidc_context']);
        $this->getContext()->shouldReturn($request['context']);
    }


    function it_should_throw_InvalidArgumentException_when_missing_some_item() {
        $request = [
          "challenge" => 'kjhfkajsdaskjdhflasdf',

          "skip"        => true,

          // The user-id of the already authenticated user - only set if skip is true
          "subject"     => 'vantt',

          // The OAuth 2.0 client that initiated the request
          "client"          => ['client_id' => 'anphabe_app'],

          "login_challenge" => 'adksjfjkshgfkajshdfgsaf',

          "login_session_id" => 'asldjfhlasjkfhalskfdjhalf',

          // The initial OAuth 2.0 request url
          "request_url" => 'http://adfadsf.com/adfaf',


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

        $invalidLoginRequest = $request; unset($invalidLoginRequest['challenge']);
        $this->shouldThrow(\InvalidArgumentException::class)->duringFromArray($invalidLoginRequest);

        $invalidLoginRequest = $request; unset($invalidLoginRequest['skip']);
        $this->shouldThrow(\InvalidArgumentException::class)->duringFromArray($invalidLoginRequest);

        $invalidLoginRequest = $request; unset($invalidLoginRequest['subject']);
        $this->shouldThrow(\InvalidArgumentException::class)->duringFromArray($invalidLoginRequest);

        $invalidLoginRequest = $request; unset($invalidLoginRequest['client']);
        $this->shouldThrow(\InvalidArgumentException::class)->duringFromArray($invalidLoginRequest);

        $invalidLoginRequest = $request; unset($invalidLoginRequest['requested_scope']);
        $this->shouldThrow(\InvalidArgumentException::class)->duringFromArray($invalidLoginRequest);

        $invalidLoginRequest = $request; unset($invalidLoginRequest['login_challenge']);
        $this->shouldThrow(\InvalidArgumentException::class)->duringFromArray($invalidLoginRequest);

        $invalidLoginRequest = $request; unset($invalidLoginRequest['login_session_id']);
        $this->shouldThrow(\InvalidArgumentException::class)->duringFromArray($invalidLoginRequest);

        $invalidLoginRequest = $request; unset($invalidLoginRequest['requested_access_token_audience']);
        $this->shouldThrow(\InvalidArgumentException::class)->duringFromArray($invalidLoginRequest);
    }
}
