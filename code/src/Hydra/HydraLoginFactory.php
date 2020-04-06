<?php

namespace App\Hydra;

use App\Hydra\DTO\LoginRequest;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class HydraLoginFactory {

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
    public function fetchLoginRequest(string $challenge): HydraLogin {
        $login_request = null;

        if (!$login_request = $this->fetchFromSession($challenge)) {
            $login_request = $this->fetchFromHydra($challenge);
        }

        return $login_request;
    }

    private function fetchFromSession(string $challenge): ?HydraLogin {
        // fetch from session
        $login_request   = $this->session->get('hydra_login_request', null);

        if ($login_request && $login_request instanceof LoginRequest && $this->isValidLoginRequest($challenge, $login_request)) {
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
        $login_request   = $this->hydra->fetchLoginRequest($challenge);

        if ($this->isValidLoginRequest($challenge, $login_request)) {
            $this->session->set('hydra_login_request', $login_request);

            return new HydraLogin($login_request, $this->hydra);
        }

        return null;
    }

    public function isValidLoginRequest(string $current_challenge, LoginRequest $old_login_request): bool {
        return ($old_login_request->getChallenge() === $current_challenge);
    }
}
