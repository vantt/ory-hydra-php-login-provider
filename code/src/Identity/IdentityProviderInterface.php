<?php

namespace App\Identity;

use InvalidArgumentException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

interface IdentityProviderInterface {

    /**
     * @param array $input
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    public function getCredentials(array $input): array;

    /**
     * @param array $input
     *
     * @return bool
     *
     * @throws InvalidArgumentException
     * @throws UserNotFoundException
     */
    public function verify(array $input): bool;

    /**
     * @param mixed                 $credentials
     * @param UserProviderInterface $userProvider
     *
     * @return UserInterface
     * @throws UserNotFoundException
     */
    public function getUser(array $credentials, UserProviderInterface $userProvider): UserInterface;

    public function checkCredentials(array $credentials, UserInterface $user): bool;
}