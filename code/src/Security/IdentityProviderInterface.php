<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;

interface IdentityProviderInterface {

    public function verify(array $credentials): bool;

    /**
     * @param mixed $credentials
     *
     * @return UserInterface|null
     */
    public function getUser(array $credentials): UserInterface;

    public function checkCredentials(array $credentials, UserInterface $user): bool;
}