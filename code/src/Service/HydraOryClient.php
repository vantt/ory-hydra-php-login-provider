<?php
declare(strict_types = 1);

namespace App\Service;

use Ory\Hydra\Client\Api\AdminApi;
use Ory\Hydra\Client\ApiException;
use Ory\Hydra\Client\Configuration;
use Ory\Hydra\Client\Model\GenericError;
use Ory\Hydra\Client\Model\LoginRequest as HydraLoginRequest;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

class HydraOryClient implements HydraClientInterface {

    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var RequestFactoryInterface
     */
    private $requestFactory;

    /**
     * @var UriFactoryInterface
     */
    private $uriFactory;
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
     * @see https://www.ory.sh/docs/hydra/sdk/api#get-a-login-request
     * @see https://www.ory.sh/docs/hydra/sdk/api#schemaloginrequest
     */
    final public function fetchLoginRequest(string $challenge): LoginRequest {
        $adminApi = $this->getHydraAdminAPI();

        try {
            $response = $adminApi->getLoginRequest($challenge);

            if ($response instanceof HydraLoginRequest) {
                return LoginRequest::fromArray([
                                                 'skip'            => (bool)$response->getSkip(),

                                                 // The user-id of the already authenticated user - only set if skip is true
                                                 'subject'         => (string)$response->getSubject(),

                                                 // The initial OAuth 2.0 request url
                                                 'request_url'     => (string)$response->getRequestUrl(),

                                                 // The OAuth 2.0 client that initiated the request
                                                 'client'          => (array)$response->getClient(),

                                                 // The OAuth 2.0 Scope requested by the client,
                                                 'requested_scope' => (array)$response->getRequestedScope(),

                                                 // Information on the OpenID Connect request - only required to process if your UI should support these values.
                                                 'oidc_context'    => $response->getOidcContext(),

                                                 // Context is an optional object which can hold arbitrary data. The data will be made available when fetching the
                                                 // consent request under the "context" field. This is useful in scenarios where login and consent endpoints share
                                                 // data.
                                                 'context'         => [],
                                               ]
                );
            }

            if ($response instanceof GenericError) {
                return null;
            }
        } catch (ApiException $e) {
            return null;
        }

        return null;
    }

    /**
     * @param string $challenge
     *
     *
     * @throws ApiException
     *
     * @see https://www.ory.sh/docs/hydra/sdk/api#accept-a-login-request
     * @see https://www.ory.sh/docs/hydra/sdk/api#schemaacceptloginrequest
     */
    public function acceptLogin($challenge) {
        $this->getHydraAdminAPI()->acceptLoginRequest($challenge);
    }

    public function rejectLogin($challenge) {
        // TODO: Implement rejectLogin() method.
    }

    private function getHydraAdminAPI(): AdminApi {
        $config = Configuration::getDefaultConfiguration()
                               ->setHost($this->hydraEndPoint);

        return new AdminApi(null, $config);
    }
}