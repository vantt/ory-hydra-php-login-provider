<?php

namespace App\Service;

class LoginRequest {

    // Skip, if true, implies that the client has requested the same scopes from the same user previously.
    // If true, you can skip asking the user to grant the requested scopes, and simply forward the user to the redirect URL.
    //
    // This feature allows you to update / set session information.
    private $skip;

    // The user-id of the already authenticated user - only set if skip is true
    private $subject;

    // The initial OAuth 2.0 request url
    private $request_url;

    private $redirect_url;

    // The OAuth 2.0 client that initiated the request
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

    private function __construct(array $data) {
        $this->skip            = $data['skip'] ?? false;
        $this->subject         = $data['subject'] ?? null;
        $this->request_url     = $data['request_url'] ?? null;
        $this->redirect_url    = $data['redirect_url'] ?? null;
        $this->client          = $data['client'] ?? [];
        $this->requested_scope = $data['requested_scope'] ?? null;
        $this->oidc_context    = $data['oidc_context'] ?? [];
        $this->context         = $data['context'] ?? [];
    }

    public static function fromArray(array $data): self {
        return new LoginRequest($data);
    }

    /**
     * @return bool|mixed
     */
    final public function isSkipLogin(): bool {
        return (bool)$this->skip;
    }

    final public function needLogin(): bool {
        return !((bool)$this->skip);
    }

    /**
     * @return string|null
     */
    public function getSubject(): ?string {
        return $this->subject;
    }

    /**
     * @return string|null
     */
    public function getRequestUrl(): ?string {
        return $this->request_url;
    }

    /**
     * @return array
     */
    public function getClient(): array {
        return $this->client;
    }

    /**
     * @return array|mixed|null
     */
    public function getRequestedScopes() {
        return $this->requested_scope;
    }

    /**
     * @return array|mixed
     */
    public function getOidcContext() {
        return $this->oidc_context;
    }

    /**
     * @return array
     */
    public function getContext(): array {
        return $this->context;
    }

    public function getRedirectUrl() {
        return $this->redirect_url;
    }
}

