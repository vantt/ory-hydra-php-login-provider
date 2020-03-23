<?php
declare(strict_types = 1);

namespace App\Service;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriFactoryInterface;

class HydraHttpClient implements HydraClientInterface {

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
     * @return array
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    final public function fetchLoginRequest(string $challenge): array {

        $request  = $this->createFetchLoginRequest($challenge);
        $response = $this->httpClient->sendRequest($request);

        $json = [];

        dump($response);

        if (200 === (int)$response->getStatusCode()) {
            $json = json_decode($response->getBody());
        }

        return $json;
    }


    /**
     * @param string $challenge
     *
     * @return RequestInterface
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