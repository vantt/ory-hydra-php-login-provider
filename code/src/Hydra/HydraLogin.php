<?php

namespace App\Hydra;

use App\Hydra\DTO\CompletedRequest;
use App\Hydra\DTO\LoginRequest;

class HydraLogin {

    /**
     * @var HydraClientInterface
     */
    private $hydra;


    private $login_challenge = null;

    /**
     * @var ?LoginRequest
     */
    private $login_request;

    /**
     * @var string
     */
    private $challenge;

    /**
     * HydraLogin constructor.
     *
     * @param LoginRequest         $loginRequest
     * @param HydraClientInterface $hydraClient
     */
    public function __construct(LoginRequest $loginRequest, HydraClientInterface $hydraClient) {
        $this->login_request = $loginRequest;
        $this->hydra         = $hydraClient;
    }

    /**
     * https://www.ory.sh/docs/hydra/sdk/api#accept-a-login-request
     *
     * @param string    $subject
     * @param bool|null $remember
     * @param int|null  $remember_for
     * @param array     $extra_options
     *
     * @return CompletedRequest
     * @throws HydraException
     */
    public function acceptLoginRequest(?string $subject = null, ?bool $remember = null, ?int $remember_for = null, array $extra_options=[]): CompletedRequest {
        $options = [];
        $options['subject'] = $subject ?? $this->login_request->getSubject();

        if (null !== $remember) {
            $options['remember'] = $remember;
        }

        if (null !== $remember_for) {
            $options['remember_for'] = $remember_for;
        }

        return $this->hydra->acceptLoginRequest($this->login_request->getChallenge(), $options);
    }

    /**
     * @param array $options
     *
     * @return CompletedRequest
     * @throws HydraException
     */
    public function rejectLoginRequest(array $options): CompletedRequest {
        return $this->hydra->rejectLoginRequest($this->login_request->getChallenge(), $options);
    }

    public function isSkip(): bool {
        return ($this->login_request->getSkip() === true);
    }

    public function getLoginRequest(): LoginRequest {
        return $this->login_request;
    }

    public function getChallenge(): string {
        return $this->login_request->getChallenge();
    }

    public function getSubject(): string {
        return $this->login_request->getSubject();
    }
}