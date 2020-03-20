<?php
declare(strict_types=1);

namespace App\Service;

use Psr\Http\Client\ClientInterface;

class HydraClient {

    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * LoginController constructor.
     */
    public function __construct(ClientInterface $httpClient) {
        $this->httpClient = $httpClient;
    }

    private function fetchLogin() {
        $this->httpClient->sendRequest();
    }

    private function acceptLogin() {
        $this->httpClient->sendRequest();
    }

}