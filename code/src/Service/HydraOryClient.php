<?php
declare(strict_types = 1);

namespace App\Service;

use Ory\Hydra\Client\Api\AdminApi;
use Ory\Hydra\Client\ApiException;
use Ory\Hydra\Client\Configuration;
use Ory\Hydra\Client\Model\GenericError;
use Ory\Hydra\Client\Model\LoginRequest;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
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
     * @param string                        $hydraEndPoint
     * @param ClientInterface               $httpClient
     * @param ServerRequestFactoryInterface $requestFactory
     * @param UriFactoryInterface           $uriFactory
     */
    public function __construct(string $hydraEndPoint,
                                ClientInterface $httpClient,
                                ServerRequestFactoryInterface $requestFactory,
                                UriFactoryInterface $uriFactory) {

        $this->hydraEndPoint  = $hydraEndPoint;
        $this->httpClient     = $httpClient;
        $this->requestFactory = $requestFactory;
        $this->uriFactory     = $uriFactory;
    }

    /**
     * @param string $challenge
     *
     * @return \Ory\Hydra\Client\Model\GenericError|\Ory\Hydra\Client\Model\LoginRequest
     * @throws \Ory\Hydra\Client\ApiException
     */
    final public function fetchLoginRequest(string $challenge): array {
        $adminApi = $this->getHydraAdminAPI();

        try {
            $response = $adminApi->getLoginRequest($challenge);

            if ($response instanceof LoginRequest) {
                $loginRequest = [
                  "skip"            => (bool)$response->getSkip(),

                  // The user-id of the already authenticated user - only set if skip is true
                  "subject"         => (string)$response->getSubject(),

                  // The initial OAuth 2.0 request url
                  "request_url"     => (string)$response->getRequestUrl(),

                  // The OAuth 2.0 client that initiated the request
                  "client"          => (array)$response->getClient(),

                  // The OAuth 2.0 Scope requested by the client,
                  "requested_scope" => (array)$response->getRequestedScope(),

                  // Information on the OpenID Connect request - only required to process if your UI should support these values.
                  "oidc_context"    => $response->getOidcContext(),

                  // Context is an optional object which can hold arbitrary data. The data will be made available when fetching the
                  // consent request under the "context" field. This is useful in scenarios where login and consent endpoints share
                  // data.
                  "context"         => [],
                ];

                return $loginRequest;
            }

            if ($response instanceof GenericError) {
                return null;
            }
        } catch (ApiException $e) {
            return null;
        }

        return null;
    }

    private function getHydraAdminAPI(): AdminApi {
        $config = Configuration::getDefaultConfiguration()
                               ->setHost($this->hydraEndPoint);

        return new AdminApi(null, $config);
    }

    /**
     * @param string $challenge
     *
     * @return ServerRequestInterface
     */
    private function createFetchLoginRequest(string $challenge): RequestInterface {

        $uri = $this->uriFactory->createUri($this->hydraEndPoint);
        $uri = $uri->withPath('/oauth2/auth/requests/login')
                   ->withQuery(http_build_query(['login_challenge' => $challenge]));

        // the final endpoint should be
        // http://hydra.app/oauth2/auth/requests/login?login_challenge=85689347569345395
        return $this->requestFactory->createRequest('GET', $uri)
                                    ->withHeader('Accept', 'application/json');
    }

    public function acceptLogin($challenge) {
        // TODO: Implement acceptLogin() method.
    }

    public function rejectLogin($challenge) {
        // TODO: Implement rejectLogin() method.
    }


}