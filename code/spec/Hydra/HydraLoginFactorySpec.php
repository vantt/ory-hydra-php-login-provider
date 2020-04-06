<?php

namespace spec\App\Hydra;

use App\Hydra\HydraClientInterface;
use App\Hydra\HydraLogin;
use App\Hydra\HydraLoginFactory;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use App\Hydra\DTO\LoginRequest as LoginRequestDTO;

class HydraLoginFactorySpec extends ObjectBehavior {
    function let(HydraClientInterface $hydraClient, SessionInterface $session) {
        $this->beConstructedWith($hydraClient, $session);
    }

    function it_is_initializable() {
        $this->shouldHaveType(HydraLoginFactory::class);
        $this->beAnInstanceOf(HydraLoginFactory::class);
    }

    function it_isValidLoginRequest_return_TRUE_when_challenges_are_same(HydraClientInterface $hydraClient) {
        $loginRequest     = $this->createLoginRequest1();
        $currentChallenge = $loginRequest->getChallenge();

        $this->isValidLoginRequest($currentChallenge, $loginRequest)->shouldReturn(true);
    }

    function it_isValidLoginRequest_return_FALSE_when_challenges_are_different(HydraClientInterface $hydraClient) {
        $loginRequest     = $this->createLoginRequest1();
        $currentChallenge = 'sdfasfasfadfasfasdfsa';

        $this->isValidLoginRequest($currentChallenge, $loginRequest)->shouldReturn(false);
    }

    function it_fetchLoginRequest_return_Object_when_SESSION_had_valid_LoginRequest(HydraClientInterface $hydraClient) {
        $sessionLoginRequest = $this->createLoginRequest1();
        $currentChallenge    = $sessionLoginRequest->getChallenge();

        // setup a valid session
        $session = new Session(new MockArraySessionStorage());
        $session->set('hydra_login_request', $sessionLoginRequest);

        $this->beConstructedWith($hydraClient, $session);
        $this->fetchLoginRequest($currentChallenge)->shouldBeAnInstanceOf(HydraLogin::class);
    }

    function it_fetchLoginRequest_return_Object_when_SESSION_had_invalid_LoginRequest_BUT_Hydra_return_valid_LoginRequest(HydraClientInterface $hydraClient) {
        $sessionLoginRequest = $this->createLoginRequest1();
        $hydraLoginRequest   = $this->createLoginRequest2();

        // setup a invalid session
        $session = new Session(new MockArraySessionStorage());
        $session->set('hydra_login_request', $sessionLoginRequest);

        // setup valid hydra return
        $hydraClient->fetchLoginRequest($hydraLoginRequest->getChallenge())->willReturn($hydraLoginRequest);

        $this->beConstructedWith($hydraClient, $session);
        $this->fetchLoginRequest($hydraLoginRequest->getChallenge())->shouldBeAnInstanceOf(HydraLogin::class);
    }

    function it_fetchLoginRequest_return_Object_when_SESSION_had_no_LoginRequest_BUT_Hydra_return_valid_LoginRequest(HydraClientInterface $hydraClient) {
        $hydraLoginRequest = $this->createLoginRequest2();
        $currentChallenge  = $hydraLoginRequest->getChallenge();

        // setup a no session
        $session = new Session(new MockArraySessionStorage());

        // setup valid hydra return
        $hydraClient->fetchLoginRequest($currentChallenge)->willReturn($hydraLoginRequest);

        $this->beConstructedWith($hydraClient, $session);
        $this->fetchLoginRequest($currentChallenge)->shouldBeAnInstanceOf(HydraLogin::class);
    }

    function it_fetchLoginRequest_return_NULL_when_SESSION_had_invalid_LoginRequest_AND_Hydra_return_invalid_LoginRequest(HydraClientInterface $hydraClient) {

        $sessionLoginRequest = $this->createLoginRequest1();
        $hydraLoginRequest   = $this->createLoginRequest2();
        $currentChallenge    = 'sdfkhgskdfjghshdlfglskdfjghsldf';

        // setup a invalid session
        $session = new Session(new MockArraySessionStorage());
        $session->set('hydra_login_request', $sessionLoginRequest);

        // setup valid hydra return
        $hydraClient->fetchLoginRequest($currentChallenge)->willReturn($hydraLoginRequest);

        $this->beConstructedWith($hydraClient, $session);
        $this->fetchLoginRequest($currentChallenge)->shouldReturn(null);
    }

    function createLoginRequest1() {
        $loginRequest = [
          "challenge" => 'the_random_challenge_1',

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

        return LoginRequestDTO::fromArray($loginRequest);
    }

    function createLoginRequest2() {
        $loginRequest = [
          "challenge" => 'the_random_challenge_2',

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

        return LoginRequestDTO::fromArray($loginRequest);
    }
}
