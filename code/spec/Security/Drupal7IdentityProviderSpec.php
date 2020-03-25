<?php

namespace spec\App\Security;

use App\Entity\Drupal7User;
use App\Security\Drupal7Encoder;
use App\Security\Drupal7IdentityProvider;
use App\Security\IdentityProviderInterface;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class Drupal7IdentityProviderSpec extends ObjectBehavior {

    function let(UserProviderInterface $userProvider) {

        $encoder        = new Drupal7Encoder();
        $encoderFactory = new EncoderFactory([Drupal7User::class => $encoder]);

        $user1           = new Drupal7User();
        $user1->name     = 'grand';
        $user1->pass     = '$S$5Bfwxf1yBeU1AV2hw.CEiSkvS8B8qOJaPSuYGvpkD.I1dsoQ2FBW';

        $user2           = new Drupal7User();
        $user2->name     = 'vantt';
        $user2->pass     = 'adsfldkjshflasd';

        $userProvider->loadUserByUsername($user1->getUsername())->willReturn($user1);
        $userProvider->loadUserByUsername($user2->getUsername())->willReturn($user2);
        $userProvider->loadUserByUsername('notExist')->willReturn(null);

        $this->beConstructedWith($userProvider, $encoderFactory);
    }

    function it_is_initializable() {
        $this->shouldHaveType(Drupal7IdentityProvider::class);
        $this->shouldBeAnInstanceOf(IdentityProviderInterface::class);
    }

    function it_could_verify_valid_user() {
        $input = ['username' => 'grand', 'password' => 'Pr!m#rW0rd'];
        $this->verify($input)->shouldBe(true);
    }

    function it_could_verify_invalid_password() {
        $input = ['username' => 'grand', 'password' => 'Wrong Password'];
        $this->verify($input)->shouldBe(false);
    }

    function it_could_verify_invalid_user() {
        $input = ['username' => 'vantt', 'password' => 'Pr!m#rW0rd'];
        $this->verify($input)->shouldBe(false);
    }

    function it_throw_exception_when_username_empty() {
        $input       = ['username' => null, 'password' => 'Pr!m#rW0rd'];
        $this->shouldThrow(new CustomUserMessageAuthenticationException("Name could not be empty."))->duringVerify($input);
    }

    function it_throw_exception_when_missing_username() {
        $input = ['user' => 'vantt', 'password' => 'adsfasdf'];
        $this->shouldThrow(new CustomUserMessageAuthenticationException("Name could not be empty."))->duringVerify($input);
    }

    function it_throw_exception_when_user_not_found() {
        $input       = ['username' => 'notExist'];
        $this->shouldThrow(new CustomUserMessageAuthenticationException(sprintf('User %s could not be found.', 'notExist')))->duringVerify($input);
    }

    function it_throw_exception_when_user_pass_notmatch() {
        $input       = ['username' => 'grand', 'password' => 'Pr!m#rW0rd'];
        $this->shouldThrow(CustomUserMessageAuthenticationException::class)->duringVerify([[$input]]);
    }
}
