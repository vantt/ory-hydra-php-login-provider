<?php

namespace App\Tests\Security;

use App\Security\Drupal7PasswordEncoder;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class Drupal7PasswordEncoderTest extends TestCase {
    public function testEncodePassword() {
        $encoder = new Drupal7PasswordEncoder();

        $rawPassword     = 'any random password';
        $encodedPassword = $encoder->encodePassword($rawPassword, null);
        $this->assertTrue($encoder->isPasswordValid($encodedPassword, $rawPassword, null));
    }

    public function testIsPasswordValid() {
        $encoder = new Drupal7PasswordEncoder();

        $rawPassword     = 'any random password';
        $encodedPassword = '$S$D6iDOTf0K5p.m165BLd4RNftqXmOlf3tpNED1SrnZWlLOzCOW2lQ';
        $this->assertTrue($encoder->isPasswordValid($encodedPassword, $rawPassword, null));

        $rawPassword = 'any random password';
        $this->assertTrue($encoder->isPasswordValid($encoder->encodePassword($rawPassword, null), $rawPassword, null));
    }


}
