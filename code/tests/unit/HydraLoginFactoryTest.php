<?php

namespace App\Tests\Unit;

use App\Hydra\HydraClientInterface;
use App\Hydra\HydraLoginFactory;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class HydraLoginFactoryTest extends TestCase {

//    function test_fetchLoginRequest_should_return_FALSE_when_LOGINREQUEST_is_Empty(HydraClientInterface $hydraClient, SessionInterface $session) {
//        $currentChallenge = 'challenge_code1_is_the_same';
//        $oldChallenge     = 'challenge_code1_is_the_same';
//        $loginRequest     = [];
//
//        // setup a valid session
//        $session = new Session(new MockArraySessionStorage());
//        $session->set('hydra_login_request', $loginRequest);
//
//        $this->beConstructedWith($hydraClient, $session);
//        $this->fetchLoginRequest($currentChallenge)
//             ->shouldReturn(null);
//    }
//
//    function test_fetchLoginRequest_should_return_NULL_when_challenge_codes_are_different(HydraClientInterface $hydraClient, SessionInterface $session) {
//        $currentChallenge = 'challenge_code1_fafljashdf';
//        $oldChallenge     = 'challenge_code2_asdfafasdfds';
//        $loginRequest     = ['challenge' => $oldChallenge, 'dummmy' => 'dummy'];
//
//        // setup an invalid session
//        $session = new Session(new MockArraySessionStorage());
//        $session->set('hydra_login_request', $loginRequest);
//
//        $this->beConstructedWith($hydraClient, $session);
//        $this->fetchLoginRequest($currentChallenge)
//             ->shouldReturn(null);
//    }
//
//    function test_fetchLoginRequest_should_return_FALSE_when_OLD_CHALLENGE_CODE_is_EMPTY(HydraClientInterface $hydraClient, SessionInterface $session) {
//        $currentChallenge = 'challenge_code1_fafljashdf';
//        $oldChallenge     = '';
//        $loginRequest     = ['challenge' => $oldChallenge, 'dummmy' => 'dummy'];
//
//        // setup a valid session
//        $session = new Session(new MockArraySessionStorage());
//        $session->set('hydra_login_request', $loginRequest);
//
//        $this->beConstructedWith($hydraClient, $session);
//        $this->fetchLoginRequest($currentChallenge)
//             ->shouldReturn(null);
//    }
//
//    ///
//    ///
//    function test_isValidLoginRequest_should_return_TRUE_when_CHALLENGE_CODES_are_same_and_loginRequest_not_Empty() {
//        $currentChallenge = 'challenge_code1_is_the_same';
//        $oldChallenge     = 'challenge_code1_is_the_same';
//        $loginRequest     = ['challenge' => $oldChallenge, 'dummmy' => 'dummy'];
//
//        $this->isValidLoginRequest($currentChallenge, $oldChallenge, $loginRequest)
//             ->shouldReturn(true);
//    }
//
//    function test_isValidLoginRequest_should_return_FALSE_when_challenge_codes_are_different() {
//        $currentChallenge = 'challenge_code1_fafljashdf';
//        $oldChallenge     = 'challenge_code2_asdfafasdfds';
//        $loginRequest     = ['challenge' => $oldChallenge, 'dummmy' => 'dummy'];
//
//        $this->isValidLoginRequest($currentChallenge, $oldChallenge, $loginRequest)
//             ->shouldReturn(false);
//    }
//
//    function test_isValidLoginRequest_should_return_FALSE_when_LOGINREQUEST_is_Empty() {
//        $currentChallenge = 'challenge_code1_is_the_same';
//        $oldChallenge     = 'challenge_code1_is_the_same';
//        $loginRequest     = [];
//
//        $this->isValidLoginRequest($currentChallenge, $oldChallenge, $loginRequest)
//             ->shouldReturn(false);
//    }
//
//    function test_isValidLoginRequest_should_return_FALSE_when_OLD_CHALLENGE_CODE_is_EMPTY() {
//        $currentChallenge = 'challenge_code1_fafljashdf';
//        $oldChallenge     = '';
//        $loginRequest     = ['challenge' => $oldChallenge, 'dummmy' => 'dummy'];
//
//        $this->isValidLoginRequest($currentChallenge, $oldChallenge, $loginRequest)
//             ->shouldReturn(false);
//    }
}



