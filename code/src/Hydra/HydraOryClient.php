<?php
declare(strict_types = 1);

namespace App\Hydra;

use App\Hydra\DTO\CompletedRequest;
use App\Hydra\DTO\ConsentRequest;
use App\Hydra\DTO\LoginRequest;
use Ory\Hydra\Client\Api\AdminApi;
use Ory\Hydra\Client\ApiException;
use Ory\Hydra\Client\Configuration;

use Ory\Hydra\Client\Model\GenericError;
use Ory\Hydra\Client\Model\LoginRequest as HydraLoginRequest;
use Ory\Hydra\Client\Model\AcceptLoginRequest as HydraAcceptLoginRequest;
use Ory\Hydra\Client\Model\CompletedRequest as HydraCompletedRequest;
use Ory\Hydra\Client\Model\RejectRequest as HydraRejectRequest;
use Ory\Hydra\Client\Model\ConsentRequest as HydraConsentRequest;
use Ory\Hydra\Client\Model\AcceptConsentRequest as HydraAcceptConsentRequest;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

class HydraOryClient implements HydraClientInterface {
    /**
     * @var string
     */
    private $hydraEndPoint;

    /**
     * LoginController constructor.
     *
     * @param string $hydraAdminEndPoint
     */
    public function __construct(string $hydraAdminEndPoint) {
        $this->hydraEndPoint = $hydraAdminEndPoint;
    }

    /**
     * @param string $challenge
     *
     * @return LoginRequest
     *
     * @throws HydraException
     *
     * @see https://www.ory.sh/docs/hydra/sdk/api#get-a-login-request
     * @see https://www.ory.sh/docs/hydra/sdk/api#schemaloginrequest
     */
    final public function fetchLoginRequest(string $challenge): LoginRequest {

        /** @var HydraLoginRequest $response */
        $response = $this->hydraFlow('get_login_request', $challenge, null);
        assert($response instanceof HydraLoginRequest);

        return LoginRequest::fromArray(
          [
            'challenge' => (string)$response->getChallenge(),

            'skip'            => (bool)$response->getSkip(),

            // The user-id of the already authenticated user - only set if skip is true
            'subject'         => (string)$response->getSubject(),

            // The initial OAuth 2.0 request url
            'request_url'     => (string)$response->getRequestUrl(),

            // The OAuth 2.0 client that initiated the request
            'client'          => $response->getClient(),

            // The OAuth 2.0 Scope requested by the client,
            'requested_scope' => (array)$response->getRequestedScope(),

            // Information on the OpenID Connect request - only required to process if your UI should support these values.
            'oidc_context'    => (array)$response->getOidcContext(),

            // Context is an optional object which can hold arbitrary data. The data will be made available when fetching the
            // consent request under the "context" field. This is useful in scenarios where login and consent endpoints share
            // data.
            'context'         => [],
          ]
        );
    }

    /**
     * @param string $challenge
     *
     * @return CompletedRequest
     * @throws HydraException
     *
     * @todo  there are many login-accept options to handle, please review when having relating businesses.
     *
     * @see   https://www.ory.sh/docs/hydra/sdk/api#accept-a-login-request
     * @see   https://www.ory.sh/docs/hydra/sdk/api#schemaacceptloginrequest
     */
    public function acceptLoginRequest($challenge, array $options = []): CompletedRequest {
        $request = new HydraAcceptLoginRequest();

        if (null !== ($value = $options['subject'] ?? null)) {
            $request->setSubject($value);
        }

        if (null !== ($value = $options['remember'] ?? null)) {
            $request->setRemember($value);
        }

        // When the session expires, in seconds. Set this to 0 so it will never expire
        if (null !== ($value = $options['remember_for'] ?? null)) {
            $request->setRememberFor($value);
        }

        /** @var HydraCompletedRequest $response */
        $response = $this->hydraFlow('accept_login_request', $challenge, $request);
        assert($response instanceof HydraCompletedRequest);

        return CompletedRequest::fromArray(
          [
            'redirect_to' => (string)$response->getRedirectTo(),
          ]
        );
    }

    /**
     * @param string $challenge
     * @param array  $options
     *
     * @return CompletedRequest
     * @throws HydraException
     *
     * @see https://www.ory.sh/docs/hydra/sdk/api#reject-a-login-request
     * @see https://www.ory.sh/docs/hydra/sdk/api#schemarejectrequest
     * @see https://www.ory.sh/docs/hydra/sdk/api#schemacompletedrequest
     */
    public function rejectLoginRequest($challenge, array $options = []): CompletedRequest {
        $request = new HydraRejectRequest();

        if (null !== ($value = $options['error'] ?? null)) {
            $request->setError($value);
        }

        if (null !== ($value = $options['error_debug'] ?? null)) {
            $request->setErrorDebug($value);
        }

        if (null !== ($value = $options['error_description'] ?? null)) {
            $request->setErrorDescription($value);
        }

        if (null !== ($value = $options['error_hint'] ?? null)) {
            $request->setErrorHint($value);
        }

        /** @var HydraCompletedRequest $response */
        $response = $this->hydraFlow('accept_login_request', $challenge, $request);
        assert($response instanceof HydraCompletedRequest);

        return CompletedRequest::fromArray(
          [
            'redirect_to' => (string)$response->getRedirectTo(),
          ]
        );
    }

    /**
     * @param string $challenge
     *
     * @return ConsentRequest
     *
     * @throws HydraException
     *
     * @see https://www.ory.sh/docs/hydra/sdk/api#get-consent-request-information
     * @see https://www.ory.sh/docs/hydra/sdk/api#schemaconsentrequest
     */
    final public function fetchConsentRequest(string $challenge): ConsentRequest {

        /** @var HydraConsentRequest $response */
        $response = $this->hydraFlow('get_consent_request', $challenge, null);
        assert($response instanceof HydraConsentRequest);

        return ConsentRequest::fromArray(
          [
            'challenge' => (string)$response->getChallenge(),

            'skip'        => (bool)$response->getSkip(),

            // The user-id of the already authenticated user - only set if skip is true
            'subject'     => (string)$response->getSubject(),

            // The initial OAuth 2.0 request url
            'request_url' => (string)$response->getRequestUrl(),

            'login_challenge' => (string)$response->getLoginChallenge(),

            'login_session_id' => (string)$response->getLoginSessionId(),

            // The OAuth 2.0 client that initiated the request
            'client'           => $response->getClient(),

            'requested_access_token_audience' => (array)$response->getRequestedAccessTokenAudience(),

            // The OAuth 2.0 Scope requested by the client,
            'requested_scope'                 => (array)$response->getRequestedScope(),

            // Information on the OpenID Connect request - only required to process if your UI should support these values.
            'oidc_context'                    => (array)$response->getOidcContext(),

            // Context is an optional object which can hold arbitrary data. The data will be made available when fetching the
            // consent request under the "context" field. This is useful in scenarios where login and consent endpoints share
            // data.
            'context'                         => [],
          ]
        );
    }

    /**
     * @param string $challenge
     *
     *
     * @return CompletedRequest
     * @throws HydraException
     *
     * @todo  there are many consent-accept options to handle, please review when having relating businesses.
     *
     * @see   https://www.ory.sh/docs/hydra/sdk/api#accept-a-consent-request
     * @see   https://www.ory.sh/docs/hydra/sdk/api#schemaacceptconsentrequest
     */
    public function acceptConsentRequest(string $challenge, array $options = []): CompletedRequest {

        //        {
        //            "grant_access_token_audience": ["string"],
        //  "grant_scope": ["string"],
        //  "handled_at": "2020-04-04T21:15:27Z",
        //  "remember": true,
        //  "remember_for": 0,
        //  "session": {
        //            "access_token": {
        //                "property1": {},
        //      "property2": {}
        //    },
        //    "id_token": {
        //                "property1": {},
        //      "property2": {}
        //    }
        //  }
        //}

        $request = new HydraAcceptConsentRequest();

        if (null !== ($value = $options['session'] ?? null)) {
            $request->setSession($value);
        }

        if (null !== ($value = $options['remember'] ?? null)) {
            $request->setRemember($value);
        }

        if (null !== ($value = $options['remember_for'] ?? null)) {
            $request->setRememberFor($value);
        }

        /** @var HydraCompletedRequest $response */
        $response = $this->hydraFlow('accept_consent_request', $challenge, $request);
        assert($response instanceof HydraCompletedRequest);

        return CompletedRequest::fromArray(
          [
            'redirect_to' => (string)$response->getRedirectTo(),
          ]
        );
    }

    /**
     * @param string $challenge
     * @param array  $options
     *
     * @return CompletedRequest
     * @throws HydraException
     *
     * @see https://www.ory.sh/docs/hydra/sdk/api#reject-a-consent-request
     * @see https://www.ory.sh/docs/hydra/sdk/api#schemarejectrequest
     * @see https://www.ory.sh/docs/hydra/sdk/api#schemacompletedrequest
     */
    public function rejectConsentRequest($challenge, array $options = []): CompletedRequest {
        $request = new HydraRejectRequest();

        if (null !== ($value = $options['error'] ?? null)) {
            $request->setError($value);
        }

        if (null !== ($value = $options['error_debug'] ?? null)) {
            $request->setErrorDebug($value);
        }

        if (null !== ($value = $options['error_description'] ?? null)) {
            $request->setErrorDescription($value);
        }

        if (null !== ($value = $options['error_hint'] ?? null)) {
            $request->setErrorHint($value);
        }

        /** @var HydraCompletedRequest $response */
        $response = $this->hydraFlow('reject_consent_request', $challenge, $request);
        assert($response instanceof HydraCompletedRequest);

        return CompletedRequest::fromArray(
          [
            'redirect_to' => (string)$response->getRedirectTo(),
          ]
        );
    }

    /**
     * @param string $action
     * @param string $challenge
     * @param        $options
     *
     * @return HydraCompletedRequest|HydraConsentRequest|HydraLoginRequest|GenericError|null
     * @throws HydraException
     */
    private function hydraFlow(string $action, string $challenge, $options) {
        $response = null;

        try {
            switch ($action) {
                case 'get_login_request':
                    $response = $this->getHydraAdminAPI()->getLoginRequest($challenge);
                    break;

                case 'accept_login_request':
                    $response = $this->getHydraAdminAPI()->acceptLoginRequest($challenge, $options);
                    break;

                case 'reject_login_request';
                    $response = $this->getHydraAdminAPI()->rejectLoginRequest($challenge, $options);
                    break;

                case 'get_consent_request':
                    $response = $this->getHydraAdminAPI()->getConsentRequest($challenge);
                    break;

                case 'accept_consent_request':
                    $response = $this->getHydraAdminAPI()->acceptConsentRequest($challenge, $options);
                    break;

                case 'reject_consent_request':
                    $response = $this->getHydraAdminAPI()->rejectConsentRequest($challenge, $options);
                    break;
            }
        } catch (ApiException $e) {
            throw new HydraException($e->getMessage(), $e->getCode(), $e->getResponseHeaders(), $e->getResponseBody());
        }

        if ($response instanceof GenericError) {
            throw new HydraException($response->getErrorDescription(), $response->getStatusCode());
        }

        return $response;
    }

    /**
     * @return AdminApi
     */
    private function getHydraAdminAPI(): AdminApi {
        $config = Configuration::getDefaultConfiguration()
                               ->setHost($this->hydraEndPoint);

        return new AdminApi(null, $config);
    }
}