<?php
declare(strict_types = 1);

namespace App\Service;

use Http\Message\RequestFactory;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

class HydraClient implements HydraClientInterface {

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
     * LoginController constructor.
     */
    public function __construct(ClientInterface $httpClient,
                                ServerRequestFactoryInterface $requestFactory,
                                UriFactoryInterface $uriFactory) {

        $this->httpClient     = $httpClient;
        $this->requestFactory = $requestFactory;
        $this->uriFactory     = $uriFactory;
    }

    public function fetchLogin($challenge): array {
        $request = $this->requestFactory->createServerRequest('GET', 'http://hydra:4444/oauth2/auth/requests/login?' . http_build_query(['login_challenge' => $challenge]))
                                        ->withHeader('Accept', 'application/json');

        dump($request);

        $response = $this->httpClient->sendRequest($request);
        $json     = [];

        dump($response->getBody());

        if (200 === (int)$response->getStatusCode()) {
            $json = json_decode($response->getBody());
        }

        return $json;
    }

    public function acceptLogin($challenge) {
        // TODO: Implement acceptLogin() method.
    }

    public function rejectLogin($challenge) {
        // TODO: Implement rejectLogin() method.
    }


}