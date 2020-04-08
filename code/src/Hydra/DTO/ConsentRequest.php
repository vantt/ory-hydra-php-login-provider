<?php

namespace App\Hydra\DTO;

/**
 * Class ConsentRequest
 * @package App\Hydra\DTO
 *
 * @see     https://www.ory.sh/docs/hydra/sdk/api#schemaconsentrequest
 */
class ConsentRequest {

    /**
     * @var string
     */
    private $challenge;

    // Skip, if true, implies that the client has requested the same scopes from the same user previously.
    // If true, you can skip asking the user to grant the requested scopes, and simply forward the user to the redirect URL.
    //
    // This feature allows you to update / set session information.
    /**
     * @var bool
     */
    private $skip;

    /**
     * The user-id of the already authenticated user - only set if skip is true
     *
     * @var string
     */
    private $subject;

    /**
     * @var string
     */
    private $login_challenge;

    /**
     * @var string
     */
    private $login_session_id;

    /**
     * The initial OAuth 2.0 request url
     *
     * @var string
     */
    private $request_url;


    /**
     * @var array
     *
     * The OAuth 2.0 client that initiated the request
     */
    private $client;

    // The OAuth 2.0 Scope requested by the client,
    /**
     * @var array
     */
    private $requested_scope = [];

    /**
     * @var  array
     */
    private $requested_access_token_audience = [];

    /**
     * Information on the OpenID Connect request - only required to process if your UI should support these values.
     *
     * @var string[]
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
     * ConsentRequest constructor.
     *
     * @param array $data
     *
     * @see https://www.ory.sh/docs/hydra/sdk/api#schemaconsentrequest
     */
    private function __construct(array $data) {
        $important_keys = [
          'challenge',
          'skip',
          'subject',
          'request_url',
          'client',
          'login_challenge',
          'login_session_id',
          'requested_access_token_audience',
          'requested_scope',
          'oidc_context',
          'context',
        ];

        $missing_keys = array_diff($important_keys, array_keys($data));

        if (!empty($missing_keys)) {
            throw new \InvalidArgumentException(sprintf('Missing many important array items: %s', implode(', ', $missing_keys)));
        }

        $this->skip                            = (bool)$data['skip'];
        $this->challenge                       = (string)$data['challenge'];
        $this->subject                         = (string)$data['subject'];
        $this->request_url                     = (string)$data['request_url'];
        $this->login_challenge                 = (string)$data['login_challenge'];
        $this->login_session_id                = (string)$data['login_session_id'];
        $this->requested_access_token_audience = (array)$data['requested_access_token_audience'];
        $this->requested_scope                 = (array)$data['requested_scope'];
        $this->client                          = $data['client'];
        $this->oidc_context                    = (array)$data['oidc_context'];
        $this->context                         = (array)$data['context'];
    }

    public static function fromArray(array $data): self {
        return new ConsentRequest($data);
    }

    /**
     * @return string
     */
    public function getChallenge(): string {
        return $this->challenge;
    }

    /**
     * @return bool
     */
    public function getSkip(): bool {
        return $this->skip;
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
    public function getLoginChallenge(): string {
        return $this->login_challenge;
    }

    /**
     * @return string
     */
    public function getLoginSessionId(): string {
        return $this->login_session_id;
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
    public function getClient() {
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

    /**
     * @return string
     */
    public function getRequestedAccessTokenAudience(): array {
        return $this->requested_access_token_audience;
    }
}

