<?php

namespace App\Hydra;

use App\Hydra\DTO\ConsentRequest;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class HydraConsentFactory {

    /**
     * @var HydraClientInterface
     */
    private $hydra;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     *
     * @param HydraClientInterface $hydraClient
     * @param SessionInterface     $session
     *
     * @todo handling exception from HydraClientInterface
     */
    public function __construct(HydraClientInterface $hydraClient, SessionInterface $session) {
        $this->hydra   = $hydraClient;
        $this->session = $session;
    }

    /**
     * @param string $challenge
     *
     * @return HydraConsent
     * @throws HydraException
     */
    public function fetchConsentRequest(string $challenge): HydraConsent {
        $request = null;

        if (!$request = $this->fetchFromSession($challenge)) {
            $request = $this->fetchFromHydra($challenge);
        }

        return $request;
    }

    private function fetchFromSession(string $challenge): ?HydraConsent {
        // fetch from session
        $request   = $this->session->get('hydra_consent_request', null);

        if ($request && $request instanceof ConsentRequest && $this->isValidRequest($challenge, $request)) {
            return new HydraConsent($request, $this->hydra);
        }

        return null;
    }

    /**
     * @param string $challenge
     *
     * @return HydraConsent|null
     * @throws HydraException
     */
    private function fetchFromHydra(string $challenge): ?HydraConsent {
        $request   = $this->hydra->fetchConsentRequest($challenge);

        if ($this->isValidRequest($challenge, $request)) {
            $this->session->set('hydra_consent_request', $request);

            return new HydraConsent($request, $this->hydra);
        }

        return null;
    }

    public function isValidRequest(string $current_challenge, ConsentRequest $request): bool {
        return ($request->getChallenge() === $current_challenge);
    }
}
