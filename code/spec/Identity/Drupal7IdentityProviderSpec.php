<?php

namespace spec\App\Identity;

use App\Entity\Drupal7User;
use App\Identity\Drupal7PasswordEncoder;
use App\Identity\Drupal7IdentityProvider;
use App\Identity\IdentityProviderInterface;
use App\Identity\UserNotFoundException;
use App\Identity\CredentialsNotMatchedException;
use InvalidArgumentException;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class Drupal7IdentityProviderSpec extends ObjectBehavior {

    protected $users = [];

    function let(UserProviderInterface $userProvider) {

        $encoder        = new Drupal7PasswordEncoder();
        $encoderFactory = new EncoderFactory([Drupal7User::class => $encoder]);

        // setup user 1
        $name = 'user1';
        $pass = 'fake password 1';
        $user  = new Drupal7User();
        $user->name     = $name;
        $user->pass     = $encoder->encodePassword($pass, null);
        $this->users[1] = [$name, $pass, $user];
        $userProvider->loadUserByUsername($name)->willReturn($user);

        // setup user 2
        $name = 'user2';
        $pass = 'fake password 2';
        $user  = new Drupal7User();
        $user->name     = $name;
        $user->pass     = $encoder->encodePassword($pass, null);
        $this->users[2] = [$name, $pass, $user];
        $userProvider->loadUserByUsername($name)->willReturn($user);

        // setup an invalid user
        $userProvider->loadUserByUsername('notExist')->willReturn(null);

        $this->beConstructedWith($userProvider, $encoderFactory);
    }

    function it_is_initializable() {
        $this->shouldHaveType(Drupal7IdentityProvider::class);
        $this->shouldBeAnInstanceOf(IdentityProviderInterface::class);
    }

    function it_could_verify_valid_user() {
        [$name, $pass, $user]  = $this->users[1];
        $input = ['username' => $name, 'password' => $pass];
        $this->verify($input)->shouldBe(true);

        [$name, $pass, $user]  = $this->users[2];
        $input = ['username' => $name, 'password' => $pass];
        $this->verify($input)->shouldBe(true);
    }

    function it_could_verify_invalid_password() {
        [$name, $pass, $user]  = $this->users[1];
        $input = ['username' => $name, 'password' => 'invalid password'];
        $this->verify($input)->shouldBe(false);
    }

    function it_throw_exception_when_user_not_found() {
        $input       = ['username' => 'notExist', 'password' => 'just dummy'];
        $this->shouldThrow(new UserNotFoundException(sprintf('User %s not found.', 'notExist')))->duringVerify($input);
    }

    function it_throw_exception_when_username_empty() {
        $input       = ['username' => null, 'password' => 'just dummy'];
        $this->shouldThrow(new InvalidArgumentException("Name could not be empty."))->duringVerify($input);

        $input       = ['username' => '', 'password' => 'just dummy'];
        $this->shouldThrow(new InvalidArgumentException("Name could not be empty."))->duringVerify($input);
    }

    function it_throw_exception_when_missing_username() {
        $input = ['user' => 'someuser', 'password' => 'just dummy'];
        $this->shouldThrow(new InvalidArgumentException("Name could not be empty."))->duringVerify($input);
    }


    function it_throw_exception_when_user_pass_notmatch() {
        $input       = ['username' => 'grand', 'password' => 'Pr!m#rW0rd'];
        $this->shouldThrow(CustomUserMessageAuthenticationException::class)->duringVerify([[$input]]);
    }
}
