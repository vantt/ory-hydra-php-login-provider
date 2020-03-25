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
     * @param array $input
     *
     * @return bool
     */
    final public function verify(array $input): bool {
        $credentials = $this->getCredentials($input);

        if ($user = $this->getUser($credentials, $this->userProvider)) {
            return $this->checkCredentials($credentials, $user);
        }

        return false;
    }

    final public function getCredentials(array $input): array {
        return [
          'username' => $input['username'] ?? null,
          'password' => $input['password'] ?? null,
        ];
    }

    /**
     * @param mixed                 $credentials
     *
     * @param UserProviderInterface $userProvider
     *
     * @return UserInterface|null
     */
    final public function getUser(array $credentials, ?UserProviderInterface $userProvider): UserInterface {
        if (empty($credentials['username'])) {
            throw new CustomUserMessageAuthenticationException('Name could not be empty.');
        }

        if (null === $userProvider) {
            $user = $this->userProvider->loadUserByUsername($credentials['username']);
        }
        else {
            $user = $userProvider->loadUserByUsername($credentials['username']);
        }

        if (!$user) {
            throw new CustomUserMessageAuthenticationException(sprintf('User %s could not be found.', $credentials['username']));
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

