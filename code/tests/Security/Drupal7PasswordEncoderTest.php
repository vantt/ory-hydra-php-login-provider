<?php

namespace App\Tests\Security;

use App\Identity\Drupal7PasswordEncoder;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class Drupal7PasswordEncoderTest extends TestCase {
    public function testEncodePassword() {
        $encoder = new Drupal7PasswordEncoder();

        $rawPassword     = 'any random password';
        $encodedPassword = $encoder->encodePassword($rawPassword, null);
        $this->assertTrue($encoder->isPasswordValid($encodedPassword, $rawPassword, null), 'Encoded password must be valid.');
    }

    public function testEncode_SameRawPassword_Will_Return_DifferentHashes() {
        $encoder = new Drupal7PasswordEncoder();

        $rawPassword     = 'any random password';
        $encodedPassword1 = $encoder->encodePassword($rawPassword, null);
        $encodedPassword2 = $encoder->encodePassword($rawPassword, null);

        $this->assertNotEquals($encodedPassword1, $encodedPassword2, 'Two hashes of the same-password could not be the same.');
        $this->assertTrue($encoder->isPasswordValid($encodedPassword1, $rawPassword, null));
        $this->assertTrue($encoder->isPasswordValid($encodedPassword2, $rawPassword, null));

    }

    public function testIsPasswordValid() {
        $encoder = new Drupal7PasswordEncoder();

        $rawPassword     = '123456';
        $encodedPassword = '$S$D/U1UB14rKNAqKMlw3wM7BgyAZuIUjp/pwWw1AHxNVKS3J1QK1h6';
        $this->assertTrue($encoder->isPasswordValid($encodedPassword, $rawPassword, null), 'Could verify encoded password');

        $rawPassword     = 'any random password';
        $encodedPassword = '$S$D6iDOTf0K5p.m165BLd4RNftqXmOlf3tpNED1SrnZWlLOzCOW2lQ';
        $this->assertTrue($encoder->isPasswordValid($encodedPassword, $rawPassword, null), 'Could verify encoded password');

        $rawPassword = 'any random password';
        $encodedPassword = $encoder->encodePassword($rawPassword, null);
        $this->assertTrue($encoder->isPasswordValid($encodedPassword, $rawPassword, null), 'Could verify encoded password');
    }
}
