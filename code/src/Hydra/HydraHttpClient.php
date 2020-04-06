<?php
declare(strict_types = 1);

namespace App\Hydra;

use App\Hydra\DTO\CompletedRequest;
use App\Hydra\DTO\LoginRequest;
use App\Hydra\HydraClientInterface;
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
    private $hydraAdminEndPoint;

    /**
     * LoginController constructor.
     *
     * @param string                        $hydraAdminEndPoint
     * @param ClientInterface               $httpClient
     * @param ServerRequestFactoryInterface $requestFactory
     * @param UriFactoryInterface           $uriFactory
     */
    public function __construct(string $hydraAdminEndPoint,
                                ClientInterface $httpClient,
                                ServerRequestFactoryInterface $requestFactory,
                                UriFactoryInterface $uriFactory) {

        $this->hydraAdminEndPoint = $hydraAdminEndPoint;
        $this->httpClient         = $httpClient;
        $this->requestFactory     = $requestFactory;
        $this->uriFactory         = $uriFactory;
    }

    public function fetchLoginRequest(string $challenge): LoginRequest {

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

        $uri = $this->uriFactory->createUri($this->hydraAdminEndPoint);
        $uri = $uri->withPath('/oauth2/auth/requests/login')
                   ->withQuery(http_build_query(['login_challenge' => $challenge]));

        // the final endpoint should be
        // http://hydra.app/oauth2/auth/requests/login?login_challenge=85689347569345395
        return $this->requestFactory->createRequest('GET', $uri)
                                    ->withHeader('Accept', 'application/json');
    }

    public function acceptLoginRequest(string $challenge, array $options = []): CompletedRequest {
        // TODO: Implement acceptLoginRequest() method.
    }

    public function rejectLoginRequest(string $challenge, array $options = []): CompletedRequest {
        // TODO: Implement rejectLoginRequest() method.
    }


}