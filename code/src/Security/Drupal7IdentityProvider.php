<?php

namespace App\Security;

use App\Entity\Drupal7User;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class Drupal7IdentityProvider implements IdentityProviderInterface {

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

    /**
     * @var UserProviderInterface
     */
    private $userProvider;

    public function __construct(UserProviderInterface $userProvider, EncoderFactoryInterface $encoderFactory) {
        $this->encoderFactory = $encoderFactory;
        $this->userProvider   = $userProvider;
    }

    /**
     * @param array $credentials
     *
     * @return bool
     */
    final public function verify(array $credentials): bool {
        if ($user = $this->getUser($credentials)) {
            return $this->checkCredentials($credentials, $user);
        }

        return false;
    }

    /**
     * @param mixed $credentials
     *
     * @return UserInterface|null
     */
    final public function getUser(array $credentials): UserInterface {

        $user = $this->userProvider->loadUserByUsername($credentials['username']);

        if (!$user) {
            // fail authentication with a custom error
            throw new CustomUserMessageAuthenticationException('Name could not be found.');
        }

        return $user;
    }

    /**
     * @param array         $credentials
     * @param UserInterface $user
     *
     * @return bool
     */
    public function checkCredentials(array $credentials, UserInterface $user): bool {
        $encoder = $this->encoderFactory->getEncoder($user);
        $isValid = ($user->getUsername() === $credentials['username']) && $encoder->isPasswordValid($user->getPassword(), $credentials['password'], null);

        return $isValid;
    }

}

