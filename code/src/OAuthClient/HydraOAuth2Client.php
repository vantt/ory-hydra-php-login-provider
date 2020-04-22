<?php

namespace App\OAuthClient;


use App\OAuthClient\OryHydraProvider;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use KnpU\OAuth2ClientBundle\Exception\InvalidStateException;
use KnpU\OAuth2ClientBundle\Exception\MissingAuthorizationCodeException;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class HydraOAuth2Client implements OAuth2ClientInterface {
    const OAUTH2_SESSION_STATE_KEY    = 'knpu.oauth2_client_state';
    const OAUTH2_SESSION_VERIFIER_KEY = 'knpu.oauth2_client_verifier';

    /** @var OryHydraProvider */
    protected $provider;

    /** @var RequestStack */
    protected $requestStack;

    /** @var bool */
    private $isStateless = false;

    /**
     * @var bool
     */
    private $isPKCE = true;


    /**
     * OAuth2Client constructor.
     */
    public function __construct(AbstractProvider $provider, RequestStack $requestStack) {
        $this->provider     = $provider;
        $this->requestStack = $requestStack;
    }

    /**
     * Call this to avoid using and checking "state".
     */
    public function setAsStateless() {
        $this->isStateless = true;
    }

    /**
     * Creates a RedirectResponse that will send the user to the
     * OAuth2 server (e.g. send them to Facebook).
     *
     * @param array $scopes  The scopes you want (leave empty to use default)
     * @param array $options Extra options to pass to the "Provider" class
     *
     * @return RedirectResponse
     */
    public function redirect(array $scopes = [], array $options = []) {
        if ($this->provider->isPKCE()) {
            dump('PKCE');
            $code_verifier  = $this->provider->getCodeVerifier();
            $code_challenge = hash('sha256', $code_verifier);

            $options += [
              'code_challenge'        => $this->base64_url_encode($code_challenge),
              'code_challenge_method' => 'S256',
            ];

            $this->getSession()->set(self::OAUTH2_SESSION_VERIFIER_KEY, $code_verifier);
        }

        if (!empty($scopes)) {
            $options['scope'] = $scopes;
        }
dump($options);
        $url = $this->provider->getAuthorizationUrl($options);
dump($url);
        // set the state (unless we're stateless)
        if (!$this->isStateless()) {
            $this->getSession()->set(self::OAUTH2_SESSION_STATE_KEY, $this->provider->getState());
        }

        return new RedirectResponse($url);
    }

    protected function base64_url_encode( $data ): string {
        return rtrim(strtr( base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Call this after the user is redirected back to get the access token.
     *
     * @param array $options Additional options that should be passed to the getAccessToken() of the underlying provider
     *
     * @return AccessToken|\League\OAuth2\Client\Token\AccessTokenInterface
     *
     * @throws InvalidStateException
     * @throws MissingAuthorizationCodeException
     * @throws IdentityProviderException         If token cannot be fetched
     */
    public function getAccessToken(array $options = []) {
        if ($this->provider->isPKCE()) {
            dump('PKCE');
            $options['code_verifier'] = $this->base64_url_encode($this->getSession()->get(self::OAUTH2_SESSION_VERIFIER_KEY));
        }

        if (!$this->isStateless()) {
            $expectedState = $this->getSession()->get(self::OAUTH2_SESSION_STATE_KEY);
            $actualState   = $this->getCurrentRequest()->query->get('state');
            if (!$actualState || ($actualState !== $expectedState)) {
                throw new InvalidStateException('Invalid state');
            }
        }

        $code = $this->getCurrentRequest()->get('code');

        if (!$code) {
            throw new MissingAuthorizationCodeException('No "code" parameter was found (usually this is a query parameter)!');
        }

        $accessToken = $this->provider->getAccessToken('authorization_code', array_merge(['code' => $code], $options));

        if ($this->provider->isPKCE()) {
            $this->getSession()->remove(self::OAUTH2_SESSION_VERIFIER_KEY);
        }

        return  $accessToken;
    }

    /**
     * Returns the "User" information (called a resource owner).
     *
     * @return \League\OAuth2\Client\Provider\ResourceOwnerInterface
     */
    public function fetchUserFromToken(AccessToken $accessToken) {
        return $this->provider->getResourceOwner($accessToken);
    }

    /**
     * Shortcut to fetch the access token and user all at once.
     *
     * Only use this if you don't need the access token, but only
     * need the user.
     *
     * @return \League\OAuth2\Client\Provider\ResourceOwnerInterface
     */
    public function fetchUser() {
        /** @var AccessToken $token */
        $token = $this->getAccessToken();

        return $this->fetchUserFromToken($token);
    }

    /**
     * Returns the underlying OAuth2 provider.
     *
     * @return AbstractProvider
     */
    public function getOAuth2Provider() {
        return $this->provider;
    }

    protected function isStateless(): bool {
        return $this->isStateless;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    private function getCurrentRequest() {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request) {
            throw new \LogicException('There is no "current request", and it is needed to perform this action');
        }

        return $request;
    }

    /**
     * @return SessionInterface
     */
    private function getSession() {
        if (!$this->getCurrentRequest()->hasSession()) {
            throw new \LogicException('In order to use "state", you must have a session. Set the OAuth2Client to stateless to avoid state');
        }

        return $this->getCurrentRequest()->getSession();
    }
}