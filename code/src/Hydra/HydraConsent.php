<?php

namespace App\Hydra;

use App\Hydra\DTO\CompletedRequest;
use App\Hydra\DTO\ConsentRequest;
use Symfony\Component\Console\Output\ConsoleSectionOutput;

class HydraConsent {

    /**
     * @var HydraClientInterface
     */
    private $hydra;


    /**
     * @var ?ConsentRequest
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
        $this->hydra         = $hydraClient;
    }

    /**
     * @see https://www.ory.sh/docs/hydra/sdk/api#accept-a-consent-request
     * @see https://www.ory.sh/docs/hydra/sdk/api#schemacompletedrequest
     *
     * @param array $options
     *
     * @return CompletedRequest
     */
    final public function acceptConsentRequest(array $options): CompletedRequest {
        return $this->hydra->acceptConsentRequest($this->consent_request->getChallenge(), $options);
    }

    /**
     * @param array $options
     *
     * @return CompletedRequest
     *
     * @see https://www.ory.sh/docs/hydra/sdk/api#reject-a-consent-request
     */
    final public function rejectConsentRequest(array $options): CompletedRequest {
        return $this->hydra->rejectConsentRequest($this->consent_request->getChallenge(), $options);
    }

    public function isSkipLogin(): bool {
        return ($this->consent_request->getSkip() === true);
    }

    public function getConsentRequest(): ConsentRequest {
        return $this->consent_request;
    }

}