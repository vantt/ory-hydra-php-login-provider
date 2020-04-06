<?php

namespace App\Identity;

use InvalidArgumentException;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
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
     * @throws UserNotFoundException
     */
    final public function verify(array $input): bool {
        $credentials = $this->getCredentials($input);

        if ($user = $this->getUser($credentials, $this->userProvider)) {
            return $this->checkCredentials($credentials, $user);
        }

        return false;
    }

    /**
     * @param array $input
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    final public function getCredentials(array $input): array {
        if (empty($input['username'])) {
            throw new InvalidArgumentException('Name could not be empty.');
        }

        if (empty($input['password'])) {
            throw new InvalidArgumentException('Password could not be empty.');
        }

        return [
          'username' => $input['username'],
          'password' => $input['password'],
        ];
    }

    /**
     * @param mixed                 $credentials
     * @param UserProviderInterface $userProvider
     *
     * @return UserInterface
     * @throws UserNotFoundException
     */
    final public function getUser(array $credentials, ?UserProviderInterface $userProvider): UserInterface {
        if (null === $userProvider) {
            $user = $this->userProvider->loadUserByUsername($credentials['username']);
        }
        else {
            try {
                $user = $userProvider->loadUserByUsername($credentials['username']);
            } catch (UsernameNotFoundException $e) {
                throw new UserNotFoundException(sprintf('User %s not found.', $credentials['username']));
            }
        }

        if (!$user) {
            throw new UserNotFoundException(sprintf('User %s not found.', $credentials['username']));
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

