<?php

namespace App\Hydra;

use App\Hydra\DTO\CompletedRequest;
use App\Hydra\DTO\ConsentRequest;

class HydraConsent {

    /**
     * @var HydraClientInterface
     */
    private $hydra;

    /**
     * @var ConsentRequest
     * @see https://www.ory.sh/docs/hydra/sdk/api#get-consent-request-information
     */
    private $consent_request;

    /**
     * HydraLogin constructor.
     *
     * @param ConsentRequest       $request
     * @param HydraClientInterface $hydraClient
     */
    public function __construct(ConsentRequest $request, HydraClientInterface $hydraClient) {
        $this->consent_request = $request;
        $this->hydra           = $hydraClient;
    }

    /**
     * @see https://www.ory.sh/docs/hydra/sdk/api#accept-a-consent-request
     * @see https://www.ory.sh/docs/hydra/sdk/api#schemacompletedrequest
     *
     * @param array     $grant_scope
     * @param array     $access_token_data
     * @param array     $id_token_data
     * @param bool|null $remember
     * @param int|null  $remember_for
     * @param array     $extra_options
     *
     * @return CompletedRequest
     * @throws HydraException
     */
    final public function acceptConsentRequest(array $grant_scope = [], array $access_token_data = [], array $id_token_data = [], ?bool $remember = null, ?int $remember_for = null, array $extra_options = []): CompletedRequest {
        $session_data = [];
        $options      = [];

        $options['grantScope']               = !empty($grant_scope) ? $grant_scope : $this->consent_request->getRequestedScope();
        $options['grantAccessTokenAudience'] = $this->consent_request->getRequestedAccessTokenAudience();

        if (null !== $remember) {
            $options['remember'] = $remember;
        }

        if (null !== $remember_for) {
            $options['rememberFor'] = $remember_for;
        }

        if (!empty($access_token_data)) {
            $session_data['access_token'] = $access_token_data;
        }

        if (!empty($id_token_data)) {
            $session_data['id_token'] = $id_token_data;
        }

        if (!empty($session_data)) {
            $options['session'] = $session_data;
        }

        return $this->hydra->acceptConsentRequest($this->consent_request->getChallenge(), $options);
    }

    /**
     * @param array $options
     *
     * @return CompletedRequest
     *
     * @throws HydraException
     *
     * @see https://www.ory.sh/docs/hydra/sdk/api#reject-a-consent-request
     */
    final public function rejectConsentRequest(
                              string $error = 'access_denied',
                              string $error_description = 'The resource owner denied the request',
                              array $extraoptions = []): CompletedRequest {
        $options['error']             = $error;
        $options['error_description'] = $error_description;

        return $this->hydra->rejectConsentRequest($this->consent_request->getChallenge(), $options);
    }

    public function isSkip(): bool {
        return ($this->consent_request->getSkip() === true);
    }

    public function getConsentRequest(): ConsentRequest {
        return $this->consent_request;
    }

}