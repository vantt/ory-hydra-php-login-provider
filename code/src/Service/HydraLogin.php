<?php


namespace App\Service;


use Symfony\Component\HttpFoundation\Session\SessionInterface;

class HydraLogin {

    /**
     * @var HydraClientInterface
     */
    private $hydra;

    /**
     * @var SessionInterface
     */
    private $session;

    private $login_challenge = null;

    private $login_request = null;

    private $isSkip = false;

    /**
     * HydraLogin constructor.
     *
     * @param HydraClientInterface $hydraClient
     * @param SessionInterface     $session
     */
    public function __construct(HydraClientInterface $hydraClient, SessionInterface $session) {
        $this->hydra   = $hydraClient;
        $this->session = $session;
    }

    // https://github.com/ory/hydra-login-consent-node/blob/master/routes/login.js
    final public function startLogin(string $challenge): self {
        $this->isSkip = false;

        if (!$this->isValidLoginRequest($challenge)) {
            if ($this->fetchLogin($challenge))  {
                return $this;
            }
            else {
                // @todo: throw new Exception
            }
        }

        return $this;
    }

    private function fetchLogin($challenge): bool {
        // fetch from session
        $this->login_challenge = $this->session->get('login_challenge', null);
        $this->login_request   = $this->session->get('login_request', null);

        // fetch from hydra
        if (!$this->isValidLoginRequest($challenge)) {
            $this->login_request   = $this->hydra->fetchLoginRequest($challenge);
            $this->login_challenge = $challenge;
        }

        if ($this->isValidLoginRequest($challenge)) {
            $this->session->set('login_challenge', $this->login_challenge);
            $this->session->set('login_request', $this->login_request);

            $this->isSkip = $this->login_request['skip'];

            return true;
        }

        return false;
    }

    public function needLogin() {
        return ($this->isSkip === false);
    }

    public function isSkipLogin() {
        return ($this->isSkip === true);
    }

    public function acceptLogin() {

    }

    public function rejectLogin() {

    }

    private function isValidLoginRequest(string $challange) {
        $hasLoginRequest = ($this->login_challenge !== null && is_array($this->login_request) && !empty($this->login_request));
        $isValid         = $hasLoginRequest;

        if (!empty($challange)) {
            $isValid = ($this->login_challenge === $challange && $hasLoginRequest);
        }

        return $isValid;
    }

}