<?php

namespace App\Hydra;

use App\Hydra\DTO\ConsentRequest;
use App\Hydra\DTO\LoginRequest;
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
     * HydraLogin constructor.
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
     * @return HydraLogin
     * @throws HydraException
     */
    public function fetchConsentRequest(string $challenge): HydraLogin {
        $request = null;

        if (!$request = $this->fetchFromSession($challenge)) {
            $request = $this->fetchFromHydra($challenge);
        }

        return $request;
    }

    private function fetchFromSession(string $challenge): ?HydraLogin {
        // fetch from session
        $login_request   = $this->session->get('hydra_consent_request', null);

        if ($login_request && $login_request instanceof ConsentRequest && $this->isValidLoginRequest($challenge, $login_request)) {
            return new HydraLogin($login_request, $this->hydra);
        }

        return null;
    }

    /**
     * @param string $challenge
     *
     * @return HydraLogin|null
     * @throws HydraException
     */
    private function fetchFromHydra(string $challenge): ?HydraLogin {
        $request   = $this->hydra->fetchLoginRequest($challenge);

        if ($this->isValidLoginRequest($challenge, $request)) {
            $this->session->set('hydra_consent_request', $request);

            return new HydraConsent($request, $this->hydra);
        }

        return null;
    }

    public function isValidLoginRequest(string $current_challenge, LoginRequest $old_login_request): bool {
        return ($old_login_request->getChallenge() === $current_challenge);
    }
}
