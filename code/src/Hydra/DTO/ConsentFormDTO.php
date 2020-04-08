<?php


namespace App\Hydra\DTO;


class ConsentFormDTO {

    /**
     * @var string
     */
    private $challenge;

    /**
     * @var array
     */
    private $grant_scope = [];

    /**
     * @var bool
     */
    private $remember = false;

    /**
     * @var string
     */
    private $approval = '';


    /**
     * @return array
     */
    public function getGrantScope(): array {
        return $this->grant_scope;
    }

    /**
     * @param array $grant_scope
     */
    public function setGrantScope(array $grant_scope): void {
        $this->grant_scope = $grant_scope;
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
    public function getApproval(): string {
        return $this->approval;
    }

    /**
     * @param string $approval
     */
    public function setApproval(string $approval): void {
        $this->approval = $approval;
    }

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

    public function isApproved() {
        return $this->approval === 'allow';
    }
}