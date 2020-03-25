<?php

namespace App\Tests\Security;

use App\Security\Drupal7Encoder;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class Drupal7EncoderTest extends TestCase {
    public function testIsPasswordValid() {
        $encoder         = new Drupal7Encoder();
        $rawPassword     = 'Pr!m#rW0rd';
        $encodedPassword = '$S$5Bfwxf1yBeU1AV2hw.CEiSkvS8B8qOJaPSuYGvpkD.I1dsoQ2FBW';

        $this->assertTrue($encoder->isPasswordValid($encodedPassword, $rawPassword, null));
    }

    public function testEncodePassword() {
        $encoder         = new Drupal7Encoder();
        $rawPassword     = 'password';
        $encodedPassword = $encoder->encodePassword($rawPassword, null);

        $this->assertTrue($encoder->isPasswordValid($encodedPassword, $rawPassword, null));

    }
}
