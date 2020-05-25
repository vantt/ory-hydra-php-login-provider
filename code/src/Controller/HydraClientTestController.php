<?php

namespace App\Controller;

use GuzzleHttp\Client;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Vantt\OAuth2\Client\Provider\OryHydraProvider;

class HydraClientTestController extends AbstractController {

    /**
     * @var OAuth2ClientInterface
     */
    private $client;

    /**
     * TestClientController constructor.
     *
     * @param ClientRegistry $clientRegistry
     */
    public function __construct(ClientRegistry $clientRegistry) {
        // on Symfony 3.3 or lower, $clientRegistry = $this->get('knpu.oauth2.registry');

        // key used in config/packages/knpu_oauth2_client.yaml
        $this->client = $clientRegistry->getClient('ory_hydra');
    }

    /**
     * Link to this controller to start the "connect" process
     *
     * @Route("/test-connect/client-credentials", name="connect_hydra_client_credentials")
     * @see https://github.com/thephpleague/oauth2-client#client-credentials-grant
     */
    public function connectClientCredentials(): Response {
        // Note: the GenericProvider requires the `urlAuthorize` option, even though
        // it's not used in the OAuth 2.0 client credentials grant type.

        $provider = new OryHydraProvider([
                                           'baseUrl'      => 'https://sso.dev.mio',
                                           'clientId'     => 'machine',
                                           // The client ID assigned to you by the provider
                                           'clientSecret' => 'some-secret',
                                         ]
        );

        $provider->setHttpClient(new Client(['verify' => false]));

        try {
            // Try to get an access token using the client credentials grant.
            $accessToken = $provider->getAccessToken('client_credentials');

            return new JsonResponse($accessToken);
        } catch (IdentityProviderException $e) {
            // Failed to get the access token
            exit($e->getMessage());
        }
    }

    /**
     * Link to this controller to start the "connect" process
     *
     * @Route("/test-connect/authorization-code", name="connect_hydra_authorization_code")
     *
     * @see https://github.com/thephpleague/oauth2-client#authorization-code-grant
     */
    public function connectAuthorizationCode(): Response {
        // the scopes you want to access
        $scopes  = ['photos.read', 'account.profile', 'openid', 'offline', 'offline_access'];
        $options = [];
        var_dump($scopes);
        // will redirect to Hydra!
        return $this->client->redirect($scopes, $options);
    }

    /**
     * Link to this controller to start the "connect" process
     *
     * @Route("/test-connect/authorization-code-pkce", name="connect_hydra_authorization_code_pkce")
     */
    public function connectAuthorizationCodePKCE(): Response {
        // the scopes you want to access
        $scopes  = ['photos.read', 'account.profile', 'openid', 'offline', 'offline_access'];
        $options = [];

        // will redirect to Hydra!
        return $this->client->redirect($scopes, $options);
    }


    /**
     * After going to Facebook, you're redirected back here
     * because this is the "redirect_route" you configured
     * in config/packages/knpu_oauth2_client.yaml
     *
     * @Route("/test-connect/hydra/check", name="connect_hydra_check", schemes={"https"})
     *
     * @return Response
     */
    public function connectCheckAction(): Response {
        /** @var \KnpU\OAuth2ClientBundle\Client\Provider\FacebookClient $client */
        $client = $this->client;

        try {
            $accessToken = $client->getAccessToken();
            $user        = $client->fetchUserFromToken($accessToken);

            return new JsonResponse(['token' => $accessToken, 'user' => $user]);
        } catch (IdentityProviderException $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }
}