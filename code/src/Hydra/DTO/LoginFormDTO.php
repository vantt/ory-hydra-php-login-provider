<?php


namespace App\Hydra\DTO;


class LoginFormDTO {

    /**
     * @var string|null
     */
    private $challenge;

    /**
     * @var bool
     */
    private $remember = false;

    /**
     * @var string
     */
    private $username = '';

    /**
     * @var string
     */
    private $password = '';

    /**
     * @return string
     */
    public function getChallenge(): string {
        return $this->challenge;
    }

    /**
     * @param string $challenge
     */
    public function setChallenge(string $challenge): void {
        $this->challenge = $challenge;
    }

    /**
     * @return bool
     */
    public function isRemember(): bool {
        return $this->remember;
    }

    /**
     * @param bool $remember
     */
    public function setRemember(bool $remember): void {
        $this->remember = $remember;
    }

    /**
     * @return string
     */
    public function getUsername(): string {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getPassword(): string {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void {
        $this->password = $password;
    }
}