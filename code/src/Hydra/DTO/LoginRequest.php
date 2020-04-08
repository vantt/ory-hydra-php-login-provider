<?php

namespace App\Hydra\DTO;

class LoginRequest {

    /**
     * @var string
     */
    private $challenge;


    /**
     * @var bool
     *
     * Skip, if true, implies that the client has requested the same scopes from the same user previously.
     * If true, you can skip asking the user to grant the requested scopes, and simply forward the user to the redirect URL.
     *
     * This feature allows you to update / set session information.
     */
    private $skip;

    // The user-id of the already authenticated user - only set if skip is true
    /**
     * @var string
     */
    private $subject;

    // The initial OAuth 2.0 request url
    /**
     * @var string
     */
    private $request_url;

    // The OAuth 2.0 client that initiated the request
    /**
     * @var array
     */
    private $client;

    // The OAuth 2.0 Scope requested by the client,
    private $requested_scope = [];

    /**
     * Information on the OpenID Connect request - only required to process if your UI should support these values.
     *
     * @var array|mixed
     */
    private $oidc_context = [];

    /**
     * Context is an optional object which can hold arbitrary data. The data will be made available when fetching the
     * consent request under the "context" field. This is useful in scenarios where login and consent endpoints share
     * data.
     *
     * @var array
     */
    private $context;

    /**
     * LoginRequest constructor.
     *
     * @param array $data
     *
     * @see https://www.ory.sh/docs/hydra/sdk/api#schemaloginrequest
     */
    private function __construct(array $data) {
        $important_keys = [
          'challenge',
          'skip',
          'subject',
          'request_url',
          'client',
          'requested_scope',
          'oidc_context',
          'context',
        ];

        $missing_keys = array_diff($important_keys, array_keys($data));

        if (!empty($missing_keys)) {
            throw new \InvalidArgumentException(sprintf('Missing many important array items: %s', implode(', ', $missing_keys)));
        }

        $this->skip            = (bool)$data['skip'];
        $this->challenge       = (string)$data['challenge'];
        $this->subject         = (string)$data['subject'];
        $this->request_url     = (string)$data['request_url'];
        $this->requested_scope = (array)$data['requested_scope'];
        $this->client          = $data['client'];
        $this->oidc_context    = (array)$data['oidc_context'];
        $this->context         = (array)$data['context'];
    }

    public static function fromArray(array $data): self {
        return new LoginRequest($data);
    }

    public function getChallenge(): string {
        return $this->challenge;
    }

    /**
     * @return bool|mixed
     */
    final public function getSkip(): bool {
        return (bool)$this->skip;
    }

    /**
     * @return string
     */
    public function getSubject(): string {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getRequestUrl(): string {
        return $this->request_url;
    }

    /**
     * @return array
     */
    public function getClient(): array {
        return $this->client;
    }

    /**
     * @return array
     */
    public function getRequestedScope(): array {
        return $this->requested_scope;
    }

    /**
     * @return array|mixed
     */
    public function getOidcContext(): array {
        return $this->oidc_context;
    }

    /**
     * @return array
     */
    public function getContext(): array {
        return $this->context;
    }
}

