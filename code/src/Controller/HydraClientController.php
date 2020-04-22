<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

class HydraClientController extends AbstractController {

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
        $this->client = $clientRegistry->getClient('ory_hydra'); // key used in config/packages/knpu_oauth2_client.yaml
    }


    /**
     * Link to this controller to start the "connect" process
     *
     * @Route("/connect/hydra", name="connect_hydra_start")
     */
    public function connectAction(): Response {
        // the scopes you want to access
        $scopes  = ['openid', 'offline', 'photos.read',];
        $options = [];

        // will redirect to Hydra!
        return $this->client->redirect($scopes, $options);
    }

    /**
     * After going to Facebook, you're redirected back here
     * because this is the "redirect_route" you configured
     * in config/packages/knpu_oauth2_client.yaml
     *
     * @Route("/connect/hydra/check", name="connect_hydra_check", schemes={"https"})
     *
     * @return Response
     */
    public function connectCheckAction(): Response {
        /** @var \KnpU\OAuth2ClientBundle\Client\Provider\FacebookClient $client */
        $client = $this->client;

        try {
            $accessToken = $client->getAccessToken();

//            return new JsonResponse($accessToken);
//            dump($accessToken);
//
            $user = $client->fetchUserFromToken($accessToken);
            dump($user);

            return new JsonResponse(['token' => $accessToken, 'user' => $user]);
        } catch (IdentityProviderException $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }
}