<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

interface IdentityProviderInterface {

    public function getCredentials(array $input): array;

    public function verify(array $credentials): bool;

    /**
     * @param mixed                 $credentials
     *
     * @param UserProviderInterface $userProvider
     * @return UserInterface|null
     */
    public function getUser(array $credentials, UserProviderInterface $userProvider): UserInterface;

    public function checkCredentials(array $credentials, UserInterface $user): bool;
}