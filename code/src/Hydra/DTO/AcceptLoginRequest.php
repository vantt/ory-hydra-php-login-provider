<?php

namespace App\Hydra\DTO;

class AcceptLoginRequest {

    /**
     * @var string|null
     */
    private $acr;

    /**
     * @var array|null
     */
    private $context;

    /**
     * @var string|null
     */
    private $force_subject_identifier;

    /**
     * @var bool|null
     */
    private $remember;

    /**
     * @var int
     */
    private $remember_for = 0;

    /**
     * @var string
     */
    private $subject;

    /**
     * AcceptLoginRequest constructor.
     *
     * @param array $data
     */
    public function __construct(array $data) {
        $this->subject                  = $data['subject'] ?? null;
        $this->acr                      = $data['acr'] ?? null;
        $this->context                  = $data['context'] ?? null;
        $this->force_subject_identifier = $data['force_subject_identifier'] ?? null;
        $this->remember                 = $data['remember'] ?? null;
        $this->remember_for             = $data['remember_for'] ?? null;
    }

    public static function fromArray(array $data): self {
        return new self($data);
    }

    /**
     * @return mixed|null
     */
    public function getAcr(): ?mixed {
        return $this->acr;
    }

    /**
     * @return mixed|null
     */
    public function getContext(): ?mixed {
        return $this->context;
    }

    /**
     * @return mixed|null
     */
    public function getForceSubjectIdentifier(): ?mixed {
        return $this->force_subject_identifier;
    }

    /**
     * @return mixed|null
     */
    public function getRemember(): ?mixed {
        return $this->remember;
    }

    /**
     * @return mixed|null
     */
    public function getRememberFor(): bool {
        return $this->remember_for;
    }

    /**
     * @return mixed|null
     */
    public function getSubject(): string {
        return $this->subject;
    }

    /**
     * @param string|null $acr
     */
    public function setAcr(?string $acr): void {
        $this->acr = $acr;
    }

    /**
     * @param array|null $context
     */
    public function setContext(?array $context): void {
        $this->context = $context;
    }

    /**
     * @param string|null $force_subject_identifier
     */
    public function setForceSubjectIdentifier(?string $force_subject_identifier): void {
        $this->force_subject_identifier = $force_subject_identifier;
    }

    /**
     * @param bool|null $remember
     */
    public function setRemember(?bool $remember): void {
        $this->remember = $remember;
    }

    /**
     * @param int $remember_for
     */
    public function setRememberFor(int $remember_for): void {
        $this->remember_for = $remember_for;
    }

    /**
     * @param string $subject
     */
    public function setSubject(string $subject): void {
        $this->subject = $subject;
    }


}
